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

                <div class="d-flex align-items-center mb-4">
                    <h2 class="mb-0">
                        <i class="bi bi-plus-circle me-2"></i>Submit New Post
                    </h2>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="alert alert-info" role="alert">
                            <i class="bi bi-info-circle me-2"></i>
                            Your post will be reviewed by an admin before being published.
                        </div>

                        <form action="{{ route('user.posts.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="mb-3">
                                <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" id="title"
                                    name="title" value="{{ old('title') }}"
                                    placeholder="Enter an engaging title for your post" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="category_id" class="form-label">Category</label>
                                <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id">
                                    <option value="">Select a category (optional)</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Choose a category that best fits your post</div>
                            </div>

                            <div class="mb-3">
                                <label for="author" class="form-label">Author Name</label>
                                <input type="text" class="form-control @error('author') is-invalid @enderror" id="author"
                                    name="author" value="{{ old('author') }}" placeholder="Your name (optional)">
                                @error('author')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Leave blank to post anonymously</div>
                            </div>

                            <!-- Featured Image Section -->
                            <div class="mb-3">
                                <label for="featured_image" class="form-label">
                                    <i class="bi bi-image me-1"></i>Featured Image
                                </label>
                                <input type="file" class="form-control @error('featured_image') is-invalid @enderror" 
                                    id="featured_image" name="featured_image" accept="image/*">
                                @error('featured_image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Upload a featured image for your post (max 5MB, formats: JPG, PNG, GIF, WebP)</div>
                                
                                <!-- Featured Image Preview -->
                                <div id="featured_image_preview" class="mt-2" style="display: none;">
                                    <img id="featured_preview_img" src="" alt="Featured Image Preview" 
                                        class="img-thumbnail" style="max-width: 300px; max-height: 200px;">
                                    <div class="mt-2">
                                        <label for="featured_image_alt" class="form-label">Alt Text</label>
                                        <input type="text" class="form-control @error('featured_image_alt') is-invalid @enderror" 
                                            id="featured_image_alt" name="featured_image_alt" value="{{ old('featured_image_alt') }}"
                                            placeholder="Describe the image for accessibility">
                                        @error('featured_image_alt')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Gallery Images Section -->
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="bi bi-images me-1"></i>Gallery Images
                                </label>
                                
                                <!-- Add Images Button -->
                                <div class="d-flex gap-2 mb-2">
                                    <input type="file" class="form-control @error('gallery_images.*') is-invalid @enderror" 
                                        id="gallery_images" name="gallery_images[]" accept="image/*" multiple>
                                    <button type="button" class="btn btn-outline-secondary" id="clear_gallery" style="display: none;">
                                        <i class="bi bi-trash me-1"></i>Clear All
                                    </button>
                                </div>
                                
                                @error('gallery_images.*')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Select multiple images at once (max 5MB each, up to 10 images total)</div>
                                
                                <!-- Gallery Preview -->
                                <div id="gallery_preview" class="mt-2 row g-2" style="display: none;"></div>
                            </div>

                            <div class="mb-4">
                                <label for="content" class="form-label">Content <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('content') is-invalid @enderror" id="content"
                                    name="content" rows="10" placeholder="Write your post content here..."
                                    required>{{ old('content') }}</textarea>
                                @error('content')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-send me-1"></i>Submit Post
                                </button>
                                <button type="reset" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-clockwise me-1"></i>Reset
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Featured Image Preview
    const featuredImageInput = document.getElementById('featured_image');
    const featuredImagePreview = document.getElementById('featured_image_preview');
    const featuredPreviewImg = document.getElementById('featured_preview_img');

    featuredImageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                featuredPreviewImg.src = e.target.result;
                featuredImagePreview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            featuredImagePreview.style.display = 'none';
        }
    });

    // Gallery Images Preview
    const galleryImagesInput = document.getElementById('gallery_images');
    const galleryPreview = document.getElementById('gallery_preview');
    const clearGalleryBtn = document.getElementById('clear_gallery');

    galleryImagesInput.addEventListener('change', function(e) {
        const files = Array.from(e.target.files);
        galleryPreview.innerHTML = '';
        
        if (files.length > 0) {
            // Validate files
            for (let file of files) {
                if (!validateFileSize([file], 5)) {
                    e.target.value = '';
                    return;
                }
            }
            
            if (files.length > 10) {
                alert('Maximum 10 images allowed in gallery');
                e.target.value = '';
                return;
            }
            
            galleryPreview.style.display = 'block';
            clearGalleryBtn.style.display = 'inline-block';
            
            files.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const col = document.createElement('div');
                    col.className = 'col-md-3 col-sm-4 col-6';
                    col.innerHTML = `
                        <div class="position-relative">
                            <img src="${e.target.result}" alt="Gallery Preview ${index + 1}" 
                                class="img-thumbnail w-100" style="height: 120px; object-fit: cover;">
                            <small class="text-muted d-block text-center mt-1">${file.name}</small>
                        </div>
                    `;
                    galleryPreview.appendChild(col);
                };
                reader.readAsDataURL(file);
            });
        } else {
            galleryPreview.style.display = 'none';
            clearGalleryBtn.style.display = 'none';
        }
    });

    clearGalleryBtn.addEventListener('click', function() {
        if (confirm('Remove all gallery images?')) {
            galleryImagesInput.value = '';
            galleryPreview.innerHTML = '';
            galleryPreview.style.display = 'none';
            clearGalleryBtn.style.display = 'none';
        }
    });

    // File size validation
    function validateFileSize(files, maxSizeMB = 5) {
        for (let i = 0; i < files.length; i++) {
            if (files[i].size > maxSizeMB * 1024 * 1024) {
                alert(`File "${files[i].name}" is too large. Maximum size is ${maxSizeMB}MB.`);
                return false;
            }
        }
        return true;
    }

    featuredImageInput.addEventListener('change', function() {
        validateFileSize(this.files);
    });
});
</script>
@endpush