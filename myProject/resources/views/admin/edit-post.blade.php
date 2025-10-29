@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">

            <div class="col-md-9">
                <div class="d-flex align-items-center mb-4">
                    <a href="{{ route('admin.posts.index') }}" class="btn btn-outline-secondary me-3">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                    <h2 class="mb-0">
                        <i class="bi bi-pencil me-2"></i>Edit Post
                    </h2>
                </div>

                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.posts.update', $post) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" id="title"
                                    name="title" value="{{ old('title', $post->title) }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="category_id" class="form-label">Category</label>
                                <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id">
                                    <option value="">Select a category (optional)</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id', $post->category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="author" class="form-label">Author</label>
                                <input type="text" class="form-control @error('author') is-invalid @enderror" id="author"
                                    name="author" value="{{ old('author', $post->author) }}">
                                @error('author')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <div class="d-flex gap-2 align-items-center">
                                    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status"
                                        required>
                                        <option value="pending" {{ old('status', $post->status) == 'pending' ? 'selected' : '' }}>
                                            Pending</option>
                                        <option value="approved" {{ old('status', $post->status) == 'approved' ? 'selected' : '' }}>Approved</option>
                                        <option value="rejected" {{ old('status', $post->status) == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                    </select>
                                    
                                    <!-- Quick Status Buttons -->
                                    <div class="btn-group" role="group">
                                        @if($post->status !== 'approved')
                                        <form action="{{ route('admin.posts.updateStatus', $post) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="approved">
                                            <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Approve this post?')" title="Quick Approve">
                                                <i class="bi bi-check-circle"></i>
                                            </button>
                                        </form>
                                        @endif
                                        
                                        @if($post->status !== 'rejected')
                                        <form action="{{ route('admin.posts.updateStatus', $post) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="rejected">
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Reject this post?')" title="Quick Reject">
                                                <i class="bi bi-x-circle"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </div>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Use the dropdown and "Update Post" button for full edit, or use quick action buttons for status-only changes.</div>
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
                                <div class="form-text">Upload a featured image for the post (max 3MB, formats: JPG, PNG, GIF, WebP)</div>
                                
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
                                <label for="gallery_images" class="form-label">
                                    <i class="bi bi-images me-1"></i>Add Gallery Images
                                </label>
                                <input type="file" class="form-control @error('gallery_images.*') is-invalid @enderror" 
                                    id="gallery_images" name="gallery_images[]" accept="image/*" multiple>
                                @error('gallery_images.*')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <strong>To select multiple images:</strong> Hold Ctrl (Windows) or Cmd (Mac) while clicking, or select all images at once in the file dialog. 
                                    Upload additional images for the gallery (max 3MB each, up to 10 total images).
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
                                
                                <!-- Gallery Preview -->
                                <div id="gallery_preview" class="mt-2 row g-2" style="display: none;"></div>
                            </div>

                            <div class="mb-4">
                                <label for="content" class="form-label">Content <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('content') is-invalid @enderror" id="content"
                                    name="content" rows="8" required>{{ old('content', $post->content) }}</textarea>
                                @error('content')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle me-1"></i>Update Post
                                </button>
                                <a href="{{ route('admin.posts.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-x-circle me-1"></i>Cancel
                                </a>
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

    galleryImagesInput.addEventListener('change', function(e) {
        const files = Array.from(e.target.files);
        const galleryOptions = document.getElementById('gallery_options');
        galleryPreview.innerHTML = '';
        
        if (files.length > 0) {
            // Validate files first
            for (let file of files) {
                if (file.size > 3 * 1024 * 1024) {
                    alert(`File "${file.name}" is too large. Maximum size is 3MB.`);
                    e.target.value = '';
                    galleryPreview.style.display = 'none';
                    galleryOptions.style.display = 'none';
                    return;
                }
            }
            
            if (files.length > 10) {
                alert('Maximum 10 images allowed in gallery');
                e.target.value = '';
                galleryPreview.style.display = 'none';
                galleryOptions.style.display = 'none';
                return;
            }
            galleryPreview.style.display = 'block';
            galleryOptions.style.display = 'block';
            
            files.forEach((file, index) => {
                if (index >= 10) return; // Limit to 10 images
                
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
        }
    });

    // File size validation is now handled inline in the event handlers

    featuredImageInput.addEventListener('change', function() {
        const files = this.files;
        for (let i = 0; i < files.length; i++) {
            if (files[i].size > 3 * 1024 * 1024) {
                alert(`File "${files[i].name}" is too large. Maximum size is 3MB.`);
                this.value = '';
                return;
            }
        }
    });
});
</script>
@endpush