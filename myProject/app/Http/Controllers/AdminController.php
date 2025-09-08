<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public function index()
    {
        $posts = Post::with('user')->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.list-post', compact('posts'));
    }

    public function create()
    {
        return view('admin.add-post');
    }

    public function store(Request $request)
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

        Post::create([
            'title' => $request->title,
            'content' => $request->input('content'),
            'author' => $request->author,
            'status' => $request->status,
            'user_id' => auth()->id()
        ]);

        return redirect()->route('admin.posts.index')
            ->with('success', 'Post created successfully!');
    }

    public function edit(Post $post)
    {
        return view('admin.edit-post', compact('post'));
    }

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
            'title' => $request->title,
            'content' => $request->input('content'),
            'author' => $request->author,
            'status' => $request->status
        ]);

        return redirect()->route('admin.posts.index')
            ->with('success', 'Post updated successfully!');
    }

    public function show(Post $post)
    {
        return view('admin.show-post', compact('post'));
    }
}
