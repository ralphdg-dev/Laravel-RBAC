@extends('layouts.app')

@section('content')
    <!-- Hero Section -->
    <div class="blog-hero bg-gradient-primary text-white py-5 mb-5">
        <div class="container">
            <div class="row justify-content-center text-center">
                <div class="col-lg-8">
                    <h1 class="display-4 fw-bold mb-3 text-shadow">Our Blog</h1>
                    <p class="lead mb-4 text-white-75">
                        Discover insights, stories, and updates from our community
                    </p>
                    <div class="blog-stats d-flex justify-content-center gap-4 flex-wrap">
                        <div class="stat-item">
                            <div class="h4 fw-bold mb-0">{{ $posts->total() }}</div>
                            <small class="text-white-75">{{ Str::plural('Post', $posts->total()) }}</small>
                        </div>
                        <div class="stat-item">
                            <div class="h4 fw-bold mb-0">{{ \App\Models\Category::count() }}</div>
                            <small class="text-white-75">{{ Str::plural('Category', \App\Models\Category::count()) }}</small>
                        </div>
                        <div class="stat-item">
                            <div class="h4 fw-bold mb-0">{{ \App\Models\User::count() }}</div>
                            <small class="text-white-75">{{ Str::plural('Author', \App\Models\User::count()) }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Categories Filter -->
        @php
            $categories = \App\Models\Category::withCount(['posts' => function($query) {
                $query->where('status', 'approved');
            }])->having('posts_count', '>', 0)->get();
        @endphp
        
        @if($categories->count() > 0)
        <div class="categories-filter mb-5">
            <div class="bg-white rounded-4 shadow-sm p-4">
                <h5 class="fw-bold mb-3">
                    <i class="bi bi-funnel me-2 text-primary"></i>Browse by Category
                </h5>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('public.blog') }}" 
                       class="btn btn-outline-primary btn-sm rounded-pill {{ !request('category') ? 'active' : '' }}">
                        <i class="bi bi-grid me-1"></i>All Posts
                    </a>
                    @foreach($categories as $category)
                        <a href="{{ route('public.blog', ['category' => $category->id]) }}" 
                           class="btn btn-outline-primary btn-sm rounded-pill {{ request('category') == $category->id ? 'active' : '' }}">
                            {{ $category->name }}
                            <span class="badge bg-primary ms-1">{{ $category->posts_count }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Posts Grid -->
        @if($posts->count() > 0)
            <div class="posts-grid mb-5">
                <div class="row g-4">
                    @foreach($posts as $post)
                    <div class="col-lg-4 col-md-6">
                        <article class="post-card h-100">
                            <div class="card border-0 shadow-sm h-100 hover-lift">
                                @if($post->featured_image)
                                    <div class="card-img-wrapper position-relative overflow-hidden">
                                        <img src="{{ $post->featured_image_url }}" 
                                             class="card-img-top post-thumbnail" 
                                             alt="{{ $post->title }}"
                                             style="height: 200px; object-fit: cover;">
                                        <div class="img-overlay"></div>
                                        @if($post->category)
                                            <span class="position-absolute top-0 start-0 m-3">
                                                <span class="badge bg-primary bg-opacity-90 rounded-pill px-3 py-2">
                                                    {{ $post->category->name }}
                                                </span>
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                        <div class="text-center text-muted">
                                            <i class="bi bi-image fs-1 mb-2"></i>
                                            @if($post->category)
                                                <div>
                                                    <span class="badge bg-primary rounded-pill px-3 py-2">
                                                        {{ $post->category->name }}
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                                
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title mb-3">
                                        <a href="{{ route('public.post.show', $post) }}" 
                                           class="text-decoration-none text-dark stretched-link">
                                            {{ $post->title }}
                                        </a>
                                    </h5>
                                    
                                    <p class="card-text text-muted mb-3 flex-grow-1">
                                        {{ Str::limit(strip_tags($post->content), 120) }}
                                    </p>
                                    
                                    <div class="post-meta d-flex align-items-center justify-content-between text-sm text-muted">
                                        <div class="d-flex align-items-center">
                                            @if($post->author)
                                                <i class="bi bi-person-circle me-1"></i>
                                                <span class="me-3">{{ $post->author }}</span>
                                            @endif
                                            <i class="bi bi-calendar3 me-1"></i>
                                            <span>{{ $post->created_at->format('M d, Y') }}</span>
                                        </div>
                                        <div class="reading-time">
                                            <i class="bi bi-clock me-1"></i>
                                            {{ ceil(str_word_count(strip_tags($post->content)) / 200) }} min
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </article>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                {{ $posts->appends(request()->query())->links('pagination::bootstrap-4') }}
            </div>
        @else
            <!-- Empty State -->
            <div class="empty-state text-center py-5">
                <div class="bg-white rounded-4 shadow-sm p-5">
                    <i class="bi bi-journal-x display-1 text-muted mb-4"></i>
                    <h3 class="h4 text-muted mb-3">No Posts Found</h3>
                    <p class="text-muted mb-4">
                        @if(request('category'))
                            No posts found in this category. Try browsing other categories or check back later.
                        @else
                            There are no published posts yet. Check back soon for new content!
                        @endif
                    </p>
                    @if(request('category'))
                        <a href="{{ route('public.blog') }}" class="btn btn-primary">
                            <i class="bi bi-arrow-left me-1"></i>View All Posts
                        </a>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <style>
        /* Hero Section */
        .blog-hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: relative;
            overflow: hidden;
        }
        
        .blog-hero::before {
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
        
        .stat-item {
            text-align: center;
            min-width: 80px;
        }

        /* Post Cards */
        .hover-lift {
            transition: all 0.3s ease;
        }
        
        .hover-lift:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.1) !important;
        }
        
        .post-thumbnail {
            transition: transform 0.3s ease;
        }
        
        .card:hover .post-thumbnail {
            transform: scale(1.05);
        }
        
        .img-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(0,0,0,0.1), transparent);
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .card:hover .img-overlay {
            opacity: 1;
        }
        
        .card-img-wrapper {
            position: relative;
        }
        
        .stretched-link::before {
            z-index: 1;
        }

        /* Categories Filter */
        .categories-filter .btn.active {
            background-color: var(--bs-primary);
            border-color: var(--bs-primary);
            color: white;
        }
        
        .categories-filter .btn:hover {
            transform: translateY(-1px);
        }

        /* Post Meta */
        .post-meta {
            font-size: 0.875rem;
        }
        
        .reading-time {
            white-space: nowrap;
        }

        /* Pagination */
        .pagination .page-link {
            border-radius: 0.5rem;
            margin: 0 2px;
            border: 1px solid #dee2e6;
            transition: all 0.2s ease;
        }
        
        .pagination .page-item.active .page-link {
            background: linear-gradient(135deg, #0d6efd, #0056b3);
            border-color: #0d6efd;
            box-shadow: 0 2px 4px rgba(13,110,253,0.3);
        }
        
        .pagination .page-link:hover {
            background-color: #f8f9fa;
            border-color: #0d6efd;
            color: #0d6efd;
            transform: translateY(-1px);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .blog-hero {
                padding: 3rem 0 !important;
            }
            
            .display-4 {
                font-size: 2.5rem !important;
            }
            
            .blog-stats {
                flex-direction: column;
                gap: 1rem !important;
            }
            
            .stat-item {
                min-width: auto;
            }
            
            .categories-filter .d-flex {
                justify-content: center;
            }
        }

        /* Smooth Animations */
        * {
            scroll-behavior: smooth;
        }
        
        .btn {
            transition: all 0.2s ease;
        }
        
        .card {
            transition: all 0.3s ease;
        }
    </style>

    <script>
        // Add smooth scroll behavior for category links
        document.querySelectorAll('.categories-filter a').forEach(link => {
            link.addEventListener('click', function(e) {
                if (this.getAttribute('href').includes('#')) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                }
            });
        });

        // Add loading animation to cards
        document.querySelectorAll('.post-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-8px)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
    </script>
@endsection
