<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\PostController as AdminPostController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/dashboard', function () {
    if (auth()->user()->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('user.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        return redirect()->route('admin.dashboard');
    });
    
    // Dashboard route
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
    
    // Posts routes
    Route::get('/posts', [AdminPostController::class, 'index'])->name('posts.index');
    Route::get('/posts/create', [AdminPostController::class, 'create'])->name('posts.create');
    Route::post('/posts', [AdminPostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{post}', [AdminPostController::class, 'show'])->name('posts.show');
    Route::get('/posts/{post}/edit', [AdminPostController::class, 'edit'])->name('posts.edit');
    Route::put('/posts/{post}', [AdminPostController::class, 'update'])->name('posts.update');
    Route::patch('/posts/{post}/status', [AdminPostController::class, 'updateStatus'])->name('posts.updateStatus');
    Route::delete('/posts/{post}', [AdminPostController::class, 'destroy'])->name('posts.destroy');
    
    // Soft delete routes for posts
    Route::post('/posts/{id}/restore', [AdminPostController::class, 'restore'])->name('posts.restore');
    Route::delete('/posts/{id}/force-delete', [AdminPostController::class, 'forceDelete'])->name('posts.force-delete');
    
    // Categories routes
    Route::resource('categories', CategoryController::class);
    Route::post('/categories/{id}/restore', [CategoryController::class, 'restore'])->name('categories.restore');
    Route::delete('/categories/{id}/force-delete', [CategoryController::class, 'forceDelete'])->name('categories.force-delete');
    
    // User Management routes
    Route::get('/users', [AdminController::class, 'users'])->name('users.index');
    Route::get('/users/create', [AdminController::class, 'createUser'])->name('users.create');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
    Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('users.edit');
    Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');
    
    // Legacy redirects
    Route::get('/list-post', function () {
        return redirect()->route('admin.posts.index');
    })->name('list');
    
    Route::get('/add-post', function () {
        return redirect()->route('admin.posts.create');
    })->name('add');
    
    Route::get('/edit-post/{post}', function ($post) {
        return redirect()->route('admin.posts.edit', $post);
    })->name('edit');
});

// Comment routes (authenticated users)
Route::middleware('auth')->group(function () {
    Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::put('/comments/{comment}', [CommentController::class, 'update'])->name('comments.update');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
    
    // Admin-only comment management
    Route::middleware('role:admin')->group(function () {
        Route::post('/comments/{id}/restore', [CommentController::class, 'restore'])->name('comments.restore');
        Route::delete('/comments/{id}/force-delete', [CommentController::class, 'forceDelete'])->name('comments.force-delete');
    });
});

Route::middleware(['auth', 'role:user'])->prefix('user')->name('user.')->group(function () {
    // User dashboard
    Route::get('/dashboard', [UserController::class, 'index'])->name('dashboard');
    Route::get('/posts/{post}', [UserController::class, 'show'])->name('posts.show');
    Route::get('/submit-post', [UserController::class, 'submit'])->name('submit');
    Route::post('/submit-post', [UserController::class, 'store'])->name('posts.store');
    
    // User post management
    Route::get('/posts/{post}/edit', [UserController::class, 'edit'])->name('posts.edit');
    Route::put('/posts/{post}', [UserController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post}', [UserController::class, 'destroy'])->name('posts.destroy');
    
    Route::get('/post/{post?}', function ($post = null) {
        if ($post) {
            return redirect()->route('user.posts.show', $post);
        }
        return redirect()->route('user.dashboard');
    })->name('post');
});


Route::get('/blog', function () {
    $query = \App\Models\Post::where('status', 'approved')
        ->with(['category', 'user']);
    
    // Filter by category if provided
    if (request('category')) {
        $query->where('category_id', request('category'));
    }
    
    $posts = $query->orderBy('created_at', 'desc')->paginate(12);
    
    return view('public.blog', compact('posts'));
})->name('public.blog');

Route::get('/blog/{post}', function (\App\Models\Post $post) {
    if ($post->status !== 'approved') {
        abort(404);
    }
    return view('public.post', compact('post'));
})->name('public.post.show');

require __DIR__ . '/auth.php';