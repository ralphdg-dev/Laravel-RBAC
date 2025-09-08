@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="bi bi-kanban me-2"></i>Manage Posts</h2>
                    <a href="{{ route('admin.posts.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>Add New Post
                    </a>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Author</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($posts as $post)
                                        <tr>
                                            <td>
                                                <div class="fw-semibold">{{ $post->title }}</div>
                                                <div class="text-muted small">{{ Str::limit($post->content, 50) }}</div>
                                            </td>
                                            <td>{{ $post->author }}</td>
                                            <td>
                                                <span
                                                    class="badge bg-{{ $post->status === 'approved' ? 'success' : ($post->status === 'pending' ? 'warning' : 'danger') }}">
                                                    {{ ucfirst($post->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $post->created_at->format('M d, Y') }}</td>
                                            <td>
                                                <div class="btn-group">
                                                    @if($post->status === 'pending')
                                                        <form action="{{ route('admin.posts.update', $post) }}" method="POST"
                                                            class="d-inline">
                                                            @csrf
                                                            @method('PUT')
                                                            <input type="hidden" name="status" value="approved">
                                                            <button type="submit" class="btn btn-sm btn-success me-1"
                                                                title="Approve">
                                                                <i class="bi bi-check-circle"></i>
                                                            </button>
                                                        </form>
                                                        <form action="{{ route('admin.posts.update', $post) }}" method="POST"
                                                            class="d-inline">
                                                            @csrf
                                                            @method('PUT')
                                                            <input type="hidden" name="status" value="rejected">
                                                            <button type="submit" class="btn btn-sm btn-danger me-1" title="Reject">
                                                                <i class="bi bi-x-circle"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                    <a href="{{ route('admin.posts.edit', $post) }}"
                                                        class="btn btn-sm btn-primary me-1" title="Edit">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <form action="{{ route('admin.posts.destroy', $post) }}" method="POST"
                                                        class="d-inline delete-form">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete"
                                                            onclick="return confirm('Are you sure you want to delete this post?')">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4">
                                                <i class="bi bi-inbox display-4 text-muted d-block"></i>
                                                <p class="text-muted mt-2">No posts found</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($posts->hasPages())
                            <div class="d-flex justify-content-center mt-4">
                                {{ $posts->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection