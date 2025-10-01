<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use App\Models\User;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::with(['user', 'category']);
        
        if ($request->get('show') === 'trashed') {
            $query->onlyTrashed();
        }
        
        $posts = $query->orderBy('created_at', 'desc')->paginate(10);
        $trashedCount = Post::onlyTrashed()->count();
        
        if ($request->route()->getName() === 'admin.dashboard') {
            return view('admin.dashboard', compact('posts', 'trashedCount'));
        }
        
        return view('admin.list-post', compact('posts', 'trashedCount'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.add-post', compact('categories'));
    }

    public function store(StorePostRequest $request)
    {
        $validated = $request->validated();
        $validated['user_id'] = auth()->id();
        
        Post::create($validated);

        return redirect()->route('admin.posts.index')
            ->with('success', 'Post created successfully!');
    }

    public function edit(Post $post)
    {
        $categories = Category::all();
        return view('admin.edit-post', compact('post', 'categories'));
    }

    public function update(UpdatePostRequest $request, Post $post)
    {
        $post->update($request->validated());

        return redirect()->route('admin.posts.index')
            ->with('success', 'Post updated successfully!');
    }

    public function show(Post $post)
    {
        $post->load(['user', 'category', 'comments.user']);
        return view('admin.show-post', compact('post'));
    }

    public function destroy(Post $post)
    {
        $post->delete();
        
        return redirect()->route('admin.posts.index')
            ->with('success', 'Post moved to trash successfully!');
    }

    public function restore($id)
    {
        $post = Post::onlyTrashed()->findOrFail($id);
        $post->restore();
        
        return redirect()->route('admin.posts.index')
            ->with('success', 'Post restored successfully!');
    }

    public function forceDelete($id)
    {
        $post = Post::onlyTrashed()->findOrFail($id);
        $post->forceDelete();
        
        return redirect()->route('admin.posts.index', ['show' => 'trashed'])
            ->with('success', 'Post permanently deleted!');
    }
    public function users(Request $request)
    {
        $query = User::query();
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        if ($request->has('role') && $request->role) {
            $query->where('role', $request->role);
        }
        
        $users = $query->withCount('posts')
                      ->orderBy('created_at', 'desc')
                      ->paginate(10);
        
        return view('admin.users.index', compact('users'));
    }

    public function createUser()
    {
        return view('admin.users.create');
    }
    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:user,admin',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully!');
    }

    public function editUser(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }
    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:user,admin',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
        ];
        if ($validated['password']) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully!');
    }
    public function destroyUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'You cannot delete your own account!');
        }
        $user->posts()->delete();        
        $user->delete();
        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully!');
    }
}
