<?php

namespace App\Services;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\Encoders\JpegEncoder;
use Intervention\Image\Encoders\PngEncoder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageService
{
    protected $manager;
    protected $disk;
    protected $path;

    public function __construct()
    {
        $this->manager = new ImageManager(new Driver());
        $this->disk = 'public';
        $this->path = 'posts';
    }

    /**
     * Upload and process a single image
     */
    public function uploadImage(UploadedFile $file, array $options = []): string
    {
        $options = array_merge([
            'width' => 800,
            'height' => 600,
            'quality' => 85,
            'format' => 'webp'
        ], $options);

        // Generate unique filename
        $filename = $this->generateFilename($file, $options['format']);
        $fullPath = $this->path . '/' . $filename;

        // Process image
        $image = $this->manager->read($file->getPathname());
        
        // Resize while maintaining aspect ratio
        $image->scale(width: $options['width'], height: $options['height']);
        
        // Encode to specified format with quality
        $encoded = match($options['format']) {
            'webp' => $image->encode(new WebpEncoder($options['quality'])),
            'jpeg', 'jpg' => $image->encode(new JpegEncoder($options['quality'])),
            'png' => $image->encode(new PngEncoder()),
            default => $image->encode(new WebpEncoder($options['quality']))
        };
        
        // Store the processed image
        Storage::disk($this->disk)->put($fullPath, $encoded);

        return $fullPath;
    }

    /**
     * Upload multiple images (gallery)
     */
    public function uploadMultipleImages(array $files, array $options = []): array
    {
        $uploadedPaths = [];
        
        foreach ($files as $file) {
            if ($file instanceof UploadedFile && $file->isValid()) {
                $uploadedPaths[] = $this->uploadImage($file, $options);
            }
        }
        
        return $uploadedPaths;
    }

    /**
     * Create thumbnail version of an image
     */
    public function createThumbnail(string $imagePath, array $options = []): string
    {
        $options = array_merge([
            'width' => 300,
            'height' => 200,
            'quality' => 80,
            'suffix' => '_thumb'
        ], $options);

        $pathInfo = pathinfo($imagePath);
        $thumbnailPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . $options['suffix'] . '.' . $pathInfo['extension'];

        if (Storage::disk($this->disk)->exists($imagePath)) {
            $imageContent = Storage::disk($this->disk)->get($imagePath);
            $image = $this->manager->read($imageContent);
            
            $image->scale(width: $options['width'], height: $options['height']);
            
            $extension = pathinfo($imagePath, PATHINFO_EXTENSION);
            $encoded = match($extension) {
                'webp' => $image->encode(new WebpEncoder($options['quality'])),
                'jpeg', 'jpg' => $image->encode(new JpegEncoder($options['quality'])),
                'png' => $image->encode(new PngEncoder()),
                default => $image->encode(new WebpEncoder($options['quality']))
            };
            
            Storage::disk($this->disk)->put($thumbnailPath, $encoded);
            
            return $thumbnailPath;
        }

        throw new \Exception('Original image not found: ' . $imagePath);
    }

    /**
     * Delete image and its thumbnail
     */
    public function deleteImage(string $imagePath): bool
    {
        $deleted = true;
        
        // Delete main image
        if (Storage::disk($this->disk)->exists($imagePath)) {
            $deleted = Storage::disk($this->disk)->delete($imagePath);
        }
        
        // Delete thumbnail if exists
        $pathInfo = pathinfo($imagePath);
        $thumbnailPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '_thumb.' . $pathInfo['extension'];
        
        if (Storage::disk($this->disk)->exists($thumbnailPath)) {
            Storage::disk($this->disk)->delete($thumbnailPath);
        }
        
        return $deleted;
    }

    /**
     * Delete multiple images
     */
    public function deleteMultipleImages(array $imagePaths): bool
    {
        $allDeleted = true;
        
        foreach ($imagePaths as $path) {
            if (!$this->deleteImage($path)) {
                $allDeleted = false;
            }
        }
        
        return $allDeleted;
    }

    /**
     * Validate image file
     */
    public function validateImage(UploadedFile $file): bool
    {
        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        
        return in_array($file->getMimeType(), $allowedMimes) && 
               $file->getSize() <= $maxSize &&
               $file->isValid();
    }

    /**
     * Generate unique filename
     */
    protected function generateFilename(UploadedFile $file, ?string $format = null): string
    {
        $format = $format ?: $file->getClientOriginalExtension();
        $name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeName = Str::slug($name);
        
        return $safeName . '_' . time() . '_' . Str::random(8) . '.' . $format;
    }

    /**
     * Get image dimensions
     */
    public function getImageDimensions(string $imagePath): array
    {
        if (Storage::disk($this->disk)->exists($imagePath)) {
            $imageContent = Storage::disk($this->disk)->get($imagePath);
            $image = $this->manager->read($imageContent);
            
            return [
                'width' => $image->width(),
                'height' => $image->height()
            ];
        }
        
        return ['width' => 0, 'height' => 0];
    }
}
