<?php


namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;
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
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Filter by featured
        if ($request->filled('featured')) {
            if ($request->featured === 'yes') {
                $query->where('is_featured', true);
            } elseif ($request->featured === 'no') {
                $query->where('is_featured', false);
            }
        }

        // Filter by stock
        if ($request->filled('stock')) {
            if ($request->stock === 'in_stock') {
                $query->where('stock', '>', 0);
            } elseif ($request->stock === 'out_of_stock') {
                $query->where('stock', 0);
            } elseif ($request->stock === 'low_stock') {
                $query->where('stock', '>', 0)->where('stock', '<=', 10);
            }
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

        // Sort
        $sortBy = $request->input('sort_by', 'latest');
        switch ($sortBy) {
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'views':
                $query->orderBy('views', 'desc');
                break;
            case 'sales':
                $query->orderBy('total_sales', 'desc');
                break;
            default:
                $query->latest();
        }

        $products = $query->paginate(20)->withQueryString();

        $categories = Category::orderBy('name')->get();
        $vendors = User::where('role', 'vendor')->orderBy('name')->get();

        return view('super-admin.products.index', compact('products', 'categories', 'vendors'));
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
        // Check if product has orders
        if ($product->orderItems()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete product with existing orders! Please deactivate it instead.');
        }

        $productName = $product->name;
        $product->delete();

        return redirect()->route('super-admin.products.index')
            ->with('success', "Product '{$productName}' deleted successfully!");
    }
}
