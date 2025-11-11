<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * Store a newly created comment
     */
    public function store(Request $request, Post $post)
    {
        // Check if post is approved and accessible
        if ($post->status !== 'approved' && $post->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            return response()->json(['message' => 'Post not found.'], 404);
        }

        $request->validate([
            'content' => 'required|string',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        // If parent_id is provided, ensure it belongs to the same post
        if ($request->parent_id) {
            $parentComment = Comment::find($request->parent_id);
            if (!$parentComment || $parentComment->post_id !== $post->id) {
                return response()->json(['message' => 'Invalid parent comment.'], 422);
            }
        }

        $comment = Comment::create([
            'content' => $request->get('content'),
            'post_id' => $post->id,
            'user_id' => Auth::id(),
            'parent_id' => $request->get('parent_id'),
        ]);

        $comment->load(['user', 'replies.user']);

        return new CommentResource($comment);
    }

    /**
     * Update the specified comment
     */
    public function update(Request $request, Comment $comment)
    {
        // Check if user owns the comment or is admin
        if ($comment->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $request->validate([
            'content' => 'required|string',
        ]);

        $comment->update(['content' => $request->get('content')]);
        $comment->load(['user', 'replies.user']);

        return new CommentResource($comment);
    }

    /**
     * Remove the specified comment (soft delete)
     */
    public function destroy(Comment $comment)
    {
        // Check if user owns the comment or is admin
        if ($comment->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $comment->delete();

        return response()->json(['message' => 'Comment deleted successfully.']);
    }

    /**
     * Display all comments for admin
     */
    public function adminIndex(Request $request)
    {
        $query = Comment::with(['user', 'post'])
            ->withCount('replies');

        // Include trashed comments if requested
        if ($request->get('include_trashed')) {
            $query->withTrashed();
        }

        // Filter by post
        if ($request->has('post_id')) {
            $query->where('post_id', $request->post_id);
        }

        // Filter by user
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Only top-level comments or all
        if ($request->get('top_level_only')) {
            $query->whereNull('parent_id');
        }

        $comments = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return CommentResource::collection($comments);
    }

    /**
     * Restore the specified comment (admin only)
     */
    public function restore($id)
    {
        $comment = Comment::withTrashed()->findOrFail($id);
        $comment->restore();

        return response()->json(['message' => 'Comment restored successfully.']);
    }

    /**
     * Permanently delete the specified comment (admin only)
     */
    public function forceDelete($id)
    {
        $comment = Comment::withTrashed()->findOrFail($id);
        
        // Also permanently delete all replies
        $comment->replies()->withTrashed()->forceDelete();
        
        $comment->forceDelete();

        return response()->json(['message' => 'Comment permanently deleted.']);
    }
}
