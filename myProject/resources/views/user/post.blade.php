@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="mb-4">
                    <a href="{{ route('user.dashboard') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Back to Dashboard
                    </a>
                </div>

                <article class="card">
                    <div class="card-body">
                        <header class="mb-4">
                            <h1 class="display-6 mb-3">{{ $post->title }}</h1>

                            <div class="d-flex flex-wrap align-items-center text-muted mb-3">
                                @if($post->category)
                                    <span class="me-4">
                                        <i class="bi bi-tag me-1"></i>
                                        <span class="badge bg-info">{{ $post->category->name }}</span>
                                    </span>
                                @endif
                                @if($post->author)
                                    <span class="me-4">
                                        <i class="bi bi-person me-1"></i>
                                        {{ $post->author }}
                                    </span>
                                @endif
                                <span class="me-4">
                                    <i class="bi bi-calendar me-1"></i>
                                    {{ $post->created_at->format('F d, Y') }}
                                </span>
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle me-1"></i>
                                    {{ ucfirst($post->status) }}
                                </span>
                            </div>
                        </header>

                        <div class="post-content">
                            {!! nl2br(e($post->content)) !!}
                        </div>
                    </div>

                    <div class="card-footer bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                Last updated {{ $post->updated_at->format('M d, Y \a\t g:i A') }}
                            </small>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-heart"></i> Like
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-share"></i> Share
                                </button>
                            </div>
                        </div>
                    </div>
                </article>

                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-chat-dots me-2"></i>
                            Comments ({{ $post->comments->count() }})
                        </h5>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif
                        @auth
                            <form action="{{ route('comments.store', $post) }}" method="POST" class="mb-4">
                                @csrf
                                <div class="mb-3">
                                    <label for="content" class="form-label">Add a comment</label>
                                    <textarea class="form-control @error('content') is-invalid @enderror" 
                                              id="content" name="content" rows="3" 
                                              placeholder="Share your thoughts...">{{ old('content') }}</textarea>
                                    @error('content')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-send me-1"></i>Post Comment
                                </button>
                            </form>
                        @else
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                Please <a href="{{ route('login') }}">login</a> to leave a comment.
                            </div>
                        @endauth

                        @if($comments->count() > 0)
                            <div class="comments-list">
                                @foreach($comments as $comment)
                                    <div class="comment mb-4" id="comment-{{ $comment->id }}">
                                        <div class="d-flex">
                                            <div class="flex-shrink-0">
                                                <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                    {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                                                </div>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <div class="comment-header d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <h6 class="mb-1">{{ $comment->user->name }}</h6>
                                                        <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                                                    </div>
                                                    @auth
                                                        @if(auth()->id() === $comment->user_id || auth()->user()->isAdmin())
                                                            <div class="dropdown">
                                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                                    <i class="bi bi-three-dots"></i>
                                                                </button>
                                                                <ul class="dropdown-menu">
                                                                    <li>
                                                                        <button class="dropdown-item" onclick="editComment({{ $comment->id }})">
                                                                            <i class="bi bi-pencil me-2"></i>Edit
                                                                        </button>
                                                                    </li>
                                                                    <li>
                                                                        <form action="{{ route('comments.destroy', $comment) }}" method="POST" class="d-inline" 
                                                                              onsubmit="return confirm('Are you sure you want to delete this comment?')">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <button type="submit" class="dropdown-item text-danger">
                                                                                <i class="bi bi-trash me-2"></i>Delete
                                                                            </button>
                                                                        </form>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        @endif
                                                    @endauth
                                                </div>
                                                <div class="comment-content" id="comment-content-{{ $comment->id }}">
                                                    <p class="mb-2">{{ $comment->content }}</p>
                                                </div>
                                                <div class="edit-comment-form d-none" id="edit-form-{{ $comment->id }}">
                                                    <form action="{{ route('comments.update', $comment) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="mb-2">
                                                            <textarea class="form-control" name="content" rows="2">{{ $comment->content }}</textarea>
                                                        </div>
                                                        <div class="btn-group btn-group-sm">
                                                            <button type="submit" class="btn btn-success">
                                                                <i class="bi bi-check me-1"></i>Save
                                                            </button>
                                                            <button type="button" class="btn btn-secondary" onclick="cancelEdit({{ $comment->id }})">
                                                                <i class="bi bi-x me-1"></i>Cancel
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>

                                                @auth
                                                    <button class="btn btn-sm btn-outline-primary mt-2" onclick="toggleReplyForm({{ $comment->id }})">
                                                        <i class="bi bi-reply me-1"></i>Reply
                                                    </button>
                                                @endauth
                                                @auth
                                                    <div class="reply-form mt-3 d-none" id="reply-form-{{ $comment->id }}">
                                                        <form action="{{ route('comments.store', $post) }}" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                                            <div class="mb-2">
                                                                <textarea class="form-control" name="content" rows="2" placeholder="Write a reply..."></textarea>
                                                            </div>
                                                            <div class="btn-group btn-group-sm">
                                                                <button type="submit" class="btn btn-primary">
                                                                    <i class="bi bi-send me-1"></i>Reply
                                                                </button>
                                                                <button type="button" class="btn btn-secondary" onclick="toggleReplyForm({{ $comment->id }})">
                                                                    <i class="bi bi-x me-1"></i>Cancel
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                @endauth
                                                @if($comment->replies->count() > 0)
                                                    <div class="replies mt-3 ms-4">
                                                        @foreach($comment->replies as $reply)
                                                            <div class="reply mb-3" id="comment-{{ $reply->id }}">
                                                                <div class="d-flex">
                                                                    <div class="flex-shrink-0">
                                                                        <div class="avatar bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                                                            {{ strtoupper(substr($reply->user->name, 0, 1)) }}
                                                                        </div>
                                                                    </div>
                                                                    <div class="flex-grow-1 ms-2">
                                                                        <div class="reply-header d-flex justify-content-between align-items-start">
                                                                            <div>
                                                                                <h6 class="mb-1 fs-6">{{ $reply->user->name }}</h6>
                                                                                <small class="text-muted">{{ $reply->created_at->diffForHumans() }}</small>
                                                                            </div>
                                                                            @auth
                                                                                @if(auth()->id() === $reply->user_id || auth()->user()->isAdmin())
                                                                                    <div class="dropdown">
                                                                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                                                            <i class="bi bi-three-dots"></i>
                                                                                        </button>
                                                                                        <ul class="dropdown-menu">
                                                                                            <li>
                                                                                                <button class="dropdown-item" onclick="editComment({{ $reply->id }})">
                                                                                                    <i class="bi bi-pencil me-2"></i>Edit
                                                                                                </button>
                                                                                            </li>
                                                                                            <li>
                                                                                                <form action="{{ route('comments.destroy', $reply) }}" method="POST" class="d-inline" 
                                                                                                      onsubmit="return confirm('Are you sure you want to delete this reply?')">
                                                                                                    @csrf
                                                                                                    @method('DELETE')
                                                                                                    <button type="submit" class="dropdown-item text-danger">
                                                                                                        <i class="bi bi-trash me-2"></i>Delete
                                                                                                    </button>
                                                                                                </form>
                                                                                            </li>
                                                                                        </ul>
                                                                                    </div>
                                                                                @endif
                                                                            @endauth
                                                                        </div>
                                                                        <div class="reply-content" id="comment-content-{{ $reply->id }}">
                                                                            <p class="mb-1">{{ $reply->content }}</p>
                                                                        </div>
                                                                        
                                                                        <div class="edit-comment-form d-none" id="edit-form-{{ $reply->id }}">
                                                                            <form action="{{ route('comments.update', $reply) }}" method="POST">
                                                                                @csrf
                                                                                @method('PUT')
                                                                                <div class="mb-2">
                                                                                    <textarea class="form-control" name="content" rows="2">{{ $reply->content }}</textarea>
                                                                                </div>
                                                                                <div class="btn-group btn-group-sm">
                                                                                    <button type="submit" class="btn btn-success">
                                                                                        <i class="bi bi-check me-1"></i>Save
                                                                                    </button>
                                                                                    <button type="button" class="btn btn-secondary" onclick="cancelEdit({{ $reply->id }})">
                                                                                        <i class="bi bi-x me-1"></i>Cancel
                                                                                    </button>
                                                                                </div>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            

                            <div class="d-flex justify-content-center mt-4">
                                {{ $comments->appends(request()->query())->links('pagination::bootstrap-4', ['class' => 'pagination-sm']) }}
                            </div>
                            

                            @if($comments->hasPages())
                                <div class="text-center mt-2">
                                    <small class="text-muted">
                                        Showing {{ $comments->firstItem() }} to {{ $comments->lastItem() }} of {{ $comments->total() }} comments
                                    </small>
                                </div>
                            @endif
                        @else
                            <div class="text-center py-4">
                                <i class="bi bi-chat display-4 text-muted"></i>
                                <p class="text-muted mt-2">No comments yet. Be the first to share your thoughts!</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>


    <style>
        .pagination-sm .page-link {
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
        }
        .pagination .page-link {
            border-radius: 0.375rem !important;
            margin: 0 2px;
            border: 1px solid #dee2e6;
        }
        .pagination .page-item.active .page-link {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }
        .pagination .page-link:hover {
            background-color: #e9ecef;
            border-color: #adb5bd;
        }
    </style>

    <!-- JavaScript for Comment Interactions -->
    <script>
        function editComment(commentId) {
            document.getElementById('comment-content-' + commentId).classList.add('d-none');
            document.getElementById('edit-form-' + commentId).classList.remove('d-none');
        }

        function cancelEdit(commentId) {
            document.getElementById('comment-content-' + commentId).classList.remove('d-none');
            document.getElementById('edit-form-' + commentId).classList.add('d-none');
        }

        function toggleReplyForm(commentId) {
            const replyForm = document.getElementById('reply-form-' + commentId);
            replyForm.classList.toggle('d-none');
        }
    </script>
@endsection