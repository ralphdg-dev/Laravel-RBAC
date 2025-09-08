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
                    <h2 class="mb-0">
                        <i class="bi bi-list-ul me-2"></i>All Posts
                    </h2>
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
                                            <th>Title</th>
                                            <th>Author</th>
                                            <th>Status</th>
                                            <th>Created By</th>
                                            <th>Created</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($posts as $post)
                                            <tr>
                                                <td>
                                                    <strong>{{ $post->title }}</strong>
                                                    <br>
                                                    <small class="text-muted">
                                                        {{ Str::limit($post->content, 50) }}
                                                    </small>
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
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <a href="{{ route('admin.posts.show', $post) }}"
                                                            class="btn btn-outline-info">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        <a href="{{ route('admin.posts.edit', $post) }}"
                                                            class="btn btn-outline-primary">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex justify-content-center">
                                {{ $posts->links() }}
                            </div>
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
@endsection