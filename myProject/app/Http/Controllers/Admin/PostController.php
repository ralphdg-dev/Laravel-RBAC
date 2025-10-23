<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{

    public function index(Request $request)
    {
        $query = Post::with(['user', 'category']);
        
        if ($request->get('show') === 'trashed') {
            $query->onlyTrashed();
        }
        
        $posts = $query->orderBy('created_at', 'desc')->paginate(10);
        $trashedCount = Post::onlyTrashed()->count();
        
        return view('admin.list-post', compact('posts', 'trashedCount'));
    }


    public function create()
    {
        $categories = \App\Models\Category::all();
        return view('admin.add-post', compact('categories'));
    }


    public function store(Request $request, ImageService $imageService)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255', 'min:3'],
            'content' => ['required', 'string', 'min:10'],
            'author' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:pending,approved,rejected'],
            'category_id' => 'nullable|exists:categories,id',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'featured_image_alt' => 'nullable|string|max:255',
            'gallery_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120'
        ]);

        $validated['user_id'] = auth()->id();

        if ($request->hasFile('featured_image')) {
            try {
                $validated['featured_image'] = $imageService->uploadImage(
                    $request->file('featured_image'),
                    config('images.featured_image')
                );
                $validated['featured_image_alt'] = $request->featured_image_alt;
            } catch (\Exception $e) {
                return redirect()->back()
                    ->withErrors(['featured_image' => 'Failed to upload featured image: ' . $e->getMessage()])
                    ->withInput();
            }
        }

        if ($request->hasFile('gallery_images')) {
            try {
                $validated['gallery_images'] = $imageService->uploadMultipleImages(
                    $request->file('gallery_images'),
                    config('images.gallery_image')
                );
            } catch (\Exception $e) {
                return redirect()->back()
                    ->withErrors(['gallery_images' => 'Failed to upload gallery images: ' . $e->getMessage()])
                    ->withInput();
            }
        }

        $post = Post::create($validated);

        return redirect()->route('admin.posts.index')
            ->with('success', 'Post created successfully');
    }


    public function show(Post $post)
    {
        return view('admin.show-post', compact('post'));
    }


    public function edit(Post $post)
    {
        $categories = \App\Models\Category::all();
        return view('admin.edit-post', compact('post', 'categories'));
    }


    public function update(Request $request, Post $post, ImageService $imageService)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255', 'min:3'],
            'content' => ['required', 'string', 'min:10'],
            'author' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:pending,approved,rejected'],
            'category_id' => 'nullable|exists:categories,id',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'featured_image_alt' => 'nullable|string|max:255',
            'gallery_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'remove_featured_image' => 'nullable|boolean',
            'remove_gallery_images' => 'nullable|array'
        ]);

        $updateData = [
            'title' => $validated['title'],
            'content' => $validated['content'],
            'author' => $validated['author'],
            'status' => $validated['status'],
            'category_id' => $validated['category_id']
        ];

        // Handle featured image removal
        if ($request->remove_featured_image && $post->featured_image) {
            try {
                $imageService->deleteImage($post->featured_image);
                $updateData['featured_image'] = null;
                $updateData['featured_image_alt'] = null;
            } catch (\Exception $e) {
                \Log::error('Failed to delete featured image', ['error' => $e->getMessage()]);
            }
        }

        // Handle new featured image upload
        if ($request->hasFile('featured_image')) {
            // Delete old featured image if exists
            if ($post->featured_image) {
                try {
                    $imageService->deleteImage($post->featured_image);
                } catch (\Exception $e) {
                    \Log::error('Failed to delete old featured image', ['error' => $e->getMessage()]);
                }
            }
            
            try {
                $updateData['featured_image'] = $imageService->uploadImage(
                    $request->file('featured_image'),
                    config('images.featured_image')
                );
                $updateData['featured_image_alt'] = $validated['featured_image_alt'] ?? null;
            } catch (\Exception $e) {
                \Log::error('Failed to upload featured image', ['error' => $e->getMessage()]);
                return redirect()->back()
                    ->withErrors(['featured_image' => 'Failed to upload featured image: ' . $e->getMessage()])
                    ->withInput();
            }
        } elseif (isset($validated['featured_image_alt']) && !$request->remove_featured_image) {
            $updateData['featured_image_alt'] = $validated['featured_image_alt'];
        }

        // Handle gallery image removal
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

        // Handle new gallery images upload
        if ($request->hasFile('gallery_images')) {
            try {
                $newGalleryPaths = $imageService->uploadMultipleImages(
                    $request->file('gallery_images'),
                    config('images.gallery_image')
                );
                
                $currentGallery = $updateData['gallery_images'] ?? $post->gallery_images ?? [];
                $updateData['gallery_images'] = array_merge($currentGallery, $newGalleryPaths);
            } catch (\Exception $e) {
                return redirect()->back()
                    ->withErrors(['gallery_images' => 'Failed to upload gallery images: ' . $e->getMessage()])
                    ->withInput();
            }
        }

        $post->update($updateData);

        return redirect()->route('admin.posts.index')
            ->with('success', 'Post updated successfully!');
    }

    public function updateStatus(Request $request, Post $post)
    {
        $request->validate([
            'status' => ['required', 'in:pending,approved,rejected']
        ]);

        $post->update(['status' => $request->status]);

        return redirect()->back()
            ->with('success', 'Post status updated successfully!');
    }

    public function destroy(Post $post, ImageService $imageService)
    {
        // Note: Since we're using soft deletes, we don't delete images here
        // Images will be deleted only on force delete
        $post->delete();

        return redirect()->route('admin.posts.index')
            ->with('success', 'Post moved to trash successfully!');
    }

    public function forceDelete($id, ImageService $imageService)
    {
        $post = Post::onlyTrashed()->findOrFail($id);
        
        if ($post->featured_image) {
            $imageService->deleteImage($post->featured_image);
        }
        
        if ($post->gallery_images) {
            $imageService->deleteMultipleImages($post->gallery_images);
        }

        $post->forceDelete();

        return redirect()->route('admin.posts.index')
            ->with('success', 'Post permanently deleted!');
    }

    public function restore($id)
    {
        $post = Post::onlyTrashed()->findOrFail($id);
        $post->restore();

        return redirect()->route('admin.posts.index')
            ->with('success', 'Post restored successfully!');
    }
}