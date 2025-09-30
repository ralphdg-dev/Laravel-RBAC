@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
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

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="mb-0">
                        <i class="bi bi-newspaper me-2"></i>Latest Posts
                    </h2>
                    <a href="{{ route('user.submit') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>Submit Post
                    </a>
                </div>

                @if($posts->count() > 0)
                    <div class="row row-cols-1 row-cols-md-2 g-4">
                        @foreach($posts as $post)
                            <div class="col">
                                <div class="card h-100 shadow-sm">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h5 class="card-title mb-0">{{ $post->title }}</h5>
                                            <div class="d-flex align-items-center">
                                                @if($post->category)
                                                    <span class="badge bg-info me-2">{{ $post->category->name }}</span>
                                                @endif
                                                @if($post->user_id === auth()->id())
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                            <i class="bi bi-three-dots"></i>
                                                        </button>
                                                        <ul class="dropdown-menu">
                                                            <li>
                                                                <a class="dropdown-item" href="{{ route('user.posts.edit', $post) }}">
                                                                    <i class="bi bi-pencil me-2"></i>Edit
                                                                </a>
                                                            </li>
                                                            <li><hr class="dropdown-divider"></li>
                                                            <li>
                                                                <form action="{{ route('user.posts.destroy', $post) }}" method="POST" class="d-inline" 
                                                                      onsubmit="return confirm('Are you sure you want to delete this post?')">
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
                                            </div>
                                        </div>
                                        <p class="card-text post-content">
                                            {{ Str::limit($post->content, 120) }}
                                        </p>
                                        <div class="mt-auto">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <div>
                                                    @if($post->author)
                                                        <p class="text-muted mb-1 small">
                                                            <i class="bi bi-person me-1"></i>
                                                            By {{ $post->author }}
                                                        </p>
                                                    @endif
                                                    <p class="text-muted small mb-0">
                                                        <i class="bi bi-calendar me-1"></i>
                                                        {{ $post->created_at->format('M d, Y') }}
                                                    </p>
                                                </div>
                                                <div class="text-end">
                                                    <small class="text-muted">
                                                        <i class="bi bi-person-check me-1"></i>
                                                        {{ $post->user->name }}
                                                    </small>
                                                </div>
                                            </div>
                                            <a href="{{ route('user.posts.show', $post) }}" class="btn btn-outline-primary w-100">
                                                <i class="bi bi-eye me-1"></i>Read More
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $posts->appends(request()->query())->links('pagination::bootstrap-4', ['class' => 'pagination-sm']) }}
                    </div>
                    
                    <!-- Pagination Info -->
                    @if($posts->hasPages())
                        <div class="text-center mt-2">
                            <small class="text-muted">
                                Showing {{ $posts->firstItem() }} to {{ $posts->lastItem() }} of {{ $posts->total() }} posts
                            </small>
                        </div>
                    @endif
                @else
                    <div class="card shadow-sm">
                        <div class="card-body text-center py-5">
                            <i class="bi bi-inbox display-1 text-muted"></i>
                            <h4 class="text-muted mt-3">No posts available</h4>
                            <p class="text-muted">Be the first to submit a post!</p>
                            <a href="{{ route('user.submit') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-1"></i>Submit Post
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Custom Pagination Styling -->
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
@endsection