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
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('admin.posts.edit', $post) }}" class="btn btn-primary">
                            <i class="bi bi-pencil me-1"></i>Edit
                        </a>

                        @if($post->status !== 'approved')
                            <form action="{{ route('admin.posts.updateStatus', $post) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="approved">
                                <button type="submit" class="btn btn-success" onclick="return confirm('Approve this post?')">
                                    <i class="bi bi-check-circle me-1"></i>Approve
                                </button>
                            </form>
                        @endif

                        @if($post->status !== 'rejected')
                            <form action="{{ route('admin.posts.updateStatus', $post) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="rejected">
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Reject this post?')">
                                    <i class="bi bi-x-circle me-1"></i>Reject
                                </button>
                            </form>
                        @endif
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

                        {{-- Featured Image Section --}}
                        @if($post->featured_image)
                            <div class="mb-4">
                                <h6 class="text-muted">Featured Image</h6>
                                <div class="text-center">
                                    <img src="{{ $post->featured_image_url }}"
                                        alt="{{ $post->featured_image_alt ?? $post->title }}"
                                        class="img-fluid rounded shadow-sm" style="max-height: 400px; object-fit: cover;">
                                    @if($post->featured_image_alt)
                                        <p class="text-muted small mt-2">{{ $post->featured_image_alt }}</p>
                                    @endif
                                </div>
                            </div>
                        @endif

                        {{-- Gallery Images Section --}}
                        @if($post->gallery_images && count($post->gallery_images) > 0)
                            <div class="mb-4">
                                <h6 class="text-muted">Gallery Images ({{ count($post->gallery_images) }})</h6>
                                <div class="row g-2">
                                    @foreach($post->gallery_images as $index => $image)
                                        <div class="col-md-3 col-sm-4 col-6">
                                            <div class="position-relative">
                                                <img src="{{ $post->getGalleryImageUrl($image) }}"
                                                    alt="Gallery image {{ $index + 1 }}"
                                                    class="img-fluid rounded shadow-sm gallery-image"
                                                    style="height: 150px; width: 100%; object-fit: cover; cursor: pointer;"
                                                    data-bs-toggle="modal" data-bs-target="#galleryModal"
                                                    data-image-src="{{ $post->getGalleryImageUrl($image) }}"
                                                    data-image-index="{{ $index }}">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <h6 class="text-muted">Content</h6>
                        <div class="post-content border-start border-3 border-primary ps-4">
                            {!! nl2br(e($post->content)) !!}
                        </div>
                    </div>
                </div>

                {{-- Gallery Modal --}}
                @if($post->gallery_images && count($post->gallery_images) > 0)
                    <div class="modal fade" id="galleryModal" tabindex="-1" aria-labelledby="galleryModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="galleryModalLabel">Gallery Image</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body text-center">
                                    <img id="modalImage" src="" alt="Gallery image" class="img-fluid rounded">
                                    <div class="mt-3">
                                        <button type="button" class="btn btn-outline-secondary" id="prevImage">
                                            <i class="bi bi-chevron-left"></i> Previous
                                        </button>
                                        <span class="mx-3" id="imageCounter"></span>
                                        <button type="button" class="btn btn-outline-secondary" id="nextImage">
                                            Next <i class="bi bi-chevron-right"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            const galleryImages = @json(array_map(function ($image) use ($post) {
                            return $post->getGalleryImageUrl($image); }, $post->gallery_images));
                            let currentImageIndex = 0;

                            const modalImage = document.getElementById('modalImage');
                            const imageCounter = document.getElementById('imageCounter');
                            const prevBtn = document.getElementById('prevImage');
                            const nextBtn = document.getElementById('nextImage');

                            function updateModal(index) {
                                modalImage.src = galleryImages[index];
                                imageCounter.textContent = `${index + 1} of ${galleryImages.length}`;
                                prevBtn.disabled = index === 0;
                                nextBtn.disabled = index === galleryImages.length - 1;
                            }

                            document.querySelectorAll('.gallery-image').forEach((img, index) => {
                                img.addEventListener('click', function () {
                                    currentImageIndex = index;
                                    updateModal(currentImageIndex);
                                });
                            });

                            prevBtn.addEventListener('click', function () {
                                if (currentImageIndex > 0) {
                                    currentImageIndex--;
                                    updateModal(currentImageIndex);
                                }
                            });

                            nextBtn.addEventListener('click', function () {
                                if (currentImageIndex < galleryImages.length - 1) {
                                    currentImageIndex++;
                                    updateModal(currentImageIndex);
                                }
                            });

                            // Keyboard navigation
                            document.addEventListener('keydown', function (e) {
                                if (document.getElementById('galleryModal').classList.contains('show')) {
                                    if (e.key === 'ArrowLeft' && currentImageIndex > 0) {
                                        currentImageIndex--;
                                        updateModal(currentImageIndex);
                                    } else if (e.key === 'ArrowRight' && currentImageIndex < galleryImages.length - 1) {
                                        currentImageIndex++;
                                        updateModal(currentImageIndex);
                                    }
                                }
                            });
                        });
                    </script>
                @endif
            </div>
        </div>
    </div>
@endsection