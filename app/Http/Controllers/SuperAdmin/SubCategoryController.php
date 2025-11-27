<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SubCategory;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubCategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = SubCategory::with('category')
            ->withCount('products')
            ->orderBy('order');

        // Search filter
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $subCategories = $query->paginate(20)->withQueryString();

        return view('super-admin.sub-categories.index', compact('subCategories'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        return view('super-admin.sub-categories.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:sub_categories,slug',
            'description' => 'nullable|string|max:1000',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        if (!$request->filled('slug')) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        SubCategory::create($validated);

        return redirect()->route('super-admin.sub-categories.index')
            ->with('success', 'Sub-category created successfully!');
    }

    public function show(SubCategory $subCategory)
    {
        $subCategory->load(['category', 'products']);
        return view('super-admin.sub-categories.show', compact('subCategory'));
    }

    public function edit(SubCategory $subCategory)
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        return view('super-admin.sub-categories.edit', compact('subCategory', 'categories'));
    }

    public function update(Request $request, SubCategory $subCategory)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:sub_categories,slug,' . $subCategory->id,
            'description' => 'nullable|string|max:1000',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        if (!$request->filled('slug')) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $subCategory->update($validated);

        return redirect()->route('super-admin.sub-categories.index')
            ->with('success', 'Sub-category updated successfully!');
    }

    public function destroy(SubCategory $subCategory)
    {
        if ($subCategory->products()->count() > 0) {
            return back()->with('error', 'Cannot delete sub-category with existing products.');
        }

        $subCategory->delete();

        return redirect()->route('super-admin.sub-categories.index')
            ->with('success', 'Sub-category deleted successfully!');
    }

    public function getByCategory($categoryId)
    {
        $subCategories = SubCategory::where('category_id', $categoryId)
            ->where('is_active', true)
            ->orderBy('order')
            ->get(['id', 'name', 'slug']);

        return response()->json($subCategories);
    }
}
