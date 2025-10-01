<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, Post $post)
    {
        $validated = $request->validate([
            'content' => 'required|string|min:3|max:1000',
            'parent_id' => 'nullable|exists:comments,id'
        ]);

        $comment = Comment::create([
            'content' => $validated['content'],
            'user_id' => auth()->id(),
            'post_id' => $post->id,
            'parent_id' => $validated['parent_id'] ?? null
        ]);

        return redirect()->back()
            ->with('success', 'Comment added successfully!');
    }
    public function update(Request $request, Comment $comment)
    {
        if (auth()->id() !== $comment->user_id && (!auth()->user() || !auth()->user()->isAdmin())) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'content' => 'required|string|min:3|max:1000'
        ]);

        $comment->update([
            'content' => $validated['content']
        ]);

        return redirect()->back()
            ->with('success', 'Comment updated successfully!');
    }
    public function destroy(Comment $comment)
    {
        if (auth()->id() !== $comment->user_id && (!auth()->user() || !auth()->user()->isAdmin())) {
            abort(403, 'Unauthorized action.');
        }

        $comment->delete();

        return redirect()->back()
            ->with('success', 'Comment deleted successfully!');
    }
    public function restore($id)
    {
        $comment = Comment::onlyTrashed()->findOrFail($id);
        if (!auth()->user() || !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $comment->restore();

        return redirect()->back()
            ->with('success', 'Comment restored successfully!');
    }

    public function forceDelete($id)
    {
        $comment = Comment::onlyTrashed()->findOrFail($id);
        
        if (!auth()->user() || !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $comment->forceDelete();

        return redirect()->back()
            ->with('success', 'Comment permanently deleted!');
    }
}
