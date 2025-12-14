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
                                    @if($post->featured_image)
                                        <img src="{{ $post->featured_image_url }}" alt="{{ $post->featured_image_alt ?: $post->title }}"
                                            class="card-img-top" style="height: 200px; object-fit: cover;">
                                    @endif
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h5 class="card-title mb-0">{{ $post->title }}</h5>
                                            <div class="d-flex align-items-center">
                                                @if($post->category)
                                                    <span class="badge bg-info me-2">{{ $post->category->name }}</span>
                                                @endif
                                                @if($post->user_id === auth()->id())
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                                                            data-bs-toggle="dropdown">
                                                            <i class="bi bi-three-dots"></i>
                                                        </button>
                                                        <ul class="dropdown-menu">
                                                            <li>
                                                                <a class="dropdown-item" href="{{ route('user.posts.edit', $post) }}">
                                                                    <i class="bi bi-pencil me-2"></i>Edit
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <hr class="dropdown-divider">
                                                            </li>
                                                            <li>
                                                                <form action="{{ route('user.posts.destroy', $post) }}" method="POST"
                                                                    class="d-inline">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="button"
                                                                        class="dropdown-item text-danger btn-user-delete"
                                                                        data-post-title="{{ $post->title }}">
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
                                        @if($post->hasImages())
                                            <div class="mb-2">
                                                @if($post->featured_image)
                                                    <span class="badge bg-secondary me-1">
                                                        <i class="bi bi-image me-1"></i>Featured Image
                                                    </span>
                                                @endif
                                                @if($post->gallery_images && count($post->gallery_images) > 0)
                                                    <span class="badge bg-secondary">
                                                        <i class="bi bi-images me-1"></i>{{ count($post->gallery_images) }} Gallery Images
                                                    </span>
                                                @endif
                                            </div>
                                        @endif
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
                    <div class="d-flex justify-content-center mt-4">
                        {{ $posts->appends(request()->query())->links('pagination::bootstrap-4', ['class' => 'pagination-sm']) }}
                    </div>
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

    <div class="modal fade" id="userDeleteModal" tabindex="-1" aria-labelledby="userDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger-subtle">
                    <h5 class="modal-title" id="userDeleteModalLabel">
                        <i class="bi bi-trash me-2"></i>Delete Post
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0 user-delete-modal-text">Are you sure you want to delete this post?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger btn-sm" id="userDeleteConfirmBtn">
                        <i class="bi bi-trash me-1"></i>Yes, delete post
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var deleteModal = document.getElementById('userDeleteModal');
                if (!deleteModal) {
                    return;
                }
                var formToSubmit = null;
                var confirmBtn = deleteModal.querySelector('#userDeleteConfirmBtn');
                var bodyText = deleteModal.querySelector('.user-delete-modal-text');
                document.querySelectorAll('.btn-user-delete').forEach(function (btn) {
                    btn.addEventListener('click', function (e) {
                        e.preventDefault();
                        formToSubmit = btn.closest('form');
                        var title = btn.getAttribute('data-post-title');
                        if (title && bodyText) {
                            bodyText.textContent = 'Are you sure you want to delete "' + title + '"? This action cannot be undone.';
                        } else if (bodyText) {
                            bodyText.textContent = 'Are you sure you want to delete this post? This action cannot be undone.';
                        }
                        var modal = new bootstrap.Modal(deleteModal);
                        modal.show();
                    });
                });
                if (confirmBtn) {
                    confirmBtn.addEventListener('click', function () {
                        if (formToSubmit) {
                            formToSubmit.submit();
                        }
                    });
                }
            });
        </script>
    @endpush

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