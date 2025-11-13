<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class ProductVariantController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $this->authorize('update', $product);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:100|unique:product_variants,sku',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'attributes' => 'nullable|json',
        ]);

        $validated['product_id'] = $product->id;

        ProductVariant::create($validated);

        return back()->with('success', 'Variant created successfully!');
    }

    public function update(Request $request, Product $product, ProductVariant $variant)
    {
        $this->authorize('update', $product);

        if ($variant->product_id !== $product->id) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:100|unique:product_variants,sku,' . $variant->id,
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'attributes' => 'nullable|json',
        ]);

        $variant->update($validated);

        return back()->with('success', 'Variant updated successfully!');
    }

    public function destroy(Product $product, ProductVariant $variant)
    {
        $this->authorize('update', $product);

        if ($variant->product_id !== $product->id) {
            abort(404);
        }

        $variant->delete();

        return back()->with('success', 'Variant deleted successfully!');
    }
}
