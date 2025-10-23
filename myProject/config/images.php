<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Image Upload Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for image uploads and processing
    |
    */

    'disk' => env('IMAGE_DISK', 'public'),
    
    'path' => env('IMAGE_PATH', 'posts'),
    
    'max_file_size' => env('IMAGE_MAX_SIZE', 5 * 1024 * 1024), // 5MB
    
    'allowed_mimes' => [
        'image/jpeg',
        'image/png', 
        'image/gif',
        'image/webp'
    ],
    
    'allowed_extensions' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
    
    'featured_image' => [
        'width' => 800,
        'height' => 600,
        'quality' => 85,
        'format' => 'webp'
    ],
    
    'gallery_image' => [
        'width' => 600,
        'height' => 400,
        'quality' => 80,
        'format' => 'webp'
    ],
    
    'thumbnail' => [
        'width' => 300,
        'height' => 200,
        'quality' => 75,
        'suffix' => '_thumb'
    ],
    
    'max_gallery_images' => 10,
];
