<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Store a newly created comment.
     */
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

    /**
     * Update the specified comment.
     */
    public function update(Request $request, Comment $comment)
    {
        // Check if user can edit this comment
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

    /**
     * Soft delete the specified comment.
     */
    public function destroy(Comment $comment)
    {
        // Check if user can delete this comment
        if (auth()->id() !== $comment->user_id && (!auth()->user() || !auth()->user()->isAdmin())) {
            abort(403, 'Unauthorized action.');
        }

        $comment->delete();

        return redirect()->back()
            ->with('success', 'Comment deleted successfully!');
    }

    /**
     * Restore a soft deleted comment.
     */
    public function restore($id)
    {
        $comment = Comment::onlyTrashed()->findOrFail($id);
        
        // Only admins can restore comments
        if (!auth()->user() || !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $comment->restore();

        return redirect()->back()
            ->with('success', 'Comment restored successfully!');
    }

    /**
     * Permanently delete a comment.
     */
    public function forceDelete($id)
    {
        $comment = Comment::onlyTrashed()->findOrFail($id);
        
        // Only admins can permanently delete comments
        if (!auth()->user() || !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $comment->forceDelete();

        return redirect()->back()
            ->with('success', 'Comment permanently deleted!');
    }
}
