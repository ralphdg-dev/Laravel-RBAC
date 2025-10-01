<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::with(['user', 'category']);
        
        if ($request->get('show') === 'trashed') {
            $query->onlyTrashed();
        }
        
        $posts = $query->orderBy('created_at', 'desc')->paginate(10);
        $trashedCount = Post::onlyTrashed()->count();
        
        if ($request->route()->getName() === 'admin.dashboard') {
            return view('admin.dashboard', compact('posts', 'trashedCount'));
        }
        
        return view('admin.list-post', compact('posts', 'trashedCount'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.add-post', compact('categories'));
    }

    public function store(StorePostRequest $request)
    {
        $validated = $request->validated();
        $validated['user_id'] = auth()->id();
        
        Post::create($validated);

        return redirect()->route('admin.posts.index')
            ->with('success', 'Post created successfully!');
    }

    public function edit(Post $post)
    {
        $categories = Category::all();
        return view('admin.edit-post', compact('post', 'categories'));
    }

    public function update(UpdatePostRequest $request, Post $post)
    {
        $post->update($request->validated());

        return redirect()->route('admin.posts.index')
            ->with('success', 'Post updated successfully!');
    }

    public function show(Post $post)
    {
        $post->load(['user', 'category', 'comments.user']);
        return view('admin.show-post', compact('post'));
    }

    public function destroy(Post $post)
    {
        $post->delete();
        
        return redirect()->route('admin.posts.index')
            ->with('success', 'Post moved to trash successfully!');
    }

    public function restore($id)
    {
        $post = Post::onlyTrashed()->findOrFail($id);
        $post->restore();
        
        return redirect()->route('admin.posts.index')
            ->with('success', 'Post restored successfully!');
    }

    public function forceDelete($id)
    {
        $post = Post::onlyTrashed()->findOrFail($id);
        $post->forceDelete();
        
        return redirect()->route('admin.posts.index', ['show' => 'trashed'])
            ->with('success', 'Post permanently deleted!');
    }
}
