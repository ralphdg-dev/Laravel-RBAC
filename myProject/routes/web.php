<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/dashboard', function () {
    if (auth()->user()->isAdmin()) {
        return redirect()->route('admin.posts.index');
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
        return redirect()->route('admin.posts.index');
    });
    
    Route::get('/posts', [AdminController::class, 'index'])->name('posts.index');
    Route::get('/posts/create', [AdminController::class, 'create'])->name('posts.create');
    Route::post('/posts', [AdminController::class, 'store'])->name('posts.store');
    Route::get('/posts/{post}', [AdminController::class, 'show'])->name('posts.show');
    Route::get('/posts/{post}/edit', [AdminController::class, 'edit'])->name('posts.edit');
    Route::put('/posts/{post}', [AdminController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post}', [AdminController::class, 'destroy'])->name('posts.destroy');
    
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

Route::middleware(['auth', 'role:user'])->prefix('user')->name('user.')->group(function () {
    // User dashboard
    Route::get('/dashboard', [UserController::class, 'index'])->name('dashboard');
    Route::get('/posts/{post}', [UserController::class, 'show'])->name('posts.show');
    Route::get('/submit-post', [UserController::class, 'submit'])->name('submit');
    Route::post('/submit-post', [UserController::class, 'store'])->name('posts.store');
    
    Route::get('/post/{post?}', function ($post = null) {
        if ($post) {
            return redirect()->route('user.posts.show', $post);
        }
        return redirect()->route('user.dashboard');
    })->name('post');
});


Route::get('/blog', function () {
    $posts = \App\Models\Post::where('status', 'approved')
        ->orderBy('created_at', 'desc')
        ->paginate(12);
    return view('public.blog', compact('posts'));
})->name('public.blog');

Route::get('/blog/{post}', function (\App\Models\Post $post) {
    if ($post->status !== 'approved') {
        abort(404);
    }
    return view('public.post', compact('post'));
})->name('public.post.show');

require __DIR__ . '/auth.php';