<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Category::withCount('posts');
        
        if ($request->get('show') === 'trashed') {
            $query->onlyTrashed();
        }
        
        $categories = $query->orderBy('name')->paginate(15);
        $trashedCount = Category::onlyTrashed()->count();
        
        return view('admin.categories.index', compact('categories', 'trashedCount'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string|max:1000',
        ]);

        Category::create([
            'name' => $request->name,
            'description' => $request->description,
            'slug' => Str::slug($request->name),
        ]);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        $category->load(['posts' => function($query) {
            $query->with('user')->orderBy('created_at', 'desc');
        }]);
        
        return view('admin.categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string|max:1000',
        ]);

        $category->update([
            'name' => $request->name,
            'description' => $request->description,
            'slug' => Str::slug($request->name),
        ]);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category updated successfully!');
    }

    /**
     * Soft delete the specified resource.
     */
    public function destroy(Category $category)
    {
        $category->delete();
        
        return redirect()->route('admin.categories.index')
            ->with('success', 'Category moved to trash successfully!');
    }

    /**
     * Restore a soft deleted category.
     */
    public function restore($id)
    {
        $category = Category::onlyTrashed()->findOrFail($id);
        $category->restore();
        
        return redirect()->route('admin.categories.index')
            ->with('success', 'Category restored successfully!');
    }

    /**
     * Permanently delete a category.
     */
    public function forceDelete($id)
    {
        $category = Category::onlyTrashed()->findOrFail($id);
        $category->forceDelete();
        
        return redirect()->route('admin.categories.index', ['show' => 'trashed'])
            ->with('success', 'Category permanently deleted!');
    }
}
