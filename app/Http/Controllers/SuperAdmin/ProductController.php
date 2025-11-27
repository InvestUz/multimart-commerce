<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Brand;
use App\Models\User;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function create()
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $brands = Brand::where('is_active', true)->orderBy('name')->get();
        $vendors = User::where('role', 'vendor')->where('is_active', true)->orderBy('name')->get();
        
        return view('super-admin.products.create', compact('categories', 'brands', 'vendors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug',
            'sku' => 'required|string|max:100|unique:products,sku',
            'vendor_id' => 'required|exists:users,id',
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'nullable|exists:sub_categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'price' => 'required|numeric|min:0',
            'old_price' => 'nullable|numeric|min:0',
            'discount_percentage' => 'nullable|integer|min:0|max:100',
            'stock' => 'required|integer|min:0',
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|string|max:100',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'images' => 'required|array|min:1',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'tags' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
        ]);

        if (!$request->filled('slug')) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Map vendor_id to user_id
        $validated['user_id'] = $validated['vendor_id'];
        unset($validated['vendor_id']);

        // Calculate discount percentage if old price is provided
        if ($request->filled('old_price') && $request->filled('price')) {
            $oldPrice = $validated['old_price'];
            $price = $validated['price'];
            if ($oldPrice > $price) {
                $validated['discount_percentage'] = round((($oldPrice - $price) / $oldPrice) * 100);
            }
        }

        DB::beginTransaction();
        try {
            $product = Product::create($validated);
            
            \Log::info('Product created with ID: ' . $product->id);

            // Handle images
            if ($request->hasFile('images')) {
                \Log::info('Processing images for product ID: ' . $product->id);
                \Log::info('Number of images: ' . count($request->file('images')));
                
                foreach ($request->file('images') as $index => $image) {
                    if ($image && $image->isValid()) {
                        \Log::info('Processing image ' . $index);
                        $path = $image->store('products', 'public');
                        \Log::info('Image stored at path: ' . $path);
                        
                        ProductImage::create([
                            'product_id' => $product->id,
                            'image_path' => $path,
                            'order' => $index,
                            'is_primary' => $index === 0,
                        ]);
                        \Log::info('Product image record created for image ' . $index);
                    } else {
                        \Log::warning('Invalid image at index ' . $index);
                    }
                }
            } else {
                \Log::warning('No images found in request for product ID: ' . $product->id);
            }

            DB::commit();

            return redirect()->route('super-admin.products.index')
                ->with('success', 'Product created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to create product: ' . $e->getMessage());
            return back()->withInput()
                ->with('error', 'Failed to create product. Please try again.');
        }
    }

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
        $categories = \App\Models\Category::where('is_active', true)->orderBy('name')->get();

        return view('super-admin.products.index', compact('products', 'categories'));
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

    public function deleteImage(Product $product, ProductImage $image)
    {
        // Ensure the image belongs to the product
        if ($image->product_id !== $product->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Delete the image file from storage
        \Storage::disk('public')->delete($image->image_path);

        // Delete the image record from database
        $image->delete();

        return response()->json(['success' => true, 'message' => 'Image deleted successfully!']);
    }

    public function edit(Product $product)
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $brands = Brand::where('is_active', true)->orderBy('name')->get();
        $vendors = User::where('role', 'vendor')->where('is_active', true)->orderBy('name')->get();
        $subCategories = SubCategory::where('category_id', $product->category_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('super-admin.products.edit', compact('product', 'categories', 'brands', 'vendors', 'subCategories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug,' . $product->id,
            'sku' => 'required|string|max:100|unique:products,sku,' . $product->id,
            'vendor_id' => 'required|exists:users,id',
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'nullable|exists:sub_categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'price' => 'required|numeric|min:0',
            'old_price' => 'nullable|numeric|min:0',
            'discount_percentage' => 'nullable|integer|min:0|max:100',
            'stock' => 'required|integer|min:0',
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|string|max:100',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'tags' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
        ]);

        if (!$request->filled('slug')) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Map vendor_id to user_id
        $validated['user_id'] = $validated['vendor_id'];
        unset($validated['vendor_id']);

        // Calculate discount percentage if old price is provided
        if ($request->filled('old_price') && $request->filled('price')) {
            $oldPrice = $validated['old_price'];
            $price = $validated['price'];
            if ($oldPrice > $price) {
                $validated['discount_percentage'] = round((($oldPrice - $price) / $oldPrice) * 100);
            }
        }

        DB::beginTransaction();
        try {
            $product->update($validated);

            // Handle new images
            if ($request->hasFile('images')) {
                $currentMaxOrder = $product->images()->max('order') ?? -1;
                \Log::info('Processing new images for product ID: ' . $product->id);
                \Log::info('Number of new images: ' . count($request->file('images')));

                foreach ($request->file('images') as $index => $image) {
                    if ($image && $image->isValid()) {
                        \Log::info('Processing new image ' . $index);
                        $path = $image->store('products', 'public');
                        \Log::info('New image stored at path: ' . $path);

                        ProductImage::create([
                            'product_id' => $product->id,
                            'image_path' => $path,
                            'order' => $currentMaxOrder + $index + 1,
                            'is_primary' => $product->images()->count() === 0 && $index === 0,
                        ]);
                        \Log::info('New product image record created for image ' . $index);
                    } else {
                        \Log::warning('Invalid new image at index ' . $index);
                    }
                }
            } else {
                \Log::info('No new images found in update request for product ID: ' . $product->id);
            }

            DB::commit();

            return redirect()->route('super-admin.products.index')
                ->with('success', 'Product updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to update product: ' . $e->getMessage());
            return back()->withInput()
                ->with('error', 'Failed to update product. Please try again.');
        }
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
