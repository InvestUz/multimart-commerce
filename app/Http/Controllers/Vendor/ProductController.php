<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $query = $user->products()
            ->with(['category', 'brand', 'images'])
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

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $products = $query->latest()->paginate(20);

        return view('vendor.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $brands = Brand::where('is_active', true)->orderBy('name')->get();
        $locales = ['en' => 'English', 'ru' => 'Russian', 'uz' => 'Uzbek'];

        return view('vendor.products.create', compact('categories', 'brands', 'locales'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            // Default language fields
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug',
            'sku' => 'required|string|max:100|unique:products,sku',
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'nullable|exists:sub_categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|string|max:100',
            'is_active' => 'boolean',
            'images' => 'required|array|min:1',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
            'tags' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            // Multilingual fields
            'name_translations.*' => 'nullable|string|max:255',
            'description_translations.*' => 'nullable|string',
            'short_description_translations.*' => 'nullable|string|max:500',
            'meta_title_translations.*' => 'nullable|string|max:255',
            'meta_description_translations.*' => 'nullable|string|max:500',
            'meta_keywords_translations.*' => 'nullable|string|max:255',
        ], [
            'name.required' => 'Mahsulot nomi kiriting.',
            'sku.required' => 'SKU majburiy.',
            'sku.unique' => 'Bu SKU allaqachon mavjud.',
            'category_id.required' => 'Kategoriya tanlang.',
            'description.required' => 'Mahsulot tavsifini kiriting.',
            'price.required' => 'Narx kiriting.',
            'price.numeric' => 'Narx raqam bo\'lishi kerak.',
            'stock.required' => 'Zaxira miqdorini kiriting.',
            'stock.integer' => 'Zaxira miqdori butun son bo\'lishi kerak.',
            'images.required' => 'Mahsulot uchun kamida bitta rasm yuklanishi kerak.',
            'images.array' => 'Rasmlar array sifatida kerak.',
            'images.min' => 'Kamida bitta rasm yuklanishi kerak.',
            'images.*.image' => 'Tanlangan fayl rasm ekani kerak.',
            'images.*.mimes' => 'Rasm JPEG, PNG, JPG yoki WEBP formatida ekani kerak.',
            'images.*.max' => 'Rasm hajmi 2MB dan ortiq bolmasligi kerak.',
        ]);

        if (!$request->filled('slug')) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Set user_id (vendor_id column is actually user_id)
        $validated['user_id'] = auth()->id();

        // Remove fields that don't exist in the table or will be handled separately
        unset($validated['images']);
        unset($validated['name_translations']);
        unset($validated['description_translations']);
        unset($validated['short_description_translations']);
        unset($validated['meta_title_translations']);
        unset($validated['meta_description_translations']);
        unset($validated['meta_keywords_translations']);

        DB::beginTransaction();
        try {
            $product = Product::create($validated);

            // Handle multilingual fields
            $locales = ['en', 'ru', 'uz'];
            foreach ($locales as $locale) {
                if ($request->filled("name_translations.$locale")) {
                    $product->setNameTranslation($locale, $request->input("name_translations.$locale"));
                }

                if ($request->filled("description_translations.$locale")) {
                    $product->setDescriptionTranslation($locale, $request->input("description_translations.$locale"));
                }

                if ($request->filled("short_description_translations.$locale")) {
                    $product->setShortDescriptionTranslation($locale, $request->input("short_description_translations.$locale"));
                }

                if ($request->filled("meta_title_translations.$locale")) {
                    $product->setMetaTitleTranslation($locale, $request->input("meta_title_translations.$locale"));
                }

                if ($request->filled("meta_description_translations.$locale")) {
                    $product->setMetaDescriptionTranslation($locale, $request->input("meta_description_translations.$locale"));
                }

                if ($request->filled("meta_keywords_translations.$locale")) {
                    $product->setMetaKeywordsTranslation($locale, $request->input("meta_keywords_translations.$locale"));
                }
            }

            // Save the translations
            $product->save();

            // Handle images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $image) {
                    $path = $image->store('products', 'public');

                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $path,
                        'order' => $index,
                        'is_primary' => $index === 0,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('vendor.products.index')
                ->with('success', 'Product created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Vendor product creation error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return back()->withInput()
                ->with('error', 'Mahsulot yaratishda xato: ' . $e->getMessage());
        }
    }

    public function show(Product $product)
    {
        $this->authorize('view', $product);

        $product->load([
            'category',
            'subCategory',
            'brand',
            'images',
            'variants',
            'reviews.user'
        ]);

        return view('vendor.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $this->authorize('update', $product);

        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $brands = Brand::where('is_active', true)->orderBy('name')->get();
        $subCategories = SubCategory::where('category_id', $product->category_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        $locales = ['en' => 'English', 'ru' => 'Russian', 'uz' => 'Uzbek'];

        return view('vendor.products.edit', compact('product', 'categories', 'brands', 'subCategories', 'locales'));
    }

    public function update(Request $request, Product $product)
    {
        $this->authorize('update', $product);

        $validated = $request->validate([
            // Default language fields
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug,' . $product->id,
            'sku' => 'required|string|max:100|unique:products,sku,' . $product->id,
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'nullable|exists:sub_categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|string|max:100',
            'is_active' => 'boolean',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'tags' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            // Multilingual fields
            'name_translations.*' => 'nullable|string|max:255',
            'description_translations.*' => 'nullable|string',
            'short_description_translations.*' => 'nullable|string|max:500',
            'meta_title_translations.*' => 'nullable|string|max:255',
            'meta_description_translations.*' => 'nullable|string|max:500',
            'meta_keywords_translations.*' => 'nullable|string|max:255',
        ]);

        if (!$request->filled('slug')) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Remove fields that don't exist in the table or will be handled separately
        unset($validated['images']);
        unset($validated['name_translations']);
        unset($validated['description_translations']);
        unset($validated['short_description_translations']);
        unset($validated['meta_title_translations']);
        unset($validated['meta_description_translations']);
        unset($validated['meta_keywords_translations']);

        DB::beginTransaction();
        try {
            $product->update($validated);

            // Handle multilingual fields
            $locales = ['en', 'ru', 'uz'];
            foreach ($locales as $locale) {
                if ($request->filled("name_translations.$locale")) {
                    $product->setNameTranslation($locale, $request->input("name_translations.$locale"));
                }

                if ($request->filled("description_translations.$locale")) {
                    $product->setDescriptionTranslation($locale, $request->input("description_translations.$locale"));
                }

                if ($request->filled("short_description_translations.$locale")) {
                    $product->setShortDescriptionTranslation($locale, $request->input("short_description_translations.$locale"));
                }

                if ($request->filled("meta_title_translations.$locale")) {
                    $product->setMetaTitleTranslation($locale, $request->input("meta_title_translations.$locale"));
                }

                if ($request->filled("meta_description_translations.$locale")) {
                    $product->setMetaDescriptionTranslation($locale, $request->input("meta_description_translations.$locale"));
                }

                if ($request->filled("meta_keywords_translations.$locale")) {
                    $product->setMetaKeywordsTranslation($locale, $request->input("meta_keywords_translations.$locale"));
                }
            }

            // Save the translations
            $product->save();

            // Handle new images
            if ($request->hasFile('images')) {
                $currentMaxOrder = $product->images()->max('order') ?? -1;

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

            DB::commit();

            return redirect()->route('vendor.products.index')
                ->with('success', 'Product updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to update product. Please try again.');
        }
    }

    public function destroy(Product $product)
    {
        $this->authorize('delete', $product);

        DB::beginTransaction();
        try {
            // Delete images
            foreach ($product->images as $image) {
                Storage::disk('public')->delete($image->image_path);
                $image->delete();
            }

            // Delete variants
            $product->variants()->delete();

            $product->delete();

            DB::commit();

            return redirect()->route('vendor.products.index')
                ->with('success', 'Product deleted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete product.');
        }
    }

    public function toggleStatus(Product $product)
    {
        $this->authorize('update', $product);

        $product->update(['is_active' => !$product->is_active]);

        return response()->json([
            'success' => true,
            'message' => 'Product status updated successfully!',
            'is_active' => $product->is_active
        ]);
    }

    public function deleteImage(Product $product, ProductImage $image)
    {
        $this->authorize('update', $product);

        if ($image->product_id !== $product->id) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid image'
            ], 400);
        }

        Storage::disk('public')->delete($image->image_path);
        $image->delete();

        // If this was the primary image, set another as primary
        if ($image->is_primary) {
            $newPrimary = $product->images()->first();
            if ($newPrimary) {
                $newPrimary->update(['is_primary' => true]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Image deleted successfully!'
        ]);
    }

    public function reorderImages(Request $request, Product $product)
    {
        $this->authorize('update', $product);

        $validated = $request->validate([
            'orders' => 'required|array',
            'orders.*.id' => 'required|exists:product_images,id',
            'orders.*.order' => 'required|integer|min:0',
        ]);

        foreach ($validated['orders'] as $item) {
            ProductImage::where('id', $item['id'])
                ->where('product_id', $product->id)
                ->update(['order' => $item['order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Images reordered successfully!'
        ]);
    }

    /**
     * Get sub-categories by category ID
     */
    public function getSubCategories($categoryId)
    {
        $subCategories = SubCategory::where('category_id', $categoryId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return response()->json($subCategories);
    }
}
