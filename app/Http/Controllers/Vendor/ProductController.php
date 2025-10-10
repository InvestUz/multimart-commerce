<?php


namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::where('user_id', auth()->id())
            ->with(['category', 'primaryImage']);

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
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
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'stock_asc':
                $query->orderBy('stock', 'asc');
                break;
            case 'stock_desc':
                $query->orderBy('stock', 'desc');
                break;
            default:
                $query->latest();
        }

        $products = $query->paginate(15)->withQueryString();
        $categories = Category::active()->orderBy('name')->get();

        return view('vendor.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::active()->orderBy('name')->get();
        return view('vendor.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'old_price' => 'nullable|numeric|min:0|gte:price',
            'stock' => 'required|integer|min:0',
            'brand' => 'nullable|string|max:100',
            'model' => 'nullable|string|max:100',
            'condition' => 'nullable|in:new,used,refurbished',
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|string|max:100',
            'sizes' => 'nullable|array',
            'sizes.*' => 'string|max:50',
            'colors' => 'nullable|array',
            'colors.*' => 'string|max:50',
            'images' => 'required|array|min:1|max:10',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        // Calculate discount percentage
        $discountPercentage = 0;
        if ($request->old_price && $request->old_price > $request->price) {
            $discountPercentage = round((($request->old_price - $request->price) / $request->old_price) * 100);
        }

        // Filter empty sizes and colors
        $sizes = $request->sizes ? array_filter($request->sizes, function ($value) {
            return !empty(trim($value));
        }) : null;

        $colors = $request->colors ? array_filter($request->colors, function ($value) {
            return !empty(trim($value));
        }) : null;

        // Create product
        $product = Product::create([
            'user_id' => auth()->id(),
            'category_id' => $request->category_id,
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'price' => $request->price,
            'old_price' => $request->old_price,
            'discount_percentage' => $discountPercentage,
            'stock' => $request->stock,
            'brand' => $request->brand,
            'model' => $request->model,
            'condition' => $request->condition ?? 'new',
            'weight' => $request->weight,
            'dimensions' => $request->dimensions,
            'sizes' => $sizes ? array_values($sizes) : null,
            'colors' => $colors ? array_values($colors) : null,
            'is_active' => true,
        ]);

        // Upload images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('products', 'public');

                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'order' => $index + 1,
                    'is_primary' => $index === 0,
                ]);
            }
        }

        return redirect()->route('vendor.products.index')
            ->with('success', 'Product created successfully!');
    }

    public function show(Product $product)
    {
        // Check ownership
        if ($product->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }

        $product->load(['images', 'category', 'reviews.user', 'orderItems.order']);

        $totalSold = $product->orderItems()
            ->whereHas('order', function ($q) {
                $q->whereIn('status', ['delivered', 'shipped']);
            })
            ->sum('quantity');

        $totalRevenue = $product->orderItems()
            ->whereHas('order', function ($q) {
                $q->where('status', 'delivered');
            })
            ->sum('total');

        $recentOrders = $product->orderItems()
            ->with('order')
            ->latest()
            ->take(10)
            ->get();

        return view('vendor.products.show', compact('product', 'totalSold', 'totalRevenue', 'recentOrders'));
    }

    public function edit(Product $product)
    {
        // Check ownership
        if ($product->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }

        $categories = Category::active()->orderBy('name')->get();
        $product->load('images');

        return view('vendor.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        // Check ownership
        if ($product->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'old_price' => 'nullable|numeric|min:0|gte:price',
            'stock' => 'required|integer|min:0',
            'brand' => 'nullable|string|max:100',
            'model' => 'nullable|string|max:100',
            'condition' => 'nullable|in:new,used,refurbished',
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|string|max:100',
            'sizes' => 'nullable|array',
            'sizes.*' => 'string|max:50',
            'colors' => 'nullable|array',
            'colors.*' => 'string|max:50',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        // Calculate discount percentage
        $discountPercentage = 0;
        if ($request->old_price && $request->old_price > $request->price) {
            $discountPercentage = round((($request->old_price - $request->price) / $request->old_price) * 100);
        }

        // Filter empty sizes and colors
        $sizes = $request->sizes ? array_filter($request->sizes, function ($value) {
            return !empty(trim($value));
        }) : null;

        $colors = $request->colors ? array_filter($request->colors, function ($value) {
            return !empty(trim($value));
        }) : null;

        // Update product
        $product->update([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'price' => $request->price,
            'old_price' => $request->old_price,
            'discount_percentage' => $discountPercentage,
            'stock' => $request->stock,
            'brand' => $request->brand,
            'model' => $request->model,
            'condition' => $request->condition ?? 'new',
            'weight' => $request->weight,
            'dimensions' => $request->dimensions,
            'sizes' => $sizes ? array_values($sizes) : null,
            'colors' => $colors ? array_values($colors) : null,
        ]);

        // Upload new images if provided
        if ($request->hasFile('images')) {
            $currentMaxOrder = $product->images()->max('order') ?? 0;

            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('products', 'public');

                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'order' => $currentMaxOrder + $index + 1,
                    'is_primary' => $product->images()->count() === 0 && $index === 0,
                ]);
            }
        }

        return redirect()->route('vendor.products.index')
            ->with('success', 'Product updated successfully!');
    }

    public function destroy(Product $product)
    {
        // Check ownership
        if ($product->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }

        // Check if product has orders
        if ($product->orderItems()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete product with existing orders. You can deactivate it instead.');
        }

        // Delete images from storage
        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }

        $product->delete();

        return redirect()->route('vendor.products.index')
            ->with('success', 'Product deleted successfully!');
    }

    public function deleteImage(ProductImage $image)
    {
        // Check ownership
        if ($image->product->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }

        // Don't allow deleting the last image
        if ($image->product->images()->count() <= 1) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete the last image. Product must have at least one image.'
            ], 422);
        }

        // If deleting primary image, set another as primary
        if ($image->is_primary) {
            $nextImage = $image->product->images()
                ->where('id', '!=', $image->id)
                ->orderBy('order')
                ->first();

            if ($nextImage) {
                $nextImage->update(['is_primary' => true]);
            }
        }

        Storage::disk('public')->delete($image->image_path);
        $image->delete();

        return response()->json([
            'success' => true,
            'message' => 'Image deleted successfully!'
        ]);
    }

    public function toggleStatus(Product $product)
    {
        // Check ownership
        if ($product->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }

        $product->update(['is_active' => !$product->is_active]);

        $status = $product->is_active ? 'activated' : 'deactivated';

        return redirect()->back()
            ->with('success', "Product {$status} successfully!");
    }
}
