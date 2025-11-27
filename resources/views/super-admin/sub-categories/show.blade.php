@extends('layouts.admin')

@section('title', 'Sub-Category Details - Admin Panel')

@section('content')
<div class="p-6">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Sub-Category Details</h1>
                <p class="text-gray-600 mt-1">View sub-category information</p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('super-admin.sub-categories.edit', $subCategory) }}"
                   class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    Edit
                </a>
                <a href="{{ route('super-admin.sub-categories.index') }}"
                   class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300">
                    Back to Sub-Categories
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Sub-Category Details -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Sub-Category Information</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-gray-500">Name</p>
                        <p class="text-lg font-medium text-gray-900">{{ $subCategory->name }}</p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-500">Slug</p>
                        <p class="text-lg font-medium text-gray-900">{{ $subCategory->slug }}</p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-500">Parent Category</p>
                        <p class="text-lg font-medium text-gray-900">{{ $subCategory->category->name }}</p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-500">Status</p>
                        @if($subCategory->is_active)
                        <span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">Active</span>
                        @else
                        <span class="px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-full">Inactive</span>
                        @endif
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-500">Order</p>
                        <p class="text-lg font-medium text-gray-900">{{ $subCategory->order }}</p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-500">Created At</p>
                        <p class="text-lg font-medium text-gray-900">{{ $subCategory->created_at->format('M d, Y H:i') }}</p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-500">Updated At</p>
                        <p class="text-lg font-medium text-gray-900">{{ $subCategory->updated_at->format('M d, Y H:i') }}</p>
                    </div>
                </div>
                
                @if($subCategory->description)
                <div class="mt-6">
                    <p class="text-sm text-gray-500">Description</p>
                    <p class="text-gray-900 mt-1">{{ $subCategory->description }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Stats -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistics</h3>
                
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <p class="text-sm text-gray-500">Total Products</p>
                        <p class="text-lg font-medium text-gray-900">{{ $subCategory->products_count }}</p>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <p class="text-sm text-gray-500">Active Products</p>
                        <p class="text-lg font-medium text-gray-900">{{ $subCategory->activeProducts()->count() }}</p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions</h3>
                
                <div class="space-y-3">
                    <a href="{{ route('super-admin.sub-categories.edit', $subCategory) }}"
                       class="block w-full text-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Edit Sub-Category
                    </a>
                    
                    <form action="{{ route('super-admin.sub-categories.destroy', $subCategory) }}"
                          method="POST"
                          onsubmit="return confirm('Are you sure you want to delete this sub-category? This action cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="block w-full text-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                            Delete Sub-Category
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Products -->
    @if($subCategory->products_count > 0)
    <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Products in this Sub-Category</h3>
            <span class="text-sm text-gray-500">{{ $subCategory->products_count }} products</span>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($subCategory->products as $product)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gray-200 rounded-lg overflow-hidden flex-shrink-0">
                                    @if($product->images->count() > 0)
                                    <img src="{{ asset('storage/' . $product->images->first()->image_path) }}"
                                         alt="{{ $product->name }}"
                                         class="w-full h-full object-cover">
                                    @endif
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-900">{{ $product->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $product->sku }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            ${{ number_format($product->price, 2) }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ $product->stock }}
                        </td>
                        <td class="px-6 py-4">
                            @if($product->is_active)
                            <span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">Active</span>
                            @else
                            <span class="px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-full">Inactive</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm font-medium">
                            <a href="{{ route('super-admin.products.show', $product) }}"
                               class="text-indigo-600 hover:text-indigo-900">
                                View
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection