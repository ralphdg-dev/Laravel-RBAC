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

                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">
                            <i class="bi bi-pencil me-2"></i>Edit Post
                        </h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('user.posts.update', $post) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="title" class="form-label">Post Title</label>
                                <input type="text" 
                                       class="form-control @error('title') is-invalid @enderror" 
                                       id="title" 
                                       name="title" 
                                       value="{{ old('title', $post->title) }}" 
                                       required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="category_id" class="form-label">Category</label>
                                <select class="form-select @error('category_id') is-invalid @enderror" 
                                        id="category_id" 
                                        name="category_id">
                                    <option value="">Select a category (optional)</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" 
                                                {{ old('category_id', $post->category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="author" class="form-label">Author Name</label>
                                <input type="text" 
                                       class="form-control @error('author') is-invalid @enderror" 
                                       id="author" 
                                       name="author" 
                                       value="{{ old('author', $post->author) }}" 
                                       placeholder="Enter author name (optional)">
                                @error('author')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Current Featured Image -->
                            @if($post->featured_image)
                            <div class="mb-3">
                                <label class="form-label">Current Featured Image</label>
                                <div class="d-flex align-items-start gap-3">
                                    <img src="{{ $post->featured_image_url }}" alt="{{ $post->featured_image_alt }}" 
                                        class="img-thumbnail" style="max-width: 200px; max-height: 150px;">
                                    <div class="flex-grow-1">
                                        <p class="mb-2"><strong>Alt Text:</strong> {{ $post->featured_image_alt ?: 'No alt text' }}</p>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="remove_featured_image" 
                                                id="remove_featured_image" value="1">
                                            <label class="form-check-label text-danger" for="remove_featured_image">
                                                <i class="bi bi-trash me-1"></i>Remove Featured Image
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Featured Image Upload -->
                            <div class="mb-3">
                                <label for="featured_image" class="form-label">
                                    <i class="bi bi-image me-1"></i>{{ $post->featured_image ? 'Replace' : 'Add' }} Featured Image
                                </label>
                                <input type="file" class="form-control @error('featured_image') is-invalid @enderror" 
                                    id="featured_image" name="featured_image" accept="image/*">
                                @error('featured_image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Upload a featured image for your post (max 3MB, formats: JPG, PNG, GIF, WebP)</div>
                                
                                <!-- Featured Image Preview -->
                                <div id="featured_image_preview" class="mt-2" style="display: none;">
                                    <img id="featured_preview_img" src="" alt="Featured Image Preview" 
                                        class="img-thumbnail" style="max-width: 300px; max-height: 200px;">
                                    <div class="mt-2">
                                        <label for="featured_image_alt" class="form-label">Alt Text</label>
                                        <input type="text" class="form-control @error('featured_image_alt') is-invalid @enderror" 
                                            id="featured_image_alt" name="featured_image_alt" 
                                            value="{{ old('featured_image_alt', $post->featured_image_alt) }}"
                                            placeholder="Describe the image for accessibility">
                                        @error('featured_image_alt')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <!-- Featured Image Options -->
                                    <div class="mt-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" 
                                                id="featured_image_greyscale" name="featured_image_greyscale" value="1"
                                                {{ old('featured_image_greyscale') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="featured_image_greyscale">
                                                <i class="bi bi-palette me-1"></i>Apply greyscale filter
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Current Gallery Images -->
                            @if($post->gallery_images && count($post->gallery_images) > 0)
                            <div class="mb-3">
                                <label class="form-label">Current Gallery Images</label>
                                <div class="row g-2">
                                    @foreach($post->gallery_image_urls as $index => $imageUrl)
                                    <div class="col-md-3 col-sm-4 col-6">
                                        <div class="position-relative">
                                            <img src="{{ $imageUrl }}" alt="Gallery Image {{ $index + 1 }}" 
                                                class="img-thumbnail w-100" style="height: 120px; object-fit: cover;">
                                            <div class="form-check mt-1">
                                                <input class="form-check-input" type="checkbox" 
                                                    name="remove_gallery_images[]" value="{{ $post->gallery_images[$index] }}" 
                                                    id="remove_gallery_{{ $index }}">
                                                <label class="form-check-label text-danger small" for="remove_gallery_{{ $index }}">
                                                    <i class="bi bi-trash"></i> Remove
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            <!-- Gallery Images Upload -->
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="bi bi-images me-1"></i>Add Gallery Images
                                </label>
                                
                                <!-- Add Images Button -->
                                <div class="d-flex gap-2 mb-2">
                                    <input type="file" class="form-control @error('gallery_images.*') is-invalid @enderror" 
                                        id="gallery_images" name="gallery_images[]" accept="image/*" multiple>
                                    <button type="button" class="btn btn-outline-secondary" id="clear_new_gallery" style="display: none;">
                                        <i class="bi bi-trash me-1"></i>Clear New Images
                                    </button>
                                </div>
                                
                                @error('gallery_images.*')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <strong>To select multiple images:</strong> Hold Ctrl (Windows) or Cmd (Mac) while clicking, or select all images at once in the file dialog. 
                                    Add new images to the gallery (max 3MB each, up to 10 total images).
                                </div>
                                
                                <!-- Gallery Image Options -->
                                <div class="mt-2" id="gallery_options" style="display: none;">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                            id="gallery_images_greyscale" name="gallery_images_greyscale" value="1"
                                            {{ old('gallery_images_greyscale') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="gallery_images_greyscale">
                                            <i class="bi bi-palette me-1"></i>Apply greyscale filter to new gallery images
                                        </label>
                                    </div>
                                </div>
                                
                                <!-- New Gallery Preview -->
                                <div id="gallery_preview" class="mt-2 row g-2" style="display: none;"></div>
                            </div>

                            <div class="mb-3">
                                <label for="content" class="form-label">Post Content</label>
                                <textarea class="form-control @error('content') is-invalid @enderror" 
                                          id="content" 
                                          name="content" 
                                          rows="8" 
                                          required>{{ old('content', $post->content) }}</textarea>
                                @error('content')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>Note:</strong> After updating, your post will be set to "pending" status and will need to be reviewed by an admin before it appears publicly.
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('user.dashboard') }}" class="btn btn-secondary">
                                    <i class="bi bi-x-circle me-1"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle me-1"></i>Update Post
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
    const clearNewGalleryBtn = document.getElementById('clear_new_gallery');

    galleryImagesInput.addEventListener('change', function(e) {
        const files = Array.from(e.target.files);
        const galleryOptions = document.getElementById('gallery_options');
        galleryPreview.innerHTML = '';
        
        if (files.length > 0) {
            // Validate files
            for (let file of files) {
                if (file.size > 3 * 1024 * 1024) {
                    alert(`File "${file.name}" is too large. Maximum size is 3MB.`);
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
            galleryOptions.style.display = 'block';
            clearNewGalleryBtn.style.display = 'inline-block';
            
            files.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const col = document.createElement('div');
                    col.className = 'col-md-3 col-sm-4 col-6';
                    col.innerHTML = `
                        <div class="position-relative">
                            <img src="${e.target.result}" alt="New Gallery Preview ${index + 1}" 
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
            galleryOptions.style.display = 'none';
            clearNewGalleryBtn.style.display = 'none';
        }
    });

    clearNewGalleryBtn.addEventListener('click', function() {
        if (confirm('Remove all new gallery images?')) {
            const galleryOptions = document.getElementById('gallery_options');
            galleryImagesInput.value = '';
            galleryPreview.innerHTML = '';
            galleryPreview.style.display = 'none';
            galleryOptions.style.display = 'none';
            clearNewGalleryBtn.style.display = 'none';
        }
    });

    // File size validation
    function validateFileSize(files, maxSizeMB = 3) {
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
