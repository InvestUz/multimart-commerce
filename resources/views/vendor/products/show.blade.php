@extends('layouts.vendor')

@section('title', 'Product Details - ' . ($product->name ?? 'Product'))

@section('content')
<div class="p-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Product Details</h1>
            <p class="text-gray-600 mt-1">View product information</p>
        </div>
        <div class="space-x-2">
            <a href="{{ route('vendor.products.edit', $product) }}"
               class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                Edit Product
            </a>
            <a href="{{ route('vendor.products.index') }}"
               class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300">
                Back to Products
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Product Images -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Product Images</h3>
                
                <div class="space-y-4">
                    @forelse($product->images ?? [] as $image)
                        <div class="relative group">
                            <img src="{{ asset('storage/' . $image->image_path) }}" 
                                 alt="{{ $product->name ?? 'Product' }}" 
                                 class="w-full h-48 object-cover rounded-lg">
                            @if($image->is_primary)
                                <span class="absolute top-2 left-2 bg-blue-600 text-white text-xs px-2 py-1 rounded">
                                    Primary
                                </span>
                            @endif
                        </div>
                    @empty
                        <div class="bg-gray-100 border-2 border-dashed rounded-lg w-full h-48 flex items-center justify-center">
                            <span class="text-gray-500">No images</span>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Product Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Information -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Product Name</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $product->name ?? 'N/A' }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500">SKU</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $product->sku ?? 'N/A' }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Category</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $product->category->name ?? 'N/A' }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Subcategory</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $product->subCategory->name ?? 'N/A' }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Brand</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $product->brand->name ?? 'N/A' }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Status</label>
                        <p class="mt-1">
                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                {{ ($product->is_active ?? false) ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ ($product->is_active ?? false) ? 'Active' : 'Inactive' }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Pricing & Inventory -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Pricing -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Pricing</h3>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Price</span>
                            <span class="font-medium">${{ number_format($product->price ?? 0, 2) }}</span>
                        </div>
                        
                        @if($product->old_price ?? false)
                            <div class="flex justify-between">
                                <span class="text-gray-500">Compare Price</span>
                                <span class="font-medium line-through text-gray-500">${{ number_format($product->old_price, 2) }}</span>
                            </div>
                            
                            <div class="flex justify-between">
                                <span class="text-gray-500">Discount</span>
                                <span class="font-medium text-green-600">-{{ $product->discount_percentage ?? 0 }}%</span>
                            </div>
                        @endif
                        
                        @if($product->cost_price ?? false)
                            <div class="flex justify-between">
                                <span class="text-gray-500">Cost Price</span>
                                <span class="font-medium">${{ number_format($product->cost_price, 2) }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Inventory -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Inventory</h3>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Stock Quantity</span>
                            <span class="font-medium">{{ $product->stock ?? 0 }}</span>
                        </div>
                        
                        <div class="flex justify-between">
                            <span class="text-gray-500">Condition</span>
                            <span class="font-medium capitalize">{{ $product->condition ?? 'N/A' }}</span>
                        </div>
                        
                        @if($product->total_sales ?? false)
                            <div class="flex justify-between">
                                <span class="text-gray-500">Total Sales</span>
                                <span class="font-medium">{{ $product->total_sales }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Description</h3>
                <div class="prose max-w-none">
                    {!! nl2br(e($product->description ?? 'No description available')) !!}
                </div>
            </div>

            <!-- Attributes -->
            @if(($product->sizes ?? false) || ($product->colors ?? false))
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Attributes</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @if($product->sizes ?? false)
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Sizes</label>
                                <div class="mt-1 flex flex-wrap gap-2">
                                    @foreach($product->sizes as $size)
                                        <span class="px-2 py-1 bg-gray-100 text-gray-800 text-sm rounded">
                                            {{ $size }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        
                        @if($product->colors ?? false)
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Colors</label>
                                <div class="mt-1 flex flex-wrap gap-2">
                                    @foreach($product->colors as $color)
                                        <span class="px-2 py-1 bg-gray-100 text-gray-800 text-sm rounded">
                                            {{ $color }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- SEO Information -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">SEO Information</h3>
                
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Meta Title</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $product->meta_title ?? 'N/A' }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Meta Description</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $product->meta_description ?? 'N/A' }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Meta Keywords</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $product->meta_keywords ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection