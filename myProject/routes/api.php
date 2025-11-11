<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

// Public posts (approved posts only)
Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/{post}', [PostController::class, 'show']);

// Public categories
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{category}', [CategoryController::class, 'show']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/user', [AuthController::class, 'user']);
    
    // User posts management
    Route::prefix('user')->group(function () {
        Route::get('/posts', [PostController::class, 'userPosts']);
        Route::post('/posts', [PostController::class, 'store']);
        Route::get('/posts/{post}', [PostController::class, 'userShow']);
        Route::put('/posts/{post}', [PostController::class, 'update']);
        Route::delete('/posts/{post}', [PostController::class, 'destroy']);
    });
    
    // Comments
    Route::post('/posts/{post}/comments', [CommentController::class, 'store']);
    Route::put('/comments/{comment}', [CommentController::class, 'update']);
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);
    
    // Admin only routes
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        // Posts management
        Route::get('/posts', [PostController::class, 'adminIndex']);
        Route::post('/posts', [PostController::class, 'adminStore']);
        Route::get('/posts/{post}', [PostController::class, 'adminShow']);
        Route::put('/posts/{post}', [PostController::class, 'adminUpdate']);
        Route::delete('/posts/{post}', [PostController::class, 'adminDestroy']);
        Route::patch('/posts/{post}/status', [PostController::class, 'updateStatus']);
        
        // Soft delete management
        Route::post('/posts/{id}/restore', [PostController::class, 'restore']);
        Route::delete('/posts/{id}/force-delete', [PostController::class, 'forceDelete']);
        
        // Categories management
        Route::post('/categories', [CategoryController::class, 'store']);
        Route::put('/categories/{category}', [CategoryController::class, 'update']);
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);
        Route::post('/categories/{id}/restore', [CategoryController::class, 'restore']);
        Route::delete('/categories/{id}/force-delete', [CategoryController::class, 'forceDelete']);
        
        // Users management
        Route::get('/users', [UserController::class, 'index']);
        Route::post('/users', [UserController::class, 'store']);
        Route::get('/users/{user}', [UserController::class, 'show']);
        Route::put('/users/{user}', [UserController::class, 'update']);
        Route::delete('/users/{user}', [UserController::class, 'destroy']);
        
        // Comments management
        Route::get('/comments', [CommentController::class, 'adminIndex']);
        Route::post('/comments/{id}/restore', [CommentController::class, 'restore']);
        Route::delete('/comments/{id}/force-delete', [CommentController::class, 'forceDelete']);
    });
});
