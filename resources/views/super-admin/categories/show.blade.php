@extends('layouts.admin')

@section('title', 'Category Details - Admin Panel')

@section('content')
<div class="p-6">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Category Details</h1>
                <p class="text-gray-600 mt-1">View category information</p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('super-admin.categories.edit', $category) }}"
                   class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    Edit
                </a>
                <a href="{{ route('super-admin.categories.index') }}"
                   class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300">
                    Back to Categories
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Category Details -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Category Information</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-gray-500">Name</p>
                        <p class="text-lg font-medium text-gray-900">{{ $category->name }}</p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-500">Slug</p>
                        <p class="text-lg font-medium text-gray-900">{{ $category->slug }}</p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-500">Status</p>
                        @if($category->is_active)
                        <span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">Active</span>
                        @else
                        <span class="px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-full">Inactive</span>
                        @endif
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-500">Featured</p>
                        @if($category->is_featured)
                        <span class="px-2 py-1 text-xs font-semibold text-blue-800 bg-blue-100 rounded-full">Featured</span>
                        @else
                        <span class="px-2 py-1 text-xs font-semibold text-gray-800 bg-gray-100 rounded-full">Not Featured</span>
                        @endif
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-500">Order</p>
                        <p class="text-lg font-medium text-gray-900">{{ $category->order }}</p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-500">Created At</p>
                        <p class="text-lg font-medium text-gray-900">{{ $category->created_at->format('M d, Y H:i') }}</p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-500">Updated At</p>
                        <p class="text-lg font-medium text-gray-900">{{ $category->updated_at->format('M d, Y H:i') }}</p>
                    </div>
                </div>
                
                @if($category->description)
                <div class="mt-6">
                    <p class="text-sm text-gray-500">Description</p>
                    <p class="text-gray-900 mt-1">{{ $category->description }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Image -->
            @if($category->image)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Category Image</h3>
                <div class="bg-gray-100 rounded-lg overflow-hidden">
                    <img src="{{ asset('storage/' . $category->image) }}" 
                         alt="{{ $category->name }}" 
                         class="w-full h-48 object-cover">
                </div>
            </div>
            @endif

            <!-- Stats -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistics</h3>
                
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <p class="text-sm text-gray-500">Total Products</p>
                        <p class="text-lg font-medium text-gray-900">{{ $category->products_count }}</p>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <p class="text-sm text-gray-500">Active Products</p>
                        <p class="text-lg font-medium text-gray-900">{{ $category->active_products_count }}</p>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <p class="text-sm text-gray-500">Sub-Categories</p>
                        <p class="text-lg font-medium text-gray-900">{{ $category->sub_categories_count }}</p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions</h3>
                
                <div class="space-y-3">
                    <a href="{{ route('super-admin.categories.edit', $category) }}"
                       class="block w-full text-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Edit Category
                    </a>
                    
                    <a href="{{ route('super-admin.sub-categories.index') }}?category={{ $category->id }}"
                       class="block w-full text-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        Manage Sub-Categories
                    </a>
                    
                    <form action="{{ route('super-admin.categories.destroy', $category) }}"
                          method="POST"
                          onsubmit="return confirm('Are you sure you want to delete this category? This action cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="block w-full text-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                            Delete Category
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Sub-Categories -->
    @if($category->sub_categories_count > 0)
    <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Sub-Categories</h3>
            <a href="{{ route('super-admin.sub-categories.create') }}?category={{ $category->id }}"
               class="text-sm text-blue-600 hover:text-blue-800">
                Add New Sub-Category
            </a>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($category->subCategories as $subCategory)
            <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50">
                <div class="flex items-center justify-between">
                    <a href="{{ route('super-admin.sub-categories.show', $subCategory) }}"
                       class="text-sm font-medium text-gray-900 hover:text-blue-600">
                        {{ $subCategory->name }}
                    </a>
                    @if(!$subCategory->is_active)
                    <span class="px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-full">Inactive</span>
                    @endif
                </div>
                <p class="text-xs text-gray-500 mt-1">{{ $subCategory->products_count }} products</p>
            </div>
            @endforeach
        </div>
    </div>
    @else
    <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No sub-categories</h3>
            <p class="mt-1 text-sm text-gray-500">Get started by creating a new sub-category.</p>
            <div class="mt-6">
                <a href="{{ route('super-admin.sub-categories.create') }}?category={{ $category->id }}"
                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Create Sub-Category
                </a>
            </div>
        </div>
    </div>
    @endif

    <!-- Products -->
    @if($category->products_count > 0)
    <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Products in this Category</h3>
            <span class="text-sm text-gray-500">{{ $category->products_count }} products</span>
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
                    @foreach($category->products as $product)
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