@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">

            <div class="col-md-9">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="d-flex align-items-center">
                        <a href="{{ route('admin.posts.index') }}" class="btn btn-outline-secondary me-3">
                            <i class="bi bi-arrow-left"></i>
                        </a>
                        <h2 class="mb-0">
                            <i class="bi bi-eye me-2"></i>View Post
                        </h2>
                    </div>
                    <div class="btn-group" role="group">
                        <a href="{{ route('admin.posts.edit', $post) }}" class="btn btn-primary">
                            <i class="bi bi-pencil me-1"></i>Edit
                        </a>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="mb-0">{{ $post->title }}</h3>
                            @if($post->status === 'approved')
                                <span class="badge bg-success fs-6">Approved</span>
                            @elseif($post->status === 'rejected')
                                <span class="badge bg-danger fs-6">Rejected</span>
                            @else
                                <span class="badge bg-warning fs-6">Pending</span>
                            @endif
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6 class="text-muted">Author</h6>
                                <p>{{ $post->author ?: 'Anonymous' }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted">Submitted By</h6>
                                <p>{{ $post->user->name }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted">Created Date</h6>
                                <p>{{ $post->created_at->format('F d, Y \a\t g:i A') }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted">Last Updated</h6>
                                <p>{{ $post->updated_at->format('F d, Y \a\t g:i A') }}</p>
                            </div>
                        </div>

                        <h6 class="text-muted">Content</h6>
                        <div class="post-content border-start border-3 border-primary ps-4">
                            {!! nl2br(e($post->content)) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection