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
        return view('user.post', compact('post'));
    }

    public function submit()
    {
        return view('user.submit-post');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'author' => 'nullable|string|max:255'
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
            'status' => 'pending',
            'user_id' => auth()->id()
        ]);

        return redirect()->route('user.submit')
            ->with('success', 'Post submitted successfully! It will be reviewed by an admin.');
    }

    public function index()
    {
        $posts = Post::where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->paginate(12);
        return view('user.dashboard', compact('posts'));
    }
}