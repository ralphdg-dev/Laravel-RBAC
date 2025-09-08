<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    /**
     * Display a listing of posts
     */
    public function index()
    {
        $posts = Post::with('user')->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.list-post', compact('posts'));
    }

    /**
     * Show the form for creating a new post
     */
    public function create()
    {
        return view('admin.add-post');
    }

    /**
     * Store a newly created post in storage
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'author' => 'nullable|string|max:255',
            'status' => 'required|in:pending,approved,rejected'
        ]);

        $validated['user_id'] = auth()->id();

        $post = Post::create($validated);

        return redirect()->route('admin.posts.index')
            ->with('success', 'Post created successfully');
    }

    /**
     * Display the specified post
     */
    public function show(Post $post)
    {
        return view('admin.show-post', compact('post'));
    }

    /**
     * Show the form for editing the specified post
     */
    public function edit(Post $post)
    {
        return view('admin.edit-post', compact('post'));
    }

    /**
     * Update the specified post in storage
     */
    public function update(Request $request, Post $post)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'author' => 'nullable|string|max:255',
            'status' => 'required|in:pending,approved,rejected'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $post->update([
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'author' => $request->input('author'),
            'status' => $request->input('status')
        ]);

        return redirect()->route('admin.posts.index')
            ->with('success', 'Post updated successfully!');
    }

    /**
     * Remove the specified post from storage
     */
    public function destroy(Post $post)
    {
        $post->delete();

        return redirect()->route('admin.posts.index')
            ->with('success', 'Post deleted successfully!');
    }
}