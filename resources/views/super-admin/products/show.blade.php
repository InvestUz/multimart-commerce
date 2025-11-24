@extends('layouts.admin')

@section('title', 'Product Details - ' . $product->name)

@section('content')
<div class="p-6">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Product Details</h1>
                <p class="text-gray-600 mt-1">View and manage product information</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('product.show', $product->slug) }}"
                   target="_blank"
                   class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300">
                    View on Store
                </a>
                <a href="{{ route('super-admin.products.index') }}"
                   class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300">
                    Back to Products
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Product Images -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Product Images</h3>

                @if($product->images->count() > 0)
                    <div class="space-y-4">
                        @foreach($product->images as $image)
                            <div class="relative">
                                <img src="{{ asset('storage/' . $image->image_path) }}"
                                     alt="{{ $product->name }}"
                                     class="w-full rounded-lg border border-gray-200">
                                @if($image->is_primary)
                                    <span class="absolute top-2 right-2 bg-blue-600 text-white text-xs font-semibold px-2 py-1 rounded">
                                        Primary
                                    </span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="w-full h-48 bg-gray-100 rounded-lg flex items-center justify-center">
                        <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                @endif
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mt-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                <div class="space-y-2">
                    <form method="POST" action="{{ route('super-admin.products.toggle-status', $product) }}">
                        @csrf
                        <button type="submit"
                                class="w-full {{ $product->is_active ? 'bg-red-50 text-red-600 hover:bg-red-100' : 'bg-green-50 text-green-600 hover:bg-green-100' }} px-4 py-2 rounded-lg text-sm font-medium">
                            {{ $product->is_active ? 'Deactivate' : 'Activate' }}
                        </button>
                    </form>

                    <form method="POST" action="{{ route('super-admin.products.toggle-featured', $product) }}">
                        @csrf
                        <button type="submit"
                                class="w-full {{ $product->is_featured ? 'bg-yellow-50 text-yellow-600 hover:bg-yellow-100' : 'bg-blue-50 text-blue-600 hover:bg-blue-100' }} px-4 py-2 rounded-lg text-sm font-medium">
                            {{ $product->is_featured ? 'Remove Featured' : 'Make Featured' }}
                        </button>
                    </form>

                    <form method="POST" action="{{ route('super-admin.products.destroy', $product) }}"
                          onsubmit="return confirm('Are you sure you want to delete this product?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="w-full bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 text-sm font-medium">
                            Delete Product
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Product Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Info -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Product Information</h3>

                <div class="space-y-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">{{ $product->name }}</h2>
                        <p class="text-sm text-gray-500 mt-1">SKU: {{ $product->sku }}</p>
                    </div>

                    <div class="flex items-center space-x-4">
                        <span class="px-3 py-1 text-sm font-medium rounded-full
                            {{ $product->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $product->is_active ? 'Active' : 'Inactive' }}
                        </span>
                        @if($product->is_featured)
                            <span class="px-3 py-1 text-sm font-medium rounded-full bg-yellow-100 text-yellow-800">
                                Featured
                            </span>
                        @endif
                    </div>

                    <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-200">
                        <div>
                            <p class="text-sm text-gray-600">Vendor</p>
                            <p class="text-base font-semibold text-gray-900">{{ $product->vendor->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Category</p>
                            <p class="text-base font-semibold text-gray-900">{{ $product->category->name }}</p>
                        </div>
                        @if($product->brand)
                            <div>
                                <p class="text-sm text-gray-600">Brand</p>
                                <p class="text-base font-semibold text-gray-900">{{ $product->brand->name }}</p>
                            </div>
                        @endif
                        <div>
                            <p class="text-sm text-gray-600">Condition</p>
                            <p class="text-base font-semibold text-gray-900">{{ ucfirst($product->condition) }}</p>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-200">
                        <p class="text-sm text-gray-600 mb-2">Description</p>
                        <p class="text-gray-700">{{ $product->description }}</p>
                    </div>
                </div>
            </div>

            <!-- Pricing & Stock -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Pricing & Stock</h3>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-blue-50 rounded-lg p-4">
                        <p class="text-sm text-blue-600 mb-1">Current Price</p>
                        <p class="text-2xl font-bold text-gray-900">${{ number_format($product->price, 2) }}</p>
                    </div>
                    @if($product->old_price)
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-sm text-gray-600 mb-1">Old Price</p>
                            <p class="text-2xl font-bold text-gray-400 line-through">${{ number_format($product->old_price, 2) }}</p>
                        </div>
                    @endif
                    @if($product->discount_percentage > 0)
                        <div class="bg-red-50 rounded-lg p-4">
                            <p class="text-sm text-red-600 mb-1">Discount</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $product->discount_percentage }}%</p>
                        </div>
                    @endif
                    <div class="bg-green-50 rounded-lg p-4">
                        <p class="text-sm text-green-600 mb-1">Stock</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $product->stock }}</p>
                    </div>
                </div>
            </div>

            <!-- Statistics -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistics</h3>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-purple-50 rounded-lg p-4">
                        <p class="text-sm text-purple-600 mb-1">Total Sales</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $product->total_sales ?? 0 }}</p>
                    </div>
                    <div class="bg-yellow-50 rounded-lg p-4">
                        <p class="text-sm text-yellow-600 mb-1">Rating</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($product->average_rating ?? 0, 1) }}</p>
                    </div>
                    <div class="bg-pink-50 rounded-lg p-4">
                        <p class="text-sm text-pink-600 mb-1">Reviews</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $product->total_reviews ?? 0 }}</p>
                    </div>
                    <div class="bg-indigo-50 rounded-lg p-4">
                        <p class="text-sm text-indigo-600 mb-1">Views</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $product->views ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <!-- Variants -->
            @if($product->variants && $product->variants->count() > 0)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Product Variants</h3>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Size</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Color</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($product->variants as $variant)
                                    <tr>
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ $variant->size ?? '-' }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ $variant->color ?? '-' }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ $variant->stock }}</td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                                {{ $variant->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $variant->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
