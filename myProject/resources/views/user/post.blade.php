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
        </div>
    </div>
</div>
@endsection
