<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index()
    {
        $subCategories = SubCategory::with('category')
            ->orderBy('category_id')
            ->orderBy('order')
            ->paginate(20);

        return view('super-admin.sub-categories.index', compact('subCategories'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)
            ->orderBy('order')
            ->get();

        return view('super-admin.sub-categories.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255|unique:sub_categories',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:20',
            'order' => 'nullable|integer|min:0',
        ]);

        SubCategory::create([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'icon' => $request->icon ?? 'fa-box',
            'color' => $request->color ?? '#4CAF50',
            'order' => $request->order ?? 0,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('super-admin.sub-categories.index')
            ->with('success', 'Sub-category created successfully!');
    }

    public function edit(SubCategory $subCategory)
    {
        $categories = Category::where('is_active', true)
            ->orderBy('order')
            ->get();

        return view('super-admin.sub-categories.edit', compact('subCategory', 'categories'));
    }

    public function update(Request $request, SubCategory $subCategory)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255|unique:sub_categories,name,' . $subCategory->id,
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:20',
            'order' => 'nullable|integer|min:0',
        ]);

        $subCategory->update([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'icon' => $request->icon ?? 'fa-box',
            'color' => $request->color ?? '#4CAF50',
            'order' => $request->order ?? 0,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('super-admin.sub-categories.index')
            ->with('success', 'Sub-category updated successfully!');
    }

    public function destroy(SubCategory $subCategory)
    {
        if ($subCategory->products()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete sub-category with existing products! Please reassign or delete the products first.');
        }

        $subCategory->delete();

        return redirect()->route('super-admin.sub-categories.index')
            ->with('success', 'Sub-category deleted successfully!');
    }

    public function show(SubCategory $subCategory)
    {
        $subCategory->load(['category', 'products.primaryImage', 'products.user']);

        return view('super-admin.sub-categories.show', compact('subCategory'));
    }

    // API endpoint to get sub-categories by category
    public function getByCategory($categoryId)
    {
        $subCategories = SubCategory::where('category_id', $categoryId)
            ->where('is_active', true)
            ->orderBy('order')
            ->get(['id', 'name', 'slug']);

        return $subCategories;
    }
}