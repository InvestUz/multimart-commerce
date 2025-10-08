<?php
// ============================================
// CONTROLLER 1: HomeController
// File: app/Http/Controllers/HomeController.php
// ============================================

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::active()
            ->withCount('products')
            ->orderBy('order')
            ->get();

        $featuredProducts = Product::with(['primaryImage', 'category', 'user'])
            ->active()
            ->featured()
            ->inStock()
            ->latest()
            ->take(12)
            ->get();

        return view('home', compact('categories', 'featuredProducts'));
    }

    public function category($slug)
    {
        $category = Category::where('slug', $slug)
            ->active()
            ->firstOrFail();

        $products = Product::with(['primaryImage', 'category', 'user'])
            ->where('category_id', $category->id)
            ->active()
            ->inStock()
            ->paginate(12);

        return view('category', compact('category', 'products'));
    }

    public function product($slug)
    {
        $product = Product::with(['images', 'category', 'user', 'reviews' => function ($query) {
            $query->where('is_approved', true)->latest();
        }, 'reviews.user'])
            ->where('slug', $slug)
            ->firstOrFail();

        // Increment views
        $product->increment('views');

        // Check if user has this in wishlist
        $inWishlist = false;
        if (auth()->check()) {
            $inWishlist = auth()->user()->wishlists()
                ->where('product_id', $product->id)
                ->exists();
        }

        // Get related products
        $relatedProducts = Product::with(['primaryImage'])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->active()
            ->inStock()
            ->take(4)
            ->get();

        return view('product', compact('product', 'relatedProducts', 'inWishlist'));
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $categoryId = $request->input('category');
        $minPrice = $request->input('min_price');
        $maxPrice = $request->input('max_price');
        $sortBy = $request->input('sort_by', 'latest');

        $productsQuery = Product::with(['primaryImage', 'category', 'user'])
            ->active()
            ->inStock();

        // Search by query
        if ($query) {
            $productsQuery->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%")
                    ->orWhere('brand', 'like', "%{$query}%");
            });
        }

        // Filter by category
        if ($categoryId) {
            $productsQuery->where('category_id', $categoryId);
        }

        // Filter by price range
        if ($minPrice) {
            $productsQuery->where('price', '>=', $minPrice);
        }
        if ($maxPrice) {
            $productsQuery->where('price', '<=', $maxPrice);
        }

        // Sort products
        switch ($sortBy) {
            case 'price_low':
                $productsQuery->orderBy('price', 'asc');
                break;
            case 'price_high':
                $productsQuery->orderBy('price', 'desc');
                break;
            case 'name':
                $productsQuery->orderBy('name', 'asc');
                break;
            case 'popular':
                $productsQuery->orderBy('views', 'desc');
                break;
            case 'rating':
                $productsQuery->orderBy('average_rating', 'desc');
                break;
            default:
                $productsQuery->latest();
        }

        $products = $productsQuery->paginate(12);
        $categories = Category::active()->orderBy('name')->get();

        return view('search', compact('products', 'query', 'categories', 'categoryId', 'minPrice', 'maxPrice', 'sortBy'));
    }
}
