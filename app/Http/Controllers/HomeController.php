<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::where('is_active', true)
            ->orderBy('order')
            ->get();

        $featured = Product::where('is_active', true)
            ->where('is_featured', true)
            ->inStock()
            ->with(['category', 'primaryImage', 'user'])
            ->latest()
            ->limit(12)
            ->get();

        return view('home', compact('categories', 'featured'));
    }

    public function category(Request $request, $slug)
    {
        $category = Category::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        // Get sub-categories with product count
        $subCategories = SubCategory::where('category_id', $category->id)
            ->where('is_active', true)
            ->orderBy('order')
            ->withCount('products')
            ->get();

        // Base query
        $query = Product::where('category_id', $category->id)
            ->where('is_active', true)
            ->inStock()
            ->with(['category', 'subCategory', 'primaryImage', 'user']);

        // Filter by sub-category
        if ($request->filled('sub_category')) {
            $query->where('sub_category_id', $request->sub_category);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('brand', 'like', "%{$search}%");
            });
        }

        // Price range filter
        if ($request->filled('price_range')) {
            [$minPrice, $maxPrice] = explode('-', $request->price_range);
            if ($maxPrice === '' || $maxPrice === null) {
                $query->where('price', '>=', (int)$minPrice);
            } else {
                $query->whereBetween('price', [(int)$minPrice, (int)$maxPrice]);
            }
        }

        // Sorting
        $sort = $request->input('sort', 'featured');
        switch ($sort) {
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'rating':
                $query->orderBy('average_rating', 'desc');
                break;
            case 'popular':
                $query->orderBy('views', 'desc');
                break;
            case 'featured':
            default:
                $query->orderBy('is_featured', 'desc')
                    ->latest();
        }

        $products = $query->paginate(12);

        return view('category.show', compact('category', 'subCategories', 'products'));
    }

    public function product($slug)
    {
        $product = Product::where('slug', $slug)
            ->where('is_active', true)
            ->with([
                'category',
                'subCategory',
                'images' => function ($query) {
                    $query->orderBy('order');
                },
                'user',
                'reviews' => function ($query) {
                    $query->where('is_approved', true)->latest();
                }
            ])
            ->firstOrFail();

        // Increment views
        $product->incrementViews();

        // Get related products
        $related = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->inStock()
            ->with(['category', 'primaryImage', 'user'])
            ->latest()
            ->limit(8)
            ->get();

        // Check if wishlisted
        $isWishlisted = false;
        if (auth()->check()) {
            $isWishlisted = $product->wishlists()
                ->where('user_id', auth()->id())
                ->exists();
        }

        return view('product.show', compact('product', 'related', 'isWishlisted'));
    }

    public function search(Request $request)
    {
        $query = request('query');

        $products = Product::where('is_active', true)
            ->inStock()
            ->search($query)
            ->with(['category', 'subCategory', 'primaryImage', 'user'])
            ->orderBy('is_featured', 'desc')
            ->paginate(12);

        return view('search.results', compact('query', 'products'));
    }
}