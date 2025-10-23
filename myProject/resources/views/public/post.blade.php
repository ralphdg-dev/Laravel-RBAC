@extends('layouts.app')

@section('content')
    <!-- Hero Section with Background -->
    <div class="hero-section bg-gradient-primary text-white py-5 mb-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <nav aria-label="breadcrumb" class="mb-4">
                        <ol class="breadcrumb bg-transparent p-0 mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('public.blog') }}" class="text-white-50 text-decoration-none">
                                    <i class="bi bi-house-door me-1"></i>Blog
                                </a>
                            </li>
                            <li class="breadcrumb-item active text-white" aria-current="page">
                                {{ Str::limit($post->title, 30) }}
                            </li>
                        </ol>
                    </nav>
                    
                    <div class="hero-content">
                        <h1 class="display-4 fw-bold mb-4 text-shadow">{{ $post->title }}</h1>
                        
                        <div class="d-flex flex-wrap align-items-center gap-3 mb-4">
                            @if($post->category)
                                <span class="badge bg-light bg-opacity-25 text-white px-3 py-2 rounded-pill">
                                    <i class="bi bi-tag me-1"></i>{{ $post->category->name }}
                                </span>
                            @endif
                            @if($post->author)
                                <span class="d-flex align-items-center text-white-75">
                                    <i class="bi bi-person-circle me-2 fs-5"></i>
                                    <span class="fw-medium">{{ $post->author }}</span>
                                </span>
                            @endif
                            <span class="d-flex align-items-center text-white-75">
                                <i class="bi bi-calendar3 me-2"></i>
                                <span>{{ $post->created_at->format('F d, Y') }}</span>
                            </span>
                            <span class="badge bg-success bg-opacity-90 px-3 py-2 rounded-pill">
                                <i class="bi bi-check-circle me-1"></i>{{ ucfirst($post->status) }}
                            </span>
                        </div>
                        
                        <div class="reading-time text-white-75">
                            <i class="bi bi-clock me-1"></i>
                            <span>{{ ceil(str_word_count(strip_tags($post->content)) / 200) }} min read</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <article class="post-article">
                    <div class="post-content-wrapper bg-white rounded-4 shadow-lg overflow-hidden">
                        <div class="p-5">

                            <!-- Featured Image -->
                            @if($post->featured_image)
                            <div class="featured-image mb-5">
                                <div class="position-relative overflow-hidden rounded-3">
                                    <img src="{{ $post->featured_image_url }}" 
                                        alt="{{ $post->featured_image_alt ?: $post->title }}" 
                                        class="img-fluid w-100 featured-img">
                                    <div class="image-overlay"></div>
                                </div>
                                @if($post->featured_image_alt)
                                    <figcaption class="text-muted text-center mt-3 fst-italic small">
                                        {{ $post->featured_image_alt }}
                                    </figcaption>
                                @endif
                            </div>
                            @endif

                            <!-- Post Content -->
                            <div class="post-content mb-5">
                                <div class="content-text fs-5 lh-lg text-dark">
                                    {!! nl2br(e($post->content)) !!}
                                </div>
                            </div>

                            <!-- Gallery Images -->
                            @if($post->gallery_images && count($post->gallery_images) > 0)
                            <div class="gallery-section mb-5">
                                <div class="section-header mb-4">
                                    <h3 class="h4 fw-bold text-primary mb-2">
                                        <i class="bi bi-images me-2"></i>Image Gallery
                                    </h3>
                                    <p class="text-muted mb-0">Click on any image to view in full size</p>
                                </div>
                                
                                <div class="gallery-grid">
                                    @foreach($post->gallery_image_urls as $index => $imageUrl)
                                    <div class="gallery-item-wrapper">
                                        <div class="gallery-item position-relative overflow-hidden rounded-3 shadow-sm">
                                            <img src="{{ $imageUrl }}" 
                                                alt="Gallery Image {{ $index + 1 }}" 
                                                class="gallery-thumbnail"
                                                onclick="openLightbox({{ $index }})">
                                            <div class="gallery-overlay">
                                                <div class="overlay-content">
                                                    <i class="bi bi-zoom-in fs-1 text-white"></i>
                                                    <p class="text-white mt-2 mb-0">View Full Size</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>

                        <!-- Post Actions Footer -->
                        <div class="post-actions bg-light bg-opacity-50 p-4 border-top">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <small class="text-muted d-flex align-items-center">
                                        <i class="bi bi-clock-history me-2"></i>
                                        Last updated {{ $post->updated_at->format('M d, Y \a\t g:i A') }}
                                    </small>
                                </div>
                                <div class="col-md-6 text-md-end mt-3 mt-md-0">
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-outline-danger btn-sm like-btn">
                                            <i class="bi bi-heart me-1"></i>
                                            <span class="like-text">Like</span>
                                            <span class="badge bg-danger ms-1">0</span>
                                        </button>
                                        <button type="button" class="btn btn-outline-primary btn-sm share-btn" 
                                                onclick="sharePost()">
                                            <i class="bi bi-share me-1"></i>Share
                                        </button>
                                        <button type="button" class="btn btn-outline-success btn-sm bookmark-btn">
                                            <i class="bi bi-bookmark me-1"></i>Save
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </article>

                <!-- Related Posts Section -->
                <div class="related-posts mt-5">
                    <div class="bg-white rounded-4 shadow-lg overflow-hidden">
                        <div class="p-4 border-bottom bg-gradient-light">
                            <h3 class="h4 fw-bold text-dark mb-0">
                                <i class="bi bi-collection me-2 text-primary"></i>
                                More Posts
                            </h3>
                        </div>
                        <div class="p-4">
                            @php
                                $relatedPosts = \App\Models\Post::where('status', 'approved')
                                    ->where('id', '!=', $post->id)
                                    ->when($post->category_id, function($query) use ($post) {
                                        return $query->where('category_id', $post->category_id);
                                    })
                                    ->latest()
                                    ->limit(3)
                                    ->get();
                            @endphp
                            
                            @if($relatedPosts->count() > 0)
                                <div class="row g-4">
                                    @foreach($relatedPosts as $relatedPost)
                                    <div class="col-md-4">
                                        <div class="card h-100 border-0 shadow-sm hover-lift">
                                            @if($relatedPost->featured_image)
                                                <img src="{{ $relatedPost->featured_image_url }}" 
                                                     class="card-img-top" 
                                                     style="height: 150px; object-fit: cover;"
                                                     alt="{{ $relatedPost->title }}">
                                            @endif
                                            <div class="card-body">
                                                <h6 class="card-title">
                                                    <a href="{{ route('public.post.show', $relatedPost) }}" 
                                                       class="text-decoration-none text-dark">
                                                        {{ Str::limit($relatedPost->title, 50) }}
                                                    </a>
                                                </h6>
                                                <p class="card-text small text-muted">
                                                    {{ Str::limit(strip_tags($relatedPost->content), 80) }}
                                                </p>
                                                <small class="text-muted">
                                                    <i class="bi bi-calendar3 me-1"></i>
                                                    {{ $relatedPost->created_at->format('M d, Y') }}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="bi bi-collection display-4 text-muted"></i>
                                    <p class="text-muted mt-2">No related posts found.</p>
                                    <a href="{{ route('public.blog') }}" class="btn btn-primary">
                                        <i class="bi bi-arrow-left me-1"></i>Back to Blog
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Lightbox Modal -->
    @if($post->gallery_images && count($post->gallery_images) > 0)
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalLabel">Gallery Image</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalImage" src="" alt="" class="img-fluid">
                    <div class="mt-3">
                        <button type="button" class="btn btn-outline-secondary" onclick="previousImage()">
                            <i class="bi bi-chevron-left"></i> Previous
                        </button>
                        <span id="imageCounter" class="mx-3"></span>
                        <button type="button" class="btn btn-outline-secondary" onclick="nextImage()">
                            Next <i class="bi bi-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <style>
        /* Hero Section Styles */
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: relative;
            overflow: hidden;
        }
        
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }
        
        .text-shadow {
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
        
        .text-white-75 {
            color: rgba(255,255,255,0.85) !important;
        }

        /* Post Content Styles */
        .post-content-wrapper {
            border: 1px solid rgba(0,0,0,0.05);
        }
        
        .featured-img {
            transition: transform 0.3s ease;
            max-height: 400px;
            object-fit: cover;
        }
        
        .featured-img:hover {
            transform: scale(1.02);
        }
        
        .image-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(0,0,0,0.1), transparent);
            pointer-events: none;
        }
        
        .content-text {
            line-height: 1.8;
            font-family: 'Georgia', serif;
        }
        
        .content-text p {
            margin-bottom: 1.5rem;
        }

        /* Gallery Styles */
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
        }
        
        .gallery-item {
            aspect-ratio: 4/3;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        
        .gallery-item:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
            border-color: var(--bs-primary);
        }
        
        .gallery-thumbnail {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        
        .gallery-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .gallery-item:hover .gallery-overlay {
            opacity: 1;
        }
        
        .gallery-item:hover .gallery-thumbnail {
            transform: scale(1.1);
        }
        
        .overlay-content {
            text-align: center;
            transform: translateY(10px);
            transition: transform 0.3s ease;
        }
        
        .gallery-item:hover .overlay-content {
            transform: translateY(0);
        }

        /* Action Buttons */
        .like-btn:hover {
            background-color: #dc3545;
            border-color: #dc3545;
            color: white;
        }
        
        .share-btn:hover {
            background-color: #0d6efd;
            border-color: #0d6efd;
            color: white;
        }
        
        .bookmark-btn:hover {
            background-color: #198754;
            border-color: #198754;
            color: white;
        }

        /* Related Posts */
        .bg-gradient-light {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }
        
        .hover-lift {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .hover-lift:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .hero-section {
                padding: 3rem 0 !important;
            }
            
            .display-4 {
                font-size: 2rem !important;
            }
            
            .gallery-grid {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 1rem;
            }
            
            .post-content-wrapper {
                margin: 0 -15px;
                border-radius: 0 !important;
            }
        }

        /* Smooth Animations */
        * {
            scroll-behavior: smooth;
        }
        
        .btn {
            transition: all 0.2s ease;
        }
        
        .btn:hover {
            transform: translateY(-1px);
        }
        
        .card, .post-content-wrapper {
            transition: box-shadow 0.3s ease;
        }
        
        .shadow-lg:hover {
            box-shadow: 0 1rem 3rem rgba(0,0,0,0.175) !important;
        }
    </style>

    <!-- JavaScript for Image Gallery and Interactive Features -->
    <script>
        @if($post->gallery_images && count($post->gallery_images) > 0)
        // Gallery lightbox functions
        const galleryImages = @json($post->gallery_image_urls);
        let currentImageIndex = 0;

        function openLightbox(index) {
            currentImageIndex = index;
            updateModalImage();
            const modal = new bootstrap.Modal(document.getElementById('imageModal'));
            modal.show();
        }

        function updateModalImage() {
            const modalImage = document.getElementById('modalImage');
            const imageCounter = document.getElementById('imageCounter');
            
            modalImage.src = galleryImages[currentImageIndex];
            modalImage.alt = `Gallery Image ${currentImageIndex + 1}`;
            imageCounter.textContent = `${currentImageIndex + 1} of ${galleryImages.length}`;
        }

        function previousImage() {
            currentImageIndex = (currentImageIndex - 1 + galleryImages.length) % galleryImages.length;
            updateModalImage();
        }

        function nextImage() {
            currentImageIndex = (currentImageIndex + 1) % galleryImages.length;
            updateModalImage();
        }

        // Keyboard navigation for lightbox
        document.addEventListener('keydown', function(e) {
            const modal = document.getElementById('imageModal');
            if (modal.classList.contains('show')) {
                if (e.key === 'ArrowLeft') {
                    previousImage();
                } else if (e.key === 'ArrowRight') {
                    nextImage();
                } else if (e.key === 'Escape') {
                    bootstrap.Modal.getInstance(modal).hide();
                }
            }
        });
        @endif

        // Interactive button functionality
        function sharePost() {
            if (navigator.share) {
                navigator.share({
                    title: '{{ $post->title }}',
                    text: '{{ Str::limit(strip_tags($post->content), 100) }}',
                    url: window.location.href
                }).catch(console.error);
            } else {
                // Fallback to clipboard
                navigator.clipboard.writeText(window.location.href).then(() => {
                    showToast('Link copied to clipboard!', 'success');
                });
            }
        }

        // Like button functionality
        document.querySelector('.like-btn').addEventListener('click', function() {
            const btn = this;
            const icon = btn.querySelector('i');
            const text = btn.querySelector('.like-text');
            const badge = btn.querySelector('.badge');
            
            if (icon.classList.contains('bi-heart')) {
                icon.classList.remove('bi-heart');
                icon.classList.add('bi-heart-fill');
                text.textContent = 'Liked';
                btn.classList.remove('btn-outline-danger');
                btn.classList.add('btn-danger');
                badge.textContent = parseInt(badge.textContent) + 1;
                showToast('Post liked!', 'success');
            } else {
                icon.classList.remove('bi-heart-fill');
                icon.classList.add('bi-heart');
                text.textContent = 'Like';
                btn.classList.remove('btn-danger');
                btn.classList.add('btn-outline-danger');
                badge.textContent = parseInt(badge.textContent) - 1;
                showToast('Like removed', 'info');
            }
        });

        // Bookmark button functionality
        document.querySelector('.bookmark-btn').addEventListener('click', function() {
            const btn = this;
            const icon = btn.querySelector('i');
            
            if (icon.classList.contains('bi-bookmark')) {
                icon.classList.remove('bi-bookmark');
                icon.classList.add('bi-bookmark-fill');
                btn.classList.remove('btn-outline-success');
                btn.classList.add('btn-success');
                btn.innerHTML = '<i class="bi bi-bookmark-fill me-1"></i>Saved';
                showToast('Post saved!', 'success');
            } else {
                icon.classList.remove('bi-bookmark-fill');
                icon.classList.add('bi-bookmark');
                btn.classList.remove('btn-success');
                btn.classList.add('btn-outline-success');
                btn.innerHTML = '<i class="bi bi-bookmark me-1"></i>Save';
                showToast('Bookmark removed', 'info');
            }
        });

        // Toast notification function
        function showToast(message, type = 'info') {
            const toastContainer = document.getElementById('toast-container') || createToastContainer();
            const toast = document.createElement('div');
            toast.className = `toast align-items-center text-white bg-${type} border-0`;
            toast.setAttribute('role', 'alert');
            toast.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi bi-check-circle me-2"></i>${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            `;
            
            toastContainer.appendChild(toast);
            const bsToast = new bootstrap.Toast(toast);
            bsToast.show();
            
            toast.addEventListener('hidden.bs.toast', () => {
                toast.remove();
            });
        }

        function createToastContainer() {
            const container = document.createElement('div');
            container.id = 'toast-container';
            container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
            container.style.zIndex = '1055';
            document.body.appendChild(container);
            return container;
        }
    </script>
@endsection
