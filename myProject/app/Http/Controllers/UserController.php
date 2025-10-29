<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function show(Post $post)
    {
        if ($post->status !== 'approved') {
            abort(404, 'Post not found or not approved.');
        }
        
        $post->load([
            'category',
            'user'
        ]);
        
        $comments = $post->topLevelComments()
            ->with(['user', 'replies.user'])
            ->orderBy('created_at', 'desc')
            ->paginate(5); 
        
        return view('user.post', compact('post', 'comments'));
    }

    public function submit()
    {
        $categories = \App\Models\Category::all();
        return view('user.submit-post', compact('categories'));
    }

    public function store(Request $request, ImageService $imageService)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'author' => 'nullable|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:3072',
            'featured_image_alt' => 'nullable|string|max:255',
            'featured_image_greyscale' => 'nullable|boolean',
            'gallery_images' => 'nullable|array|max:10',
            'gallery_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:3072',
            'gallery_images_greyscale' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $postData = [
            'title' => $request->title,
            'content' => $request->input('content'),
            'author' => $request->author,
            'category_id' => $request->category_id,
            'status' => 'pending',
            'user_id' => auth()->id()
        ];

        if ($request->hasFile('featured_image')) {
            try {
                $imageOptions = array_merge(
                    config('images.featured_image'),
                    ['greyscale' => $request->boolean('featured_image_greyscale')]
                );
                $postData['featured_image'] = $imageService->uploadImage(
                    $request->file('featured_image'),
                    $imageOptions
                );
                $postData['featured_image_alt'] = $request->featured_image_alt;
            } catch (\Exception $e) {
                return redirect()->back()
                    ->withErrors(['featured_image' => 'Failed to upload featured image: ' . $e->getMessage()])
                    ->withInput();
            }
        }

        if ($request->hasFile('gallery_images')) {
            try {
                $galleryOptions = array_merge(
                    config('images.gallery_image'),
                    ['greyscale' => $request->boolean('gallery_images_greyscale')]
                );
                $galleryPaths = $imageService->uploadMultipleImages(
                    $request->file('gallery_images'),
                    $galleryOptions
                );
                $postData['gallery_images'] = $galleryPaths;
            } catch (\Exception $e) {
                return redirect()->back()
                    ->withErrors(['gallery_images' => 'Failed to upload gallery images: ' . $e->getMessage()])
                    ->withInput();
            }
        }

        Post::create($postData);

        return redirect()->route('user.submit')
            ->with('success', 'Post submitted successfully! It will be reviewed by an admin.');
    }

    public function index()
    {
        $posts = Post::where('status', 'approved')
            ->with(['user', 'category'])
            ->orderBy('created_at', 'desc')
            ->paginate(4); 
        return view('user.dashboard', compact('posts'));
    }
    public function edit(Post $post)
    {
        if ($post->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }
        $categories = \App\Models\Category::all();
        return view('user.edit-post', compact('post', 'categories'));
    }
    public function update(Request $request, Post $post, ImageService $imageService)
    {
        if ($post->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|min:10',
            'author' => 'nullable|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:3072',
            'featured_image_alt' => 'nullable|string|max:255',
            'featured_image_greyscale' => 'nullable|boolean',
            'gallery_images' => 'nullable|array|max:10',
            'gallery_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:3072',
            'gallery_images_greyscale' => 'nullable|boolean',
            'remove_featured_image' => 'nullable|boolean',
            'remove_gallery_images' => 'nullable|array'
        ]);

        $updateData = [
            'title' => $request->title,
            'content' => $request->input('content'),
            'author' => $request->author,
            'category_id' => $request->category_id,
            'status' => 'pending',
        ];

        if ($request->remove_featured_image && $post->featured_image) {
            $imageService->deleteImage($post->featured_image);
            $updateData['featured_image'] = null;
            $updateData['featured_image_alt'] = null;
        }

        if ($request->hasFile('featured_image')) {
            if ($post->featured_image) {
                $imageService->deleteImage($post->featured_image);
            }
            
            try {
                $imageOptions = array_merge(
                    config('images.featured_image'),
                    ['greyscale' => $request->boolean('featured_image_greyscale')]
                );
                $updateData['featured_image'] = $imageService->uploadImage(
                    $request->file('featured_image'),
                    $imageOptions
                );
                $updateData['featured_image_alt'] = $request->featured_image_alt;
            } catch (\Exception $e) {
                return redirect()->back()
                    ->withErrors(['featured_image' => 'Failed to upload featured image: ' . $e->getMessage()])
                    ->withInput();
            }
        } elseif ($request->featured_image_alt && !$request->remove_featured_image) {
            $updateData['featured_image_alt'] = $request->featured_image_alt;
        }

        if ($request->remove_gallery_images && $post->gallery_images) {
            $currentGallery = $post->gallery_images ?? [];
            $toRemove = $request->remove_gallery_images;
            
            foreach ($toRemove as $imageToRemove) {
                if (in_array($imageToRemove, $currentGallery)) {
                    $imageService->deleteImage($imageToRemove);
                    $currentGallery = array_values(array_diff($currentGallery, [$imageToRemove]));
                }
            }
            
            $updateData['gallery_images'] = $currentGallery;
        }

        if ($request->hasFile('gallery_images')) {
            try {
                $galleryOptions = array_merge(
                    config('images.gallery_image'),
                    ['greyscale' => $request->boolean('gallery_images_greyscale')]
                );
                $newGalleryPaths = $imageService->uploadMultipleImages(
                    $request->file('gallery_images'),
                    $galleryOptions
                );
                
                $currentGallery = $updateData['gallery_images'] ?? $post->gallery_images ?? [];
                $updateData['gallery_images'] = array_merge($currentGallery, $newGalleryPaths);
            } catch (\Exception $e) {
                return redirect()->back()
                    ->withErrors(['gallery_images' => 'Failed to upload gallery images: ' . $e->getMessage()])
                    ->withInput();
            }
        }

        $post->fill($updateData);
        $post->save();

        return redirect()->route('user.dashboard')
            ->with('success', 'Post updated successfully! It will be reviewed by an admin.');
    }

    public function destroy(Post $post, ImageService $imageService)
    {
        if ($post->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        if ($post->featured_image) {
            $imageService->deleteImage($post->featured_image);
        }
        
        if ($post->gallery_images) {
            $imageService->deleteMultipleImages($post->gallery_images);
        }

        $post->delete();

        return redirect()->route('user.dashboard')
            ->with('success', 'Post deleted successfully!');
    }
}