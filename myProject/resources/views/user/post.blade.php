@extends('layouts.app')

@section('content')
    <!-- Enhanced Modern CSS -->
    <style>
        :root {
            --card-bg: #ffffff;
            --card-border: #e1e5e9;
            --text-primary: #1c1e21;
            --text-secondary: #65676b;
            --text-muted: #8a8d91;
            --primary-color: #1877f2;
            --success-color: #42b883;
            --background: #f8f9fa;
            --shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            --shadow-hover: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        body {
            background: var(--background);
            min-height: 100vh;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            padding: 0;
        }
        
        .post-card {
            background: var(--card-bg);
            border-radius: 8px;
            margin-bottom: 24px;
            max-width: 1400px;
            width: 100%;
            box-shadow: var(--shadow);
            border: 1px solid var(--card-border);
            overflow: hidden;
        }

        .post-card:hover {
            box-shadow: var(--shadow-hover);
            transition: box-shadow 0.2s ease;
        }
        
        
        .post-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 16px;
            margin-right: 12px;
        }
        
        .post-time {
            color: var(--text-muted);
            font-size: 13px;
            font-weight: 400;
        }
        
        .enhanced-post-meta a {
            color: var(--text-secondary);
            text-decoration: none;
            transition: color 0.2s ease;
        }
        
        .enhanced-post-meta a:hover {
            color: var(--primary-color);
        }
        
        .post-username {
            color: var(--text-primary);
            font-weight: 600;
            font-size: 15px;
            text-decoration: none;
            margin-bottom: 2px;
        }
        
        .post-username:hover {
            color: var(--primary-color);
            text-decoration: none;
        }

        .reading-time {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            margin-left: 8px;
        }
        
        .post-card {
            padding: 24px;
        }

        .post-title {
            font-size: 20px;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 12px;
            line-height: 1.3;
        }

        .post-text {
            color: var(--text-secondary);
            font-size: 15px;
            line-height: 1.5;
            margin-bottom: 16px;
        }
        
        .enhanced-post-title {
            font-size: 28px;
            font-weight: 800;
            color: var(--text-primary);
            margin-bottom: 16px;
            line-height: 36px;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .enhanced-post-text {
            font-size: 16px;
            line-height: 28px;
            color: var(--text-primary);
            margin-bottom: 20px;
        }

        .enhanced-post-text p {
            margin-bottom: 16px;
        }
        
        .enhanced-category-badge {
            background: var(--gradient-success);
            color: white;
            padding: 8px 16px;
            border-radius: 25px;
            font-size: 13px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 16px;
            box-shadow: var(--shadow-light);
            transition: all 0.3s ease;
        }

        .enhanced-category-badge:hover {
            transform: translateY(-1px);
            box-shadow: var(--shadow-medium);
        }
        
        .post-image {
            width: 100%;
            height: auto;
            max-height: 400px;
            object-fit: cover;
            cursor: pointer;
            display: block;
        }

        .image-container {
            position: relative;
            overflow: hidden;
            border-radius: 12px;
            margin: 16px 0;
        }

        .image-loading {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: var(--text-secondary);
        }

        .image-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(0,0,0,0.1) 0%, transparent 50%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .image-container:hover .image-overlay {
            opacity: 1;
        }
        
        .post-carousel {
            position: relative;
            width: 100%;
            height: 400px;
            overflow: hidden;
            background: var(--background);
        }
        
        .post-carousel-container {
            display: flex;
            width: 100%;
            height: 100%;
            transition: transform 0.3s ease;
            flex-wrap: nowrap;
        }
        
        .post-carousel-slide {
            min-width: 100%;
            max-width: 100%;
            height: 100%;
            position: relative;
            flex-shrink: 0;
        }
        
        .post-carousel-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        
        .post-carousel-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255, 255, 255, 0.9);
            color: var(--text-secondary);
            border: none;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: 10;
            box-shadow: var(--shadow);
        }
        
        .post-carousel:hover .post-carousel-nav {
            opacity: 1;
        }
        
        .post-carousel-nav:hover {
            background: white;
        }
        
        .post-carousel-nav.prev {
            left: 12px;
        }
        
        .post-carousel-nav.next {
            right: 12px;
        }
        
        .fb-carousel-indicators {
            position: absolute;
            bottom: 12px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 8px;
            z-index: 10;
        }
        
        .fb-carousel-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.6);
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid rgba(255, 255, 255, 0.8);
        }
        
        .fb-carousel-dot.active {
            background: white;
            transform: scale(1.2);
        }
        
        .fb-carousel-counter {
            position: absolute;
            top: 12px;
            right: 12px;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 6px 10px;
            border-radius: 16px;
            font-size: 13px;
            font-weight: 600;
            z-index: 10;
        }
        
        .fb-engagement {
            padding: 12px 20px;
            border-top: 1px solid var(--fb-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 15px;
            color: var(--fb-gray);
            background: rgba(0, 0, 0, 0.01);
        }
        
        .fb-reactions {
            display: flex;
            align-items: center;
            gap: 4px;
        }
        
        .fb-reaction-icons {
            display: flex;
            margin-right: 8px;
        }
        
        .fb-reaction-icon {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            margin-left: -2px;
            border: 2px solid white;
        }
        
        .fb-like-icon {
            background: var(--fb-like);
            color: white;
            font-size: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .fb-love-icon {
            background: var(--fb-love);
            color: white;
            font-size: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .fb-actions {
            padding: 12px 20px;
            border-top: 1px solid var(--fb-border);
            display: flex;
            justify-content: space-around;
            background: rgba(0, 0, 0, 0.01);
        }
        
        .fb-action-btn {
            flex: 1;
            padding: 10px 16px;
            border: none;
            background: none;
            color: var(--fb-gray);
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            border-radius: 8px;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            position: relative;
        }
        
        .fb-action-btn:hover {
            background-color: var(--fb-hover);
            transform: translateY(-1px);
        }
        
        .fb-action-btn.liked {
            color: var(--fb-like);
            background-color: rgba(24, 119, 242, 0.1);
        }
        
        .fb-action-btn.shared {
            color: var(--fb-care);
            background-color: rgba(247, 177, 37, 0.1);
        }

        .enhanced-action-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
            transition: left 0.5s ease;
        }
        
        .enhanced-action-btn:hover {
            background: rgba(255, 255, 255, 0.8);
            transform: translateY(-2px);
            box-shadow: var(--shadow-light);
        }

        .enhanced-action-btn:hover::before {
            left: 100%;
        }
        
        .enhanced-action-btn.liked {
            color: #e74c3c;
            background: rgba(231, 76, 60, 0.1);
        }
        
        .enhanced-action-btn.shared {
            color: var(--success-color);
            background: rgba(66, 184, 131, 0.1);
        }

        .action-count {
            background: var(--gradient-primary);
            color: white;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            margin-left: 4px;
        }
        
        .fb-breadcrumb {
            background: var(--fb-white);
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 16px;
            box-shadow: var(--fb-shadow);
        }
        
        .fb-breadcrumb a {
            color: var(--fb-primary);
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
        }
        
        .fb-breadcrumb a:hover {
            text-decoration: underline;
        }
        
        .fb-breadcrumb-separator {
            color: var(--fb-gray);
            margin: 0 8px;
        }
        
        .enhanced-status-badge {
            background: var(--gradient-success);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            box-shadow: var(--shadow-light);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(66, 184, 131, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(66, 184, 131, 0); }
            100% { box-shadow: 0 0 0 0 rgba(66, 184, 131, 0); }
        }

        .engagement-stats {
            display: flex;
            gap: 16px;
            margin-top: 12px;
            padding: 12px 0;
            border-top: 1px solid var(--border-color);
        }

        .stat-item {
            display: flex;
            align-items: center;
            gap: 6px;
            color: var(--text-secondary);
            font-size: 13px;
            font-weight: 500;
        }

        .stat-number {
            color: var(--primary-color);
            font-weight: 700;
        }
        
        .floating-action {
            position: fixed;
            bottom: 24px;
            right: 24px;
            background: var(--gradient-primary);
            color: white;
            border: none;
            border-radius: 50%;
            width: 56px;
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            box-shadow: var(--shadow-heavy);
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1000;
        }

        .floating-action:hover {
            transform: scale(1.1) rotate(5deg);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.3);
        }

        .scroll-progress {
            position: fixed;
            top: 0;
            left: 0;
            width: 0%;
            height: 3px;
            background: var(--gradient-primary);
            z-index: 1001;
            transition: width 0.1s ease;
        }

        @media (max-width: 768px) {
            .container {
                padding: 0 12px;
            }
            
            .fb-post {
                border-radius: 8px;
                margin-bottom: 16px;
                max-width: 100%;
            }
            
            .fb-breadcrumb {
                border-radius: 8px;
                margin-bottom: 16px;
                padding: 12px 16px;
            }

            .fb-header {
                padding: 14px 16px;
            }

            .fb-content {
                padding: 16px 20px;
            }

            .fb-post-title {
                font-size: 16px;
                line-height: 22px;
            }

            .fb-actions {
                padding: 10px 16px;
            }

            .fb-action-btn {
                padding: 8px 12px;
                font-size: 14px;
            }

            .floating-action {
                bottom: 16px;
                right: 16px;
                width: 48px;
                height: 48px;
                font-size: 18px;
            }
        }

        @media (max-width: 480px) {
            .fb-post-title {
                font-size: 15px;
                line-height: 20px;
            }

            .fb-content {
                padding: 14px 16px;
            }

            .fb-header {
                padding: 12px 16px;
            }

            .fb-carousel {
                height: 300px;
            }
        }
    </style>

    <!-- Scroll Progress Bar -->
    <div class="scroll-progress" id="scrollProgress"></div>

    <div class="container-fluid py-3">
        <div class="row justify-content-center">
            <div class="col-12" style="max-width: 1600px;">
                <!-- Unified Post Card -->
                <div class="post-card">
                    <!-- Breadcrumb -->
                    <div class="mb-3">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('user.dashboard') }}">
                                        <i class="bi bi-house-door me-1"></i>Dashboard
                                    </a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">
                                    {{ Str::limit($post->title, 30) }}
                                </li>
                            </ol>
                        </nav>
                    </div>

                    <!-- Post Header -->
                    <div class="d-flex align-items-center mb-3">
                        <div class="post-avatar">
                            {{ strtoupper(substr($post->author ?: $post->user->name, 0, 1)) }}
                        </div>
                        <div class="flex-grow-1">
                            <div class="post-username">
                                {{ $post->author ?: $post->user->name }}
                            </div>
                            <div class="post-time">
                                {{ $post->created_at->format('F d, Y \a\t g:i A') }}
                            </div>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-sm" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('user.posts.edit', $post) }}">
                                    <i class="bi bi-pencil me-2"></i>Edit Post
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="#" onclick="deletePost()">
                                    <i class="bi bi-trash me-2"></i>Delete Post
                                </a></li>
                            </ul>
                        </div>
                    </div>

                    <!-- Main Content Row -->
                    <div class="row">
                        <!-- Post Content Column -->
                        <div class="col-lg-8 col-md-7">
                            <!-- Post Content -->
                            @if($post->category)
                                <span class="badge bg-primary mb-3">
                                    <i class="bi bi-tag me-1"></i>{{ $post->category->name }}
                                </span>
                            @endif
                            
                            <h1 class="post-title mb-3">{{ $post->title }}</h1>
                            
                            <div class="post-text mb-4">
                                {!! nl2br(e($post->content)) !!}
                            </div>

                            <!-- Post Images -->
                            @if($post->featured_image && (!$post->gallery_images || count($post->gallery_images) == 0))
                                <!-- Single Featured Image -->
                                <img src="{{ $post->featured_image_url }}" 
                                     alt="{{ $post->featured_image_alt ?: $post->title }}" 
                                     class="post-image"
                                     onclick="openImageModal('{{ $post->featured_image_url }}')">
                            @elseif($post->gallery_images && count($post->gallery_images) > 0)
                                <!-- Post Carousel for Gallery -->
                                <div class="post-carousel" id="carousel-{{ $post->id }}">
                                    <div class="post-carousel-container">
                                        @if($post->featured_image)
                                            <div class="post-carousel-slide">
                                                <img src="{{ $post->featured_image_url }}" alt="{{ $post->featured_image_alt ?: $post->title }}" class="post-carousel-img" onclick="openLightbox(-1)">
                                            </div>
                                        @endif
                                        @foreach($post->gallery_image_urls as $index => $imageUrl)
                                            <div class="post-carousel-slide">
                                                <img src="{{ $imageUrl }}" alt="Gallery Image {{ $index + 1 }}" class="post-carousel-img" onclick="openLightbox({{ $index }})">
                                            </div>
                                        @endforeach
                                    </div>
                                    
                                    @php
                                        $totalImages = count($post->gallery_images) + ($post->featured_image ? 1 : 0);
                                    @endphp
                                    
                                    @if($totalImages > 1)
                                        <!-- Navigation Arrows -->
                                        <button class="post-carousel-nav prev" onclick="prevSlide('carousel-{{ $post->id }}')">
                                            <i class="bi bi-chevron-left"></i>
                                        </button>
                                        <button class="post-carousel-nav next" onclick="nextSlide('carousel-{{ $post->id }}')">
                                            <i class="bi bi-chevron-right"></i>
                                        </button>
                                        
                                        <!-- Indicators -->
                                        <div class="fb-carousel-indicators">
                                            @if($post->featured_image)
                                                <div class="fb-carousel-dot active" onclick="goToSlide('carousel-{{ $post->id }}', 0)"></div>
                                            @endif
                                            @foreach($post->gallery_image_urls as $index => $imageUrl)
                                                <div class="fb-carousel-dot {{ !$post->featured_image && $index === 0 ? 'active' : '' }}" onclick="goToSlide('carousel-{{ $post->id }}', {{ $post->featured_image ? $index + 1 : $index }})"></div>
                                            @endforeach
                                        </div>
                                        
                                        <!-- Counter -->
                                        <div class="fb-carousel-counter">
                                            <span class="current-slide">1</span> / {{ $totalImages }}
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <!-- Post Actions -->
                            <div class="d-flex justify-content-between align-items-center mb-4 pt-3 border-top">
                                <div class="d-flex gap-3">
                                    <button class="btn btn-outline-primary btn-sm like-btn" onclick="toggleLike()">
                                        <i class="bi bi-heart me-1"></i>
                                        <span class="like-text">Like</span>
                                    </button>
                                    <button class="btn btn-outline-secondary btn-sm" onclick="scrollToComments()">
                                        <i class="bi bi-chat me-1"></i>
                                        Comment ({{ $post->comments->count() }})
                                    </button>
                                    <button class="btn btn-outline-info btn-sm share-btn" onclick="sharePost()">
                                        <i class="bi bi-share me-1"></i>
                                        Share
                                    </button>
                                </div>
                                <small class="text-muted">
                                    <span id="likeCount">{{ rand(10, 100) }}</span> likes
                                </small>
                            </div>
                        </div>
                        
                        <!-- Comments Column -->
                        <div class="col-lg-4 col-md-5">
                            <div class="border-start ps-4" id="comments-section">
                    <h5 class="mb-3">
                        <i class="bi bi-chat-dots me-2"></i>
                        Comments ({{ $post->comments->count() }})
                    </h5>
                
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
                            <div class="d-flex gap-3">
                                <div class="post-avatar" style="width: 32px; height: 32px; font-size: 14px;">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                                <div class="flex-grow-1">
                                    <textarea class="form-control @error('content') is-invalid @enderror" 
                                              name="content" rows="2" 
                                              placeholder="Write a comment..."
                                              style="border-radius: 20px; resize: none;">{{ old('content') }}</textarea>
                                    @error('content')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="mt-2">
                                        <button type="submit" class="btn btn-primary btn-sm rounded-pill">
                                            <i class="bi bi-send me-1"></i>Post
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    @else
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            Please <a href="{{ route('login') }}">login</a> to leave a comment.
                        </div>
                    @endif

                    @if($comments->count() > 0)
                        <div class="comments-list" style="max-height: 600px; overflow-y: auto;">
                            @foreach($comments as $comment)
                                <div class="comment mb-3 p-3" style="background: var(--background); border-radius: 12px; border: 1px solid var(--card-border);">
                                    <div class="d-flex gap-3">
                                        <div class="post-avatar" style="width: 32px; height: 32px; font-size: 14px;">
                                            {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <strong class="post-username">{{ $comment->user->name }}</strong>
                                                    <div class="post-time">
                                                        {{ $comment->created_at->diffForHumans() }}
                                                    </div>
                                                </div>
                                                @auth
                                                    @if(auth()->id() === $comment->user_id || auth()->user()->isAdmin())
                                                        <div class="dropdown">
                                                            <button class="btn btn-sm" type="button" data-bs-toggle="dropdown">
                                                                <i class="bi bi-three-dots"></i>
                                                            </button>
                                                            <ul class="dropdown-menu">
                                                                <li>
                                                                    <button class="dropdown-item" onclick="editComment({{ $comment->id }})">
                                                                        <i class="bi bi-pencil me-2"></i>Edit
                                                                    </button>
                                                                </li>
                                                                <li><hr class="dropdown-divider"></li>
                                                                <li>
                                                                    <form action="{{ route('comments.destroy', $comment) }}" method="POST" class="d-inline">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" class="dropdown-item text-danger" 
                                                                                onclick="return confirm('Are you sure you want to delete this comment?')">
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

                                            <div class="edit-form d-none" id="edit-form-{{ $comment->id }}">
                                                <form action="{{ route('comments.update', $comment) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <textarea class="form-control mb-2" name="content" rows="2" 
                                                              style="border-radius: 12px;">{{ $comment->content }}</textarea>
                                                    <div class="d-flex gap-2">
                                                        <button type="submit" class="btn btn-success btn-sm">
                                                            <i class="bi bi-check me-1"></i>Save
                                                        </button>
                                                        <button type="button" class="btn btn-secondary btn-sm" onclick="cancelEdit({{ $comment->id }})">
                                                            <i class="bi bi-x me-1"></i>Cancel
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>

                                            @auth
                                                <button class="btn btn-sm btn-link p-0 mt-2 text-muted" onclick="toggleReplyForm({{ $comment->id }})">
                                                    <i class="bi bi-reply me-1"></i>Reply
                                                </button>
                                            @endauth

                                            @auth
                                                <div class="reply-form mt-3 d-none" id="reply-form-{{ $comment->id }}">
                                                    <form action="{{ route('comments.store', $post) }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                                        <div class="d-flex gap-2">
                                                            <div class="post-avatar" style="width: 28px; height: 28px; font-size: 12px;">
                                                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <textarea class="form-control" name="content" rows="1" 
                                                                          placeholder="Write a reply..." style="border-radius: 16px; resize: none;"></textarea>
                                                                <div class="mt-2">
                                                                    <button type="submit" class="btn btn-primary btn-sm">Reply</button>
                                                                    <button type="button" class="btn btn-link btn-sm" onclick="toggleReplyForm({{ $comment->id }})">Cancel</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            @endauth

                                            @if($comment->replies->count() > 0)
                                                <div class="replies mt-3">
                                                    @foreach($comment->replies as $reply)
                                                        <div class="reply mb-2 ms-4">
                                                            <div class="d-flex gap-2">
                                                                <div class="post-avatar" style="width: 28px; height: 28px; font-size: 12px;">
                                                                    {{ strtoupper(substr($reply->user->name, 0, 1)) }}
                                                                </div>
                                                                <div class="flex-grow-1">
                                                                    <div class="p-2" style="background: white; border-radius: 12px;">
                                                                        <strong class="post-username" style="font-size: 13px;">{{ $reply->user->name }}</strong>
                                                                        <div style="font-size: 14px;">{{ $reply->content }}</div>
                                                                        <small class="text-muted">{{ $reply->created_at->diffForHumans() }}</small>
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
                            {{ $comments->appends(request()->query())->links('pagination::bootstrap-4') }}
                        </div>
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
        </div>
    </div>

    <!-- Image Modal -->
    <div class="modal fade" id="imageModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content bg-dark">
                <div class="modal-header border-0">
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center p-0">
                    <img id="modalImage" src="" alt="" class="img-fluid">
                </div>
            </div>
        </div>
    </div>

    <!-- Gallery Lightbox Modal -->
    @if($post->gallery_images && count($post->gallery_images) > 0)
    <div class="modal fade" id="galleryModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content bg-dark">
                <div class="modal-header border-0">
                    <h5 class="modal-title text-white">Gallery</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="galleryModalImage" src="" alt="" class="img-fluid">
                    <div class="mt-3">
                        <button type="button" class="btn btn-outline-light" onclick="previousImage()">
                            <i class="bi bi-chevron-left"></i> Previous
                        </button>
                        <span id="imageCounter" class="mx-3 text-white"></span>
                        <button type="button" class="btn btn-outline-light" onclick="nextImage()">
                            Next <i class="bi bi-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <script>
        // Simple Like functionality
        function toggleLike() {
            const btn = document.querySelector('.like-btn');
            const icon = btn.querySelector('i');
            const text = btn.querySelector('.like-text');
            const likeCount = document.getElementById('likeCount');
            
            if (btn.classList.contains('liked')) {
                btn.classList.remove('liked');
                btn.classList.remove('btn-primary');
                btn.classList.add('btn-outline-primary');
                icon.className = 'bi bi-heart me-1';
                text.textContent = 'Like';
                
                // Update count
                const currentCount = parseInt(likeCount.textContent);
                likeCount.textContent = currentCount - 1;
            } else {
                btn.classList.add('liked');
                btn.classList.remove('btn-outline-primary');
                btn.classList.add('btn-primary');
                icon.className = 'bi bi-heart-fill me-1';
                text.textContent = 'Liked';
                
                // Update count
                const currentCount = parseInt(likeCount.textContent);
                likeCount.textContent = currentCount + 1;
            }
        }

        // Bookmark functionality
        function bookmarkPost() {
            const btn = event.target.closest('.ig-action-btn');
            const icon = btn.querySelector('i');
            
            if (icon.classList.contains('bi-bookmark')) {
                icon.className = 'bi bi-bookmark-fill';
                showToast('Post saved!', 'success');
            } else {
                icon.className = 'bi bi-bookmark';
                showToast('Post removed from saved', 'info');
            }
        }

        // Toggle full content
        function toggleFullContent() {
            // This would expand the content - implement as needed
            showToast('Full content view coming soon!', 'info');
        }

        // Enhanced Share functionality
        function sharePost() {
            const shareBtn = document.querySelector('.share-btn');
            const shareCount = document.getElementById('shareCount');
            
            // Add animation
            shareBtn.style.transform = 'scale(0.95)';
            setTimeout(() => {
                shareBtn.style.transform = 'scale(1)';
            }, 150);
            
            if (navigator.share) {
                navigator.share({
                    title: '{{ $post->title }}',
                    text: '{{ Str::limit(strip_tags($post->content), 100) }}',
                    url: window.location.href
                }).then(() => {
                    // Update share count
                    const currentCount = parseInt(shareCount.textContent);
                    shareCount.textContent = currentCount + 1;
                    shareBtn.classList.add('shared');
                });
            } else {
                copyLink();
            }
        }

        // Copy Link functionality
        function copyLink() {
            navigator.clipboard.writeText(window.location.href).then(() => {
                // Show success message
                showToast('Link copied to clipboard!', 'success');
            }).catch(() => {
                // Fallback for older browsers
                const textArea = document.createElement('textarea');
                textArea.value = window.location.href;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);
                showToast('Link copied to clipboard!', 'success');
            });
        }

        // Scroll to comments
        function scrollToComments() {
            document.getElementById('comments-section').scrollIntoView({ 
                behavior: 'smooth',
                block: 'start'
            });
        }

        // Scroll to top
        function scrollToTop() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        // Scroll progress bar
        function updateScrollProgress() {
            const scrollProgress = document.getElementById('scrollProgress');
            const scrollTop = document.documentElement.scrollTop || document.body.scrollTop;
            const scrollHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
            const scrolled = (scrollTop / scrollHeight) * 100;
            scrollProgress.style.width = scrolled + '%';
        }

        // Toast notification system
        function showToast(message, type = 'info') {
            const toast = document.createElement('div');
            toast.className = `toast-notification toast-${type}`;
            toast.innerHTML = `
                <div class="toast-content">
                    <i class="bi bi-${type === 'success' ? 'check-circle' : 'info-circle'} me-2"></i>
                    ${message}
                </div>
            `;
            
            // Add toast styles
            toast.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: ${type === 'success' ? 'var(--success-color)' : 'var(--info-color)'};
                color: white;
                padding: 12px 20px;
                border-radius: 8px;
                box-shadow: var(--shadow-medium);
                z-index: 1002;
                transform: translateX(100%);
                transition: transform 0.3s ease;
            `;
            
            document.body.appendChild(toast);
            
            // Animate in
            setTimeout(() => {
                toast.style.transform = 'translateX(0)';
            }, 100);
            
            // Remove after 3 seconds
            setTimeout(() => {
                toast.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    document.body.removeChild(toast);
                }, 300);
            }, 3000);
        }

        // Image modal
        function openImageModal(src) {
            document.getElementById('modalImage').src = src;
            new bootstrap.Modal(document.getElementById('imageModal')).show();
        }

        // Instagram Carousel functionality
        const carousels = {};
        
        @if($post->gallery_images && count($post->gallery_images) > 0)
        const galleryImages = @json($post->gallery_image_urls);
        let currentImageIndex = 0;
        
        // Initialize Facebook carousel
        @php
            $totalImages = count($post->gallery_images) + ($post->featured_image ? 1 : 0);
        @endphp
        carousels['carousel-{{ $post->id }}'] = {
            currentSlide: 0,
            totalSlides: {{ $totalImages }},
            container: null,
            startX: 0,
            isDragging: false
        };
        
        function initCarousel(carouselId) {
            const carousel = document.getElementById(carouselId);
            const container = carousel.querySelector('.post-carousel-container');
            carousels[carouselId].container = container;
            
            // Touch events for mobile swipe
            container.addEventListener('touchstart', handleTouchStart, { passive: true });
            container.addEventListener('touchmove', handleTouchMove, { passive: false });
            container.addEventListener('touchend', handleTouchEnd, { passive: true });
            
            // Mouse events for desktop drag
            container.addEventListener('mousedown', handleMouseDown);
            container.addEventListener('mousemove', handleMouseMove);
            container.addEventListener('mouseup', handleMouseUp);
            container.addEventListener('mouseleave', handleMouseUp);
        }
        
        function prevSlide(carouselId) {
            const carousel = carousels[carouselId];
            carousel.currentSlide = (carousel.currentSlide - 1 + carousel.totalSlides) % carousel.totalSlides;
            updateCarousel(carouselId);
        }
        
        function nextSlide(carouselId) {
            const carousel = carousels[carouselId];
            carousel.currentSlide = (carousel.currentSlide + 1) % carousel.totalSlides;
            updateCarousel(carouselId);
        }
        
        function goToSlide(carouselId, slideIndex) {
            const carousel = carousels[carouselId];
            carousel.currentSlide = slideIndex;
            updateCarousel(carouselId);
        }
        
        function updateCarousel(carouselId) {
            const carousel = carousels[carouselId];
            const container = carousel.container;
            const translateX = -carousel.currentSlide * 100;
            
            container.style.transform = `translateX(${translateX}%)`;
            
            // Update indicators
            const carouselElement = document.getElementById(carouselId);
            const dots = carouselElement.querySelectorAll('.fb-carousel-dot');
            dots.forEach((dot, index) => {
                dot.classList.toggle('active', index === carousel.currentSlide);
            });
            
            // Update counter
            const counter = carouselElement.querySelector('.current-slide');
            if (counter) {
                counter.textContent = carousel.currentSlide + 1;
            }
        }
        
        // Touch and swipe handlers for post carousel
        function handleTouchStart(e) {
            const carouselId = e.currentTarget.closest('.post-carousel').id;
            carousels[carouselId].startX = e.touches[0].clientX;
            carousels[carouselId].isDragging = true;
        }
        
        function handleTouchMove(e) {
            const carouselId = e.currentTarget.closest('.post-carousel').id;
            if (!carousels[carouselId].isDragging) return;
            e.preventDefault();
        }
        
        function handleTouchEnd(e) {
            const carouselId = e.currentTarget.closest('.post-carousel').id;
            const carousel = carousels[carouselId];
            
            if (!carousel.isDragging) return;
            
            const endX = e.changedTouches[0].clientX;
            const diffX = carousel.startX - endX;
            
            if (Math.abs(diffX) > 50) { // Minimum swipe distance
                if (diffX > 0) {
                    nextSlide(carouselId);
                } else {
                    prevSlide(carouselId);
                }
            }
            
            carousel.isDragging = false;
        }
        
        // Mouse drag handlers for post carousel
        function handleMouseDown(e) {
            const carouselId = e.currentTarget.closest('.post-carousel').id;
            carousels[carouselId].startX = e.clientX;
            carousels[carouselId].isDragging = true;
            e.preventDefault();
        }
        
        function handleMouseMove(e) {
            const carouselId = e.currentTarget.closest('.post-carousel').id;
            if (!carousels[carouselId].isDragging) return;
            e.preventDefault();
        }
        
        function handleMouseUp(e) {
            const carouselId = e.currentTarget.closest('.post-carousel').id;
            const carousel = carousels[carouselId];
            
            if (!carousel.isDragging) return;
            
            const endX = e.clientX;
            const diffX = carousel.startX - endX;
            
            if (Math.abs(diffX) > 50) { // Minimum drag distance
                if (diffX > 0) {
                    nextSlide(carouselId);
                } else {
                    prevSlide(carouselId);
                }
            }
            
            carousel.isDragging = false;
        }

        function openLightbox(index) {
            currentImageIndex = index;
            updateGalleryModal();
            new bootstrap.Modal(document.getElementById('galleryModal')).show();
        }

        function updateGalleryModal() {
            const img = document.getElementById('galleryModalImage');
            const counter = document.getElementById('imageCounter');
            
            img.src = galleryImages[currentImageIndex];
            counter.textContent = `${currentImageIndex + 1} of ${galleryImages.length}`;
        }

        function previousImage() {
            currentImageIndex = (currentImageIndex - 1 + galleryImages.length) % galleryImages.length;
            updateGalleryModal();
        }

        function nextImage() {
            currentImageIndex = (currentImageIndex + 1) % galleryImages.length;
            updateGalleryModal();
        }
        @endif

        // Comment functions
        function editComment(commentId) {
            document.getElementById('comment-content-' + commentId).classList.add('d-none');
            document.getElementById('edit-form-' + commentId).classList.remove('d-none');
        }

        function cancelEdit(commentId) {
            document.getElementById('comment-content-' + commentId).classList.remove('d-none');
            document.getElementById('edit-form-' + commentId).classList.add('d-none');
        }

        function toggleReplyForm(commentId) {
            const form = document.getElementById('reply-form-' + commentId);
            form.classList.toggle('d-none');
        }

        function deletePost() {
            if (confirm('Are you sure you want to delete this post? This action cannot be undone.')) {
                // Show loading state
                const deleteBtn = event.target;
                deleteBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Deleting...';
                deleteBtn.disabled = true;
                
                // Create and submit form
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("user.posts.destroy", $post) }}';
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                
                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';
                
                form.appendChild(csrfToken);
                form.appendChild(methodField);
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Initialize page features
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize carousels
            @if(($post->featured_image && $post->gallery_images && count($post->gallery_images) > 0) || (!$post->featured_image && $post->gallery_images && count($post->gallery_images) > 1))
            initCarousel('carousel-{{ $post->id }}');
            @endif
            
            // Scroll progress listener
            window.addEventListener('scroll', updateScrollProgress);
            
            // Floating action button visibility
            const floatingBtn = document.querySelector('.floating-action');
            if (floatingBtn) {
                window.addEventListener('scroll', function() {
                    if (window.scrollY > 300) {
                        floatingBtn.style.opacity = '1';
                        floatingBtn.style.pointerEvents = 'auto';
                    } else {
                        floatingBtn.style.opacity = '0';
                        floatingBtn.style.pointerEvents = 'none';
                    }
                });
                
                // Initialize floating button as hidden
                floatingBtn.style.opacity = '0';
                floatingBtn.style.pointerEvents = 'none';
                floatingBtn.style.transition = 'opacity 0.3s ease';
            }
            
            // Add keyboard shortcuts
            document.addEventListener('keydown', function(e) {
                // Press 'L' to like
                if (e.key.toLowerCase() === 'l' && !e.ctrlKey && !e.metaKey) {
                    const activeElement = document.activeElement;
                    if (activeElement.tagName !== 'INPUT' && activeElement.tagName !== 'TEXTAREA') {
                        e.preventDefault();
                        toggleLike();
                    }
                }
                
                // Press 'C' to focus comment box
                if (e.key.toLowerCase() === 'c' && !e.ctrlKey && !e.metaKey) {
                    const activeElement = document.activeElement;
                    if (activeElement.tagName !== 'INPUT' && activeElement.tagName !== 'TEXTAREA') {
                        e.preventDefault();
                        scrollToComments();
                        setTimeout(() => {
                            const commentBox = document.querySelector('textarea[name="content"]');
                            if (commentBox) commentBox.focus();
                        }, 500);
                    }
                }
            });
            
            // Instagram-style interactions
            const comments = document.querySelectorAll('.comment');
            comments.forEach(comment => {
                comment.addEventListener('mouseenter', function() {
                    this.style.backgroundColor = 'var(--ig-background)';
                });
                
                comment.addEventListener('mouseleave', function() {
                    this.style.backgroundColor = 'transparent';
                });
            });

            // Double-tap to like (mobile)
            let lastTap = 0;
            document.querySelector('.post-image')?.addEventListener('touchend', function(e) {
                const currentTime = new Date().getTime();
                const tapLength = currentTime - lastTap;
                if (tapLength < 500 && tapLength > 0) {
                    toggleLike();
                    // Show heart animation
                    const heart = document.createElement('div');
                    heart.innerHTML = '<i class="bi bi-heart-fill" style="font-size: 60px; color: #dc3545;"></i>';
                    heart.style.cssText = `
                        position: absolute;
                        top: 50%;
                        left: 50%;
                        transform: translate(-50%, -50%);
                        pointer-events: none;
                        animation: heartPop 1s ease-out forwards;
                        z-index: 1000;
                    `;
                    this.parentElement.style.position = 'relative';
                    this.parentElement.appendChild(heart);
                    setTimeout(() => {
                        heart.remove();
                    }, 1000);
                }
                lastTap = currentTime;
            });
        });

        // Add CSS for animations
        const style = document.createElement('style');
        style.textContent = `
            @keyframes heartPop {
                0% { transform: translate(-50%, -50%) scale(0); opacity: 1; }
                50% { transform: translate(-50%, -50%) scale(1.2); opacity: 0.8; }
                100% { transform: translate(-50%, -50%) scale(1.5); opacity: 0; }
            }
        `;
        document.head.appendChild(style);
    </script>
@endsection
