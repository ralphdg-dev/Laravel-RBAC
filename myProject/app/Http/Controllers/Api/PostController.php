<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class PostController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Display a listing of approved posts (public)
     */
    public function index(Request $request)
    {
        $query = Post::where('status', 'approved')
            ->with(['user', 'category'])
            ->withCount('comments');

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%");
            });
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $posts = $query->paginate($request->get('per_page', 15));

        return PostResource::collection($posts);
    }

    /**
     * Display the specified post (public)
     */
    public function show(Post $post)
    {
        if ($post->status !== 'approved') {
            return response()->json(['message' => 'Post not found.'], 404);
        }

        $post->load(['user', 'category', 'comments.user', 'comments.replies.user']);

        return new PostResource($post);
    }

    /**
     * Display user's posts
     */
    public function userPosts(Request $request)
    {
        $query = Auth::user()->posts()
            ->with(['category'])
            ->withCount('comments');

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Include trashed posts if requested
        if ($request->get('include_trashed')) {
            $query->withTrashed();
        }

        $posts = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return PostResource::collection($posts);
    }

    /**
     * Display user's specific post
     */
    public function userShow(Post $post)
    {
        if ($post->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $post->load(['category', 'comments.user', 'comments.replies.user']);

        return new PostResource($post);
    }

    /**
     * Store a newly created post (user)
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'author' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:3072',
            'featured_image_alt' => 'nullable|string|max:255',
            'featured_image_greyscale' => 'boolean',
            'gallery_images' => 'nullable|array|max:10',
            'gallery_images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:3072',
            'gallery_images_greyscale' => 'boolean',
        ]);

        $postData = [
            'title' => $request->get('title'),
            'content' => $request->get('content'),
            'author' => $request->get('author'),
            'category_id' => $request->get('category_id'),
            'user_id' => Auth::id(),
            'status' => 'pending',
        ];

        // Handle featured image
        if ($request->hasFile('featured_image')) {
            $imageOptions = ['greyscale' => $request->boolean('featured_image_greyscale')];
            $featuredImagePath = $this->imageService->uploadImage(
                $request->file('featured_image'),
                $imageOptions
            );
            $postData['featured_image'] = $featuredImagePath;
            $postData['featured_image_alt'] = $request->featured_image_alt;
        }

        // Handle gallery images
        if ($request->hasFile('gallery_images')) {
            $imageOptions = ['greyscale' => $request->boolean('gallery_images_greyscale')];
            $galleryPaths = $this->imageService->uploadMultipleImages(
                $request->file('gallery_images'),
                $imageOptions
            );
            $postData['gallery_images'] = $galleryPaths;
        }

        $post = Post::create($postData);
        $post->load(['user', 'category']);

        return new PostResource($post);
    }

    /**
     * Update the specified post (user)
     */
    public function update(Request $request, Post $post)
    {
        if ($post->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'author' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:3072',
            'featured_image_alt' => 'nullable|string|max:255',
            'featured_image_greyscale' => 'boolean',
            'gallery_images' => 'nullable|array|max:10',
            'gallery_images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:3072',
            'gallery_images_greyscale' => 'boolean',
            'remove_featured_image' => 'boolean',
            'remove_gallery_images' => 'boolean',
        ]);

        $postData = [
            'title' => $request->get('title'),
            'content' => $request->get('content'),
            'author' => $request->get('author'),
            'category_id' => $request->get('category_id'),
        ];

        // Handle featured image removal
        if ($request->boolean('remove_featured_image')) {
            if ($post->featured_image) {
                $this->imageService->deleteImage($post->featured_image);
            }
            $postData['featured_image'] = null;
            $postData['featured_image_alt'] = null;
        }

        // Handle new featured image
        if ($request->hasFile('featured_image')) {
            if ($post->featured_image) {
                $this->imageService->deleteImage($post->featured_image);
            }
            $imageOptions = ['greyscale' => $request->boolean('featured_image_greyscale')];
            $featuredImagePath = $this->imageService->uploadImage(
                $request->file('featured_image'),
                $imageOptions
            );
            $postData['featured_image'] = $featuredImagePath;
            $postData['featured_image_alt'] = $request->featured_image_alt;
        }

        // Handle gallery images removal
        if ($request->boolean('remove_gallery_images')) {
            if ($post->gallery_images) {
                foreach ($post->gallery_images as $imagePath) {
                    $this->imageService->deleteImage($imagePath);
                }
            }
            $postData['gallery_images'] = null;
        }

        // Handle new gallery images
        if ($request->hasFile('gallery_images')) {
            if ($post->gallery_images && !$request->boolean('remove_gallery_images')) {
                foreach ($post->gallery_images as $imagePath) {
                    $this->imageService->deleteImage($imagePath);
                }
            }
            $imageOptions = ['greyscale' => $request->boolean('gallery_images_greyscale')];
            $galleryPaths = $this->imageService->uploadMultipleImages(
                $request->file('gallery_images'),
                $imageOptions
            );
            $postData['gallery_images'] = $galleryPaths;
        }

        $post->update($postData);
        $post->load(['user', 'category']);

        return new PostResource($post);
    }

    /**
     * Remove the specified post (user - soft delete)
     */
    public function destroy(Post $post)
    {
        if ($post->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $post->delete();

        return response()->json(['message' => 'Post deleted successfully.']);
    }

    // Admin methods

    /**
     * Display all posts for admin
     */
    public function adminIndex(Request $request)
    {
        $query = Post::with(['user', 'category'])
            ->withCount('comments');

        // Include trashed posts if requested
        if ($request->get('include_trashed')) {
            $query->withTrashed();
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by user
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $posts = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return PostResource::collection($posts);
    }

    /**
     * Store a newly created post (admin)
     */
    public function adminStore(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'author' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'user_id' => 'required|exists:users,id',
            'status' => ['required', Rule::in(['pending', 'approved', 'rejected'])],
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:3072',
            'featured_image_alt' => 'nullable|string|max:255',
            'featured_image_greyscale' => 'boolean',
            'gallery_images' => 'nullable|array|max:10',
            'gallery_images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:3072',
            'gallery_images_greyscale' => 'boolean',
        ]);

        $postData = $request->only(['title', 'content', 'author', 'category_id', 'user_id', 'status']);

        // Handle images (same logic as user store)
        if ($request->hasFile('featured_image')) {
            $imageOptions = ['greyscale' => $request->boolean('featured_image_greyscale')];
            $featuredImagePath = $this->imageService->uploadImage(
                $request->file('featured_image'),
                $imageOptions
            );
            $postData['featured_image'] = $featuredImagePath;
            $postData['featured_image_alt'] = $request->featured_image_alt;
        }

        if ($request->hasFile('gallery_images')) {
            $imageOptions = ['greyscale' => $request->boolean('gallery_images_greyscale')];
            $galleryPaths = $this->imageService->uploadMultipleImages(
                $request->file('gallery_images'),
                $imageOptions
            );
            $postData['gallery_images'] = $galleryPaths;
        }

        $post = Post::create($postData);
        $post->load(['user', 'category']);

        return new PostResource($post);
    }

    /**
     * Display the specified post (admin)
     */
    public function adminShow($id)
    {
        $post = Post::withTrashed()->with(['user', 'category', 'comments.user'])->findOrFail($id);

        return new PostResource($post);
    }

    /**
     * Update the specified post (admin)
     */
    public function adminUpdate(Request $request, Post $post)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'author' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'user_id' => 'required|exists:users,id',
            'status' => ['required', Rule::in(['pending', 'approved', 'rejected'])],
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:3072',
            'featured_image_alt' => 'nullable|string|max:255',
            'featured_image_greyscale' => 'boolean',
            'gallery_images' => 'nullable|array|max:10',
            'gallery_images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:3072',
            'gallery_images_greyscale' => 'boolean',
            'remove_featured_image' => 'boolean',
            'remove_gallery_images' => 'boolean',
        ]);

        $postData = $request->only(['title', 'content', 'author', 'category_id', 'user_id', 'status']);

        // Handle image updates (same logic as user update)
        if ($request->boolean('remove_featured_image')) {
            if ($post->featured_image) {
                $this->imageService->deleteImage($post->featured_image);
            }
            $postData['featured_image'] = null;
            $postData['featured_image_alt'] = null;
        }

        if ($request->hasFile('featured_image')) {
            if ($post->featured_image) {
                $this->imageService->deleteImage($post->featured_image);
            }
            $imageOptions = ['greyscale' => $request->boolean('featured_image_greyscale')];
            $featuredImagePath = $this->imageService->uploadImage(
                $request->file('featured_image'),
                $imageOptions
            );
            $postData['featured_image'] = $featuredImagePath;
            $postData['featured_image_alt'] = $request->featured_image_alt;
        }

        if ($request->boolean('remove_gallery_images')) {
            if ($post->gallery_images) {
                foreach ($post->gallery_images as $imagePath) {
                    $this->imageService->deleteImage($imagePath);
                }
            }
            $postData['gallery_images'] = null;
        }

        if ($request->hasFile('gallery_images')) {
            if ($post->gallery_images && !$request->boolean('remove_gallery_images')) {
                foreach ($post->gallery_images as $imagePath) {
                    $this->imageService->deleteImage($imagePath);
                }
            }
            $imageOptions = ['greyscale' => $request->boolean('gallery_images_greyscale')];
            $galleryPaths = $this->imageService->uploadMultipleImages(
                $request->file('gallery_images'),
                $imageOptions
            );
            $postData['gallery_images'] = $galleryPaths;
        }

        $post->update($postData);
        $post->load(['user', 'category']);

        return new PostResource($post);
    }

    /**
     * Update post status (admin)
     */
    public function updateStatus(Request $request, Post $post)
    {
        $request->validate([
            'status' => ['required', Rule::in(['pending', 'approved', 'rejected'])],
        ]);

        $post->update(['status' => $request->status]);

        return new PostResource($post);
    }

    /**
     * Remove the specified post (admin - soft delete)
     */
    public function adminDestroy(Post $post)
    {
        $post->delete();

        return response()->json(['message' => 'Post deleted successfully.']);
    }

    /**
     * Restore the specified post (admin)
     */
    public function restore($id)
    {
        $post = Post::withTrashed()->findOrFail($id);
        $post->restore();

        return response()->json(['message' => 'Post restored successfully.']);
    }

    /**
     * Permanently delete the specified post (admin)
     */
    public function forceDelete($id)
    {
        $post = Post::withTrashed()->findOrFail($id);

        // Delete associated images
        if ($post->featured_image) {
            $this->imageService->deleteImage($post->featured_image);
        }

        if ($post->gallery_images) {
            foreach ($post->gallery_images as $imagePath) {
                $this->imageService->deleteImage($imagePath);
            }
        }

        $post->forceDelete();

        return response()->json(['message' => 'Post permanently deleted.']);
    }
}
