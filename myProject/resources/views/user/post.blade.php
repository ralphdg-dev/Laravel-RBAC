@extends('layouts.app')

@section('content')
    <!-- Facebook-style CSS -->
    <style>
        body {
            background-color: #f0f2f5 !important;
        }
        
        .fb-post-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
            margin-bottom: 20px;
            overflow: hidden;
        }
        
        .fb-post-header {
            padding: 16px 16px 0 16px;
        }
        
        .fb-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #1877f2, #42a5f5);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 16px;
        }
        
        .fb-post-meta {
            color: #65676b;
            font-size: 13px;
            line-height: 16px;
        }
        
        .fb-post-meta a {
            color: #65676b;
            text-decoration: none;
        }
        
        .fb-post-meta a:hover {
            text-decoration: underline;
        }
        
        .fb-author-name {
            color: #050505;
            font-weight: 600;
            font-size: 15px;
            line-height: 20px;
            text-decoration: none;
        }
        
        .fb-author-name:hover {
            text-decoration: underline;
            color: #050505;
        }
        
        .fb-post-content {
            padding: 16px;
            color: #050505;
            font-size: 15px;
            line-height: 20px;
        }
        
        .fb-post-title {
            font-size: 20px;
            font-weight: 600;
            color: #050505;
            margin-bottom: 8px;
            line-height: 24px;
        }
        
        .fb-category-badge {
            background: #e7f3ff;
            color: #1877f2;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
            display: inline-block;
            margin-bottom: 12px;
        }
        
        .fb-post-image {
            width: 100%;
            height: auto;
            max-height: 500px;
            object-fit: cover;
            cursor: pointer;
        }
        
        .fb-gallery {
            display: grid;
            gap: 2px;
            margin-top: 12px;
        }
        
        .fb-gallery.single {
            grid-template-columns: 1fr;
        }
        
        .fb-gallery.two {
            grid-template-columns: 1fr 1fr;
        }
        
        .fb-gallery.three {
            grid-template-columns: 2fr 1fr;
            grid-template-rows: 1fr 1fr;
        }
        
        .fb-gallery.multiple {
            grid-template-columns: 1fr 1fr;
            grid-template-rows: 1fr 1fr;
        }
        
        .fb-gallery-item {
            position: relative;
            overflow: hidden;
            background: #f0f2f5;
            aspect-ratio: 1;
            cursor: pointer;
        }
        
        .fb-gallery.single .fb-gallery-item {
            aspect-ratio: 16/10;
        }
        
        .fb-gallery.three .fb-gallery-item:first-child {
            grid-row: 1 / 3;
            aspect-ratio: 1;
        }
        
        .fb-gallery-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.2s ease;
        }
        
        .fb-gallery-item:hover .fb-gallery-img {
            transform: scale(1.05);
        }
        
        .fb-gallery-more {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.6);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            font-weight: 600;
        }
        
        .fb-actions {
            border-top: 1px solid #ced0d4;
            padding: 8px 16px;
            display: flex;
            justify-content: space-around;
        }
        
        .fb-action-btn {
            flex: 1;
            padding: 8px 12px;
            border: none;
            background: none;
            color: #65676b;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            border-radius: 6px;
            transition: background-color 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .fb-action-btn:hover {
            background-color: #f2f2f2;
        }
        
        .fb-action-btn.liked {
            color: #1877f2;
        }
        
        .fb-action-btn.shared {
            color: #42b883;
        }
        
        .fb-breadcrumb {
            background: white;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 16px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }
        
        .fb-breadcrumb a {
            color: #1877f2;
            text-decoration: none;
            font-size: 14px;
        }
        
        .fb-breadcrumb a:hover {
            text-decoration: underline;
        }
        
        .fb-breadcrumb-separator {
            color: #65676b;
            margin: 0 8px;
        }
        
        .fb-status-badge {
            background: #d4edda;
            color: #155724;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 500;
            text-transform: uppercase;
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 0 8px;
            }
            
            .fb-post-container {
                border-radius: 0;
                margin-bottom: 8px;
            }
            
            .fb-breadcrumb {
                border-radius: 0;
                margin-bottom: 8px;
            }
        }
    </style>

    <div class="container py-3">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <!-- Breadcrumb -->
                <div class="fb-breadcrumb">
                    <a href="{{ route('user.dashboard') }}">
                        <i class="bi bi-house-door me-1"></i>Dashboard
                    </a>
                    <span class="fb-breadcrumb-separator">></span>
                    <span class="text-muted">{{ Str::limit($post->title, 30) }}</span>
                </div>

                <!-- Main Post -->
                <div class="fb-post-container">
                    <!-- Post Header -->
                    <div class="fb-post-header">
                        <div class="d-flex align-items-start">
                            <div class="fb-avatar me-3">
                                {{ strtoupper(substr($post->author ?: $post->user->name, 0, 1)) }}
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <a href="#" class="fb-author-name">
                                        {{ $post->author ?: $post->user->name }}
                                    </a>
                                    @if($post->status === 'approved')
                                        <span class="fb-status-badge">{{ ucfirst($post->status) }}</span>
                                    @endif
                                </div>
                                <div class="fb-post-meta">
                                    <span>{{ $post->created_at->format('F d, Y \a\t g:i A') }}</span>
                                    <span class="mx-1">·</span>
                                    <i class="bi bi-globe2"></i>
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
                    </div>

                    <!-- Post Content -->
                    <div class="fb-post-content">
                        @if($post->category)
                            <span class="fb-category-badge">
                                <i class="bi bi-tag me-1"></i>{{ $post->category->name }}
                            </span>
                        @endif
                        
                        <div class="fb-post-title">{{ $post->title }}</div>
                        
                        <div class="fb-post-text">
                            {!! nl2br(e($post->content)) !!}
                        </div>
                    </div>

                    <!-- Featured Image -->
                    @if($post->featured_image)
                        <div class="fb-post-media">
                            <img src="{{ $post->featured_image_url }}" 
                                 alt="{{ $post->featured_image_alt ?: $post->title }}" 
                                 class="fb-post-image"
                                 onclick="openImageModal('{{ $post->featured_image_url }}')">
                        </div>
                    @endif

                    <!-- Gallery -->
                    @if($post->gallery_images && count($post->gallery_images) > 0)
                        <div class="fb-post-content">
                            <div class="fb-gallery {{ count($post->gallery_images) == 1 ? 'single' : (count($post->gallery_images) == 2 ? 'two' : (count($post->gallery_images) == 3 ? 'three' : 'multiple')) }}">
                                @foreach($post->gallery_image_urls as $index => $imageUrl)
                                    @if($index < 4)
                                        <div class="fb-gallery-item" onclick="openLightbox({{ $index }})">
                                            <img src="{{ $imageUrl }}" alt="Gallery Image {{ $index + 1 }}" class="fb-gallery-img">
                                            @if($index == 3 && count($post->gallery_images) > 4)
                                                <div class="fb-gallery-more">
                                                    +{{ count($post->gallery_images) - 4 }}
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Post Actions -->
                    <div class="fb-actions">
                        <button class="fb-action-btn like-btn" onclick="toggleLike()">
                            <i class="bi bi-heart"></i>
                            <span class="like-text">Like</span>
                        </button>
                        <button class="fb-action-btn" onclick="scrollToComments()">
                            <i class="bi bi-chat"></i>
                            Comment
                        </button>
                        <button class="fb-action-btn share-btn" onclick="sharePost()">
                            <i class="bi bi-share"></i>
                            Share
                        </button>
                    </div>
                </div>

                <!-- Comments Section -->
                <div class="fb-post-container" id="comments-section">
                    <div class="fb-post-header">
                        <h5 class="mb-0">
                            <i class="bi bi-chat-dots me-2"></i>
                            Comments ({{ $post->comments->count() }})
                        </h5>
                    </div>
                    
                    <div class="fb-post-content">
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
                                    <div class="fb-avatar">
                                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                    </div>
                                    <div class="flex-grow-1">
                                        <textarea class="form-control @error('content') is-invalid @enderror" 
                                                  name="content" rows="2" 
                                                  placeholder="Write a comment..."
                                                  style="border-radius: 20px; border: 1px solid #ced0d4; resize: none;">{{ old('content') }}</textarea>
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
                        @endauth

                        @if($comments->count() > 0)
                            <div class="comments-list">
                                @foreach($comments as $comment)
                                    <div class="comment mb-3 p-3" style="background: #f0f2f5; border-radius: 16px;">
                                        <div class="d-flex gap-3">
                                            <div class="fb-avatar" style="width: 32px; height: 32px; font-size: 14px;">
                                                {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <strong class="fb-author-name">{{ $comment->user->name }}</strong>
                                                        <div class="fb-post-meta">
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
                                                                    <li>
                                                                        <form action="{{ route('comments.destroy', $comment) }}" method="POST" class="d-inline" 
                                                                              onsubmit="return confirm('Delete this comment?')">
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
                                                
                                                <div class="comment-content mt-2" id="comment-content-{{ $comment->id }}">
                                                    <p class="mb-0">{{ $comment->content }}</p>
                                                </div>
                                                
                                                <div class="edit-comment-form d-none" id="edit-form-{{ $comment->id }}">
                                                    <form action="{{ route('comments.update', $comment) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <textarea class="form-control mb-2" name="content" rows="2" style="border-radius: 16px;">{{ $comment->content }}</textarea>
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
                                                                <div class="fb-avatar" style="width: 28px; height: 28px; font-size: 12px;">
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
                                                                    <div class="fb-avatar" style="width: 28px; height: 28px; font-size: 12px;">
                                                                        {{ strtoupper(substr($reply->user->name, 0, 1)) }}
                                                                    </div>
                                                                    <div class="flex-grow-1">
                                                                        <div class="p-2" style="background: white; border-radius: 12px;">
                                                                            <strong class="fb-author-name" style="font-size: 13px;">{{ $reply->user->name }}</strong>
                                                                            <div style="font-size: 14px;">{{ $reply->content }}</div>
                                                                        </div>
                                                                        <div class="fb-post-meta mt-1 ms-2">
                                                                            {{ $reply->created_at->diffForHumans() }}
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
        // Like functionality
        function toggleLike() {
            const btn = document.querySelector('.like-btn');
            const icon = btn.querySelector('i');
            const text = btn.querySelector('.like-text');
            
            if (btn.classList.contains('liked')) {
                btn.classList.remove('liked');
                icon.className = 'bi bi-heart';
                text.textContent = 'Like';
            } else {
                btn.classList.add('liked');
                icon.className = 'bi bi-heart-fill';
                text.textContent = 'Liked';
            }
        }

        // Share functionality
        function sharePost() {
            if (navigator.share) {
                navigator.share({
                    title: '{{ $post->title }}',
                    text: '{{ Str::limit(strip_tags($post->content), 100) }}',
                    url: window.location.href
                });
            } else {
                navigator.clipboard.writeText(window.location.href).then(() => {
                    alert('Link copied to clipboard!');
                });
            }
        }

        // Scroll to comments
        function scrollToComments() {
            document.getElementById('comments-section').scrollIntoView({ behavior: 'smooth' });
        }

        // Image modal
        function openImageModal(src) {
            document.getElementById('modalImage').src = src;
            new bootstrap.Modal(document.getElementById('imageModal')).show();
        }

        // Gallery lightbox
        @if($post->gallery_images && count($post->gallery_images) > 0)
        const galleryImages = @json($post->gallery_image_urls);
        let currentImageIndex = 0;

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
            if (confirm('Are you sure you want to delete this post?')) {
                // Add delete functionality here
                window.location.href = '{{ route("user.posts.destroy", $post) }}';
            }
        }
    </script>
@endsection
