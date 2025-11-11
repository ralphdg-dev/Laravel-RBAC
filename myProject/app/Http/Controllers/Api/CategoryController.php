<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories (public)
     */
    public function index(Request $request)
    {
        $query = Category::withCount('posts');

        // Include trashed categories if requested (admin only)
        if ($request->get('include_trashed') && auth()->check() && auth()->user()->isAdmin()) {
            $query->withTrashed();
        }

        $categories = $query->orderBy('name')->get();

        return CategoryResource::collection($categories);
    }

    /**
     * Display the specified category (public)
     */
    public function show(Category $category)
    {
        $category->loadCount('posts');

        return new CategoryResource($category);
    }

    /**
     * Store a newly created category (admin only)
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
            'slug' => 'required|string|max:255|unique:categories,slug',
        ]);

        $category = Category::create($request->all());

        return new CategoryResource($category);
    }

    /**
     * Update the specified category (admin only)
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
            'slug' => 'required|string|max:255|unique:categories,slug,' . $category->id,
        ]);

        $category->update($request->all());

        return new CategoryResource($category);
    }

    /**
     * Remove the specified category (admin only - soft delete)
     */
    public function destroy(Category $category)
    {
        // Check if category has posts
        if ($category->posts()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete category with existing posts.'
            ], 422);
        }

        $category->delete();

        return response()->json(['message' => 'Category deleted successfully.']);
    }

    /**
     * Restore the specified category (admin only)
     */
    public function restore($id)
    {
        $category = Category::withTrashed()->findOrFail($id);
        $category->restore();

        return response()->json(['message' => 'Category restored successfully.']);
    }

    /**
     * Permanently delete the specified category (admin only)
     */
    public function forceDelete($id)
    {
        $category = Category::withTrashed()->findOrFail($id);

        // Check if category has posts (including trashed)
        if ($category->posts()->withTrashed()->count() > 0) {
            return response()->json([
                'message' => 'Cannot permanently delete category with existing posts.'
            ], 422);
        }

        $category->forceDelete();

        return response()->json(['message' => 'Category permanently deleted.']);
    }
}
