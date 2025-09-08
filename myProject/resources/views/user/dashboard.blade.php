@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
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
                                        <h5 class="card-title">{{ $post->title }}</h5>
                                        <p class="card-text post-content">
                                            {{ Str::limit($post->content, 120) }}
                                        </p>
                                        <div class="mt-auto">
                                            @if($post->author)
                                                <p class="text-muted mb-2">
                                                    <i class="bi bi-person me-1"></i>
                                                    By {{ $post->author }}
                                                </p>
                                            @endif
                                            <p class="text-muted small mb-3">
                                                <i class="bi bi-calendar me-1"></i>
                                                {{ $post->created_at->format('M d, Y') }}
                                            </p>
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
                        {{ $posts->links() }}
                    </div>
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
@endsection