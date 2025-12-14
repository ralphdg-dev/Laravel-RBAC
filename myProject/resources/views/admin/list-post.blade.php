@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">

            <div class="col-md-9">
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
                    <div>
                        <h2 class="mb-0">
                            <i class="bi bi-list-ul me-2"></i>All Posts
                        </h2>
                        <div class="mt-2">
                            <a href="{{ route('admin.posts.index') }}" 
                               class="btn btn-sm {{ !request('show') ? 'btn-primary' : 'btn-outline-primary' }}">
                                Active Posts
                            </a>
                            <a href="{{ route('admin.posts.index', ['show' => 'trashed']) }}" 
                               class="btn btn-sm {{ request('show') === 'trashed' ? 'btn-danger' : 'btn-outline-danger' }}">
                                Trash ({{ $trashedCount }})
                            </a>
                        </div>
                    </div>
                    <a href="{{ route('admin.posts.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>Add New Post
                    </a>
                </div>

                <div class="card">
                    <div class="card-body">
                        @if($posts->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="80">Image</th>
                                            <th>Title</th>
                                            <th>Category</th>
                                            <th>Author</th>
                                            <th>Status</th>
                                            <th>Created By</th>
                                            <th>Created</th>
                                            <th width="120">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($posts as $post)
                                            <tr>
                                                <td>
                                                    @if($post->featured_image)
                                                        <img src="{{ $post->featured_image_url }}" 
                                                             alt="{{ $post->featured_image_alt ?? $post->title }}" 
                                                             class="img-thumbnail" 
                                                             style="width: 60px; height: 60px; object-fit: cover;">
                                                    @elseif($post->hasImages())
                                                        <div class="d-flex align-items-center justify-content-center bg-light rounded" 
                                                             style="width: 60px; height: 60px;">
                                                            <i class="bi bi-images text-muted"></i>
                                                        </div>
                                                    @else
                                                        <div class="d-flex align-items-center justify-content-center bg-light rounded" 
                                                             style="width: 60px; height: 60px;">
                                                            <i class="bi bi-image text-muted"></i>
                                                        </div>
                                                    @endif
                                                </td>
                                                <td>
                                                    <strong>
                                                        {{ $post->title }}
                                                        @if($post->hasImages())
                                                            <i class="bi bi-camera-fill text-primary ms-1" title="Has images"></i>
                                                        @endif
                                                    </strong>
                                                    <br>
                                                    <small class="text-muted">
                                                        {{ Str::limit($post->content, 50) }}
                                                    </small>
                                                </td>
                                                <td>
                                                    @if($post->category)
                                                        <span class="badge bg-info">{{ $post->category->name }}</span>
                                                    @else
                                                        <span class="text-muted">Uncategorized</span>
                                                    @endif
                                                </td>
                                                <td>{{ $post->author ?: 'N/A' }}</td>
                                                <td>
                                                    @if($post->status === 'approved')
                                                        <span class="badge bg-success status-badge">Approved</span>
                                                    @elseif($post->status === 'rejected')
                                                        <span class="badge bg-danger status-badge">Rejected</span>
                                                    @else
                                                        <span class="badge bg-warning status-badge">Pending</span>
                                                    @endif
                                                </td>
                                                <td>{{ $post->user->name }}</td>
                                                <td>{{ $post->created_at->format('M d, Y') }}</td>
                                                <td>
                                                    @if($post->trashed())
                                                        {{-- Actions for trashed posts --}}
                                                        <div class="dropdown">
                                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                                <i class="bi bi-three-dots"></i>
                                                            </button>
                                                            <ul class="dropdown-menu">
                                                                <li>
                                                                    <form action="{{ route('admin.posts.restore', $post->id) }}" method="POST" class="d-inline">
                                                                        @csrf
                                                                        <button type="submit" class="dropdown-item text-success">
                                                                            <i class="bi bi-arrow-clockwise me-2"></i>Restore
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                                <li><hr class="dropdown-divider"></li>
                                                                <li>
                                                                    <form action="{{ route('admin.posts.force-delete', $post->id) }}" method="POST" class="d-inline">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="button" class="dropdown-item text-danger btn-force-delete" data-post-title="{{ $post->title }}">
                                                                            <i class="bi bi-trash3 me-2"></i>Delete Permanently
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    @else
                                                        {{-- Actions for active posts --}}
                                                        <div class="dropdown">
                                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                                <i class="bi bi-three-dots"></i>
                                                            </button>
                                                            <ul class="dropdown-menu">
                                                                <li>
                                                                    <a class="dropdown-item" href="{{ route('admin.posts.show', $post) }}">
                                                                        <i class="bi bi-eye me-2"></i>View
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a class="dropdown-item" href="{{ route('admin.posts.edit', $post) }}">
                                                                        <i class="bi bi-pencil me-2"></i>Edit
                                                                    </a>
                                                                </li>
                                                                <li><hr class="dropdown-divider"></li>
                                                                <li>
                                                                    <form action="{{ route('admin.posts.destroy', $post) }}" method="POST" class="d-inline">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="button" class="dropdown-item text-warning btn-soft-delete" data-post-title="{{ $post->title }}">
                                                                            <i class="bi bi-trash me-2"></i>Move to Trash
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex justify-content-center">
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
                            <div class="text-center py-5">
                                <i class="bi bi-inbox display-1 text-muted"></i>
                                <h4 class="text-muted mt-3">No posts found</h4>
                                <p class="text-muted">Start by creating your first post.</p>
                                <a href="{{ route('admin.posts.create') }}" class="btn btn-primary">
                                    <i class="bi bi-plus-circle me-1"></i>Create Post
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="softDeleteModal" tabindex="-1" aria-labelledby="softDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning-subtle">
                    <h5 class="modal-title" id="softDeleteModalLabel">
                        <i class="bi bi-trash me-2"></i>Move Post to Trash
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0 soft-delete-modal-text">Are you sure you want to move this post to trash?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-warning btn-sm" id="softDeleteConfirmBtn">
                        <i class="bi bi-trash me-1"></i>Yes, move to trash
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="forceDeleteModal" tabindex="-1" aria-labelledby="forceDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger-subtle">
                    <h5 class="modal-title" id="forceDeleteModalLabel">
                        <i class="bi bi-trash3 me-2"></i>Permanently Delete Post
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0 force-delete-modal-text">Are you sure you want to permanently delete this post? This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger btn-sm" id="forceDeleteConfirmBtn">
                        <i class="bi bi-trash3 me-1"></i>Yes, delete permanently
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var formToSubmit = null;

                var softDeleteModalEl = document.getElementById('softDeleteModal');
                if (softDeleteModalEl) {
                    var softConfirmBtn = softDeleteModalEl.querySelector('#softDeleteConfirmBtn');
                    var softBodyText = softDeleteModalEl.querySelector('.soft-delete-modal-text');
                    document.querySelectorAll('.btn-soft-delete').forEach(function (btn) {
                        btn.addEventListener('click', function (e) {
                            e.preventDefault();
                            formToSubmit = btn.closest('form');
                            var title = btn.getAttribute('data-post-title');
                            if (title && softBodyText) {
                                softBodyText.textContent = 'Are you sure you want to move "' + title + '" to trash?';
                            } else if (softBodyText) {
                                softBodyText.textContent = 'Are you sure you want to move this post to trash?';
                            }
                            var modal = new bootstrap.Modal(softDeleteModalEl);
                            modal.show();
                        });
                    });
                    if (softConfirmBtn) {
                        softConfirmBtn.addEventListener('click', function () {
                            if (formToSubmit) {
                                formToSubmit.submit();
                            }
                        });
                    }
                }

                var forceDeleteModalEl = document.getElementById('forceDeleteModal');
                if (forceDeleteModalEl) {
                    var forceConfirmBtn = forceDeleteModalEl.querySelector('#forceDeleteConfirmBtn');
                    var forceBodyText = forceDeleteModalEl.querySelector('.force-delete-modal-text');
                    document.querySelectorAll('.btn-force-delete').forEach(function (btn) {
                        btn.addEventListener('click', function (e) {
                            e.preventDefault();
                            formToSubmit = btn.closest('form');
                            var title = btn.getAttribute('data-post-title');
                            if (title && forceBodyText) {
                                forceBodyText.textContent = 'Are you sure you want to permanently delete "' + title + '"? This action cannot be undone.';
                            } else if (forceBodyText) {
                                forceBodyText.textContent = 'Are you sure you want to permanently delete this post? This action cannot be undone.';
                            }
                            var modal = new bootstrap.Modal(forceDeleteModalEl);
                            modal.show();
                        });
                    });
                    if (forceConfirmBtn) {
                        forceConfirmBtn.addEventListener('click', function () {
                            if (formToSubmit) {
                                formToSubmit.submit();
                            }
                        });
                    }
                }
            });
        </script>
    @endpush
@endsection