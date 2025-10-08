<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'user', 'primaryImage']);

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filter by vendor
        if ($request->filled('vendor')) {
            $query->where('user_id', $request->vendor);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('brand', 'like', "%{$search}%");
            });
        }

        $products = $query->latest()->paginate(20);

        return view('super-admin.products.index', compact('products'));
    }

    public function show(Product $product)
    {
        $product->load(['category', 'user', 'images', 'reviews.user']);

        return view('super-admin.products.show', compact('product'));
    }

    public function toggleStatus(Product $product)
    {
        $product->update(['is_active' => !$product->is_active]);

        $status = $product->is_active ? 'activated' : 'deactivated';

        return redirect()->back()
            ->with('success', "Product {$status} successfully!");
    }

    public function toggleFeatured(Product $product)
    {
        $product->update(['is_featured' => !$product->is_featured]);

        $status = $product->is_featured ? 'marked as featured' : 'removed from featured';

        return redirect()->back()
            ->with('success', "Product {$status} successfully!");
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('super-admin.products.index')
            ->with('success', 'Product deleted successfully!');
    }
}
