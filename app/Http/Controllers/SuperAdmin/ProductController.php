<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['vendor', 'category', 'brand'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('vendor')) {
            $query->where('vendor_id', $request->vendor);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $products = $query->latest()->paginate(20);

        return view('super-admin.products.index', compact('products'));
    }

    public function show(Product $product)
    {
        $product->load([
            'vendor',
            'category',
            'subCategory',
            'brand',
            'images',
            'variants',
            'reviews.user'
        ]);

        return view('super-admin.products.show', compact('product'));
    }

    public function toggleStatus(Product $product)
    {
        $product->update(['is_active' => !$product->is_active]);

        return response()->json([
            'success' => true,
            'message' => 'Product status updated successfully!',
            'is_active' => $product->is_active
        ]);
    }

    public function toggleFeatured(Product $product)
    {
        $product->update(['is_featured' => !$product->is_featured]);

        return response()->json([
            'success' => true,
            'message' => 'Product featured status updated!',
            'is_featured' => $product->is_featured
        ]);
    }

    public function destroy(Product $product)
    {
        // Delete associated images from storage
        foreach ($product->images as $image) {
            \Storage::disk('public')->delete($image->image_path);
            $image->delete();
        }

        $product->delete();

        return redirect()->route('super-admin.products.index')
            ->with('success', 'Product deleted successfully!');
    }
}
