<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function show(Post $post)
    {
        if ($post->status !== 'approved') {
            abort(404, 'Post not found or not approved.');
        }
        
        // Load relationships for the post
        $post->load([
            'category',
            'user'
        ]);
        
        // Paginate comments separately for better performance
        $comments = $post->topLevelComments()
            ->with(['user', 'replies.user'])
            ->orderBy('created_at', 'desc')
            ->paginate(5); // 5 comments per page
        
        return view('user.post', compact('post', 'comments'));
    }

    public function submit()
    {
        $categories = \App\Models\Category::all();
        return view('user.submit-post', compact('categories'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'author' => 'nullable|string|max:255',
            'category_id' => 'nullable|exists:categories,id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Post::create([
            'title' => $request->title,
            'content' => $request->input('content'),
            'author' => $request->author,
            'category_id' => $request->category_id,
            'status' => 'pending',
            'user_id' => auth()->id()
        ]);

        return redirect()->route('user.submit')
            ->with('success', 'Post submitted successfully! It will be reviewed by an admin.');
    }

    public function index()
    {
        $posts = Post::where('status', 'approved')
            ->with(['user', 'category'])
            ->orderBy('created_at', 'desc')
            ->paginate(4); // 4 posts per page
        return view('user.dashboard', compact('posts'));
    }

    public function edit(Post $post)
    {
        // Check if user owns this post
        if ($post->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $categories = \App\Models\Category::all();
        return view('user.edit-post', compact('post', 'categories'));
    }

    public function update(Request $request, Post $post)
    {
        // Check if user owns this post
        if ($post->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|min:10',
            'author' => 'nullable|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $post->fill([
            'title' => $request->title,
            'content' => $request->input('content'),
            'author' => $request->author,
            'category_id' => $request->category_id,
            'status' => 'pending', // Reset to pending when user edits
        ]);
        $post->save();

        return redirect()->route('user.dashboard')
            ->with('success', 'Post updated successfully! It will be reviewed by an admin.');
    }

    public function destroy(Post $post)
    {
        // Check if user owns this post
        if ($post->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $post->delete();

        return redirect()->route('user.dashboard')
            ->with('success', 'Post deleted successfully!');
    }
}