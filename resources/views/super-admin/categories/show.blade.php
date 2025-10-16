@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="mb-6">
            <a href="{{ route('super-admin.categories.index') }}" class="text-blue-500 hover:underline">&larr; Back to
                Categories</a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Category Details -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded shadow-md p-6 mb-6">
                    <h1 class="text-3xl font-bold mb-4">{{ $category->name }}</h1>

                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <p class="text-gray-600">Icon</p>
                            <p class="font-semibold"><i class="fas {{ $category->icon }}"></i> {{ $category->icon }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Color</p>
                            <div class="flex items-center gap-2">
                                <span class="inline-block w-8 h-8 rounded border border-gray-300"
                                    style="background-color: {{ $category->color }};"></span>
                                <p class="font-semibold">{{ $category->color }}</p>
                            </div>
                        </div>
                        <div>
                            <p class="text-gray-600">Order</p>
                            <p class="font-semibold">{{ $category->order }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Status</p>
                            <span
                                class="px-3 py-1 rounded text-sm {{ $category->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $category->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <a href="{{ route('super-admin.categories.edit', $category) }}"
                            class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">Edit</a>
                        <form action="{{ route('super-admin.categories.destroy', $category) }}" method="POST"
                            class="inline" onsubmit="return confirm('Are you sure?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Delete</button>
                        </form>
                    </div>
                </div>

                <!-- Sub-Categories Section -->
                <div class="bg-white rounded shadow-md p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-2xl font-bold">Sub-Categories
                            ({{ $category->sub_categories_count ?? count($category->subCategories) }})</h2>
                        <a href="{{ route('super-admin.sub-categories.create') }}?category_id={{ $category->id }}"
                            class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 text-sm">
                            Add Sub-Category
                        </a>
                    </div>

                    @if ($category->subCategories->count() > 0)
                        <div class="space-y-3">
                            @foreach ($category->subCategories as $subCategory)
                                <div class="border border-gray-200 rounded p-4 hover:bg-gray-50">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <h3 class="text-lg font-semibold">{{ $subCategory->name }}</h3>
                                            <div class="flex items-center gap-4 mt-2 text-sm text-gray-600">
                                                <span><i class="fas {{ $subCategory->icon }}"></i>
                                                    {{ $subCategory->icon }}</span>
                                                <span class="inline-block w-4 h-4 rounded"
                                                    style="background-color: {{ $subCategory->color }};"></span>
                                                <span>Order: {{ $subCategory->order }}</span>
                                                <span>Products: {{ $subCategory->products_count }}</span>
                                                <span
                                                    class="px-2 py-1 rounded text-xs {{ $subCategory->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ $subCategory->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex gap-2">
                                            <a href="{{ route('super-admin.sub-categories.edit', $subCategory) }}"
                                                class="text-blue-500 hover:text-blue-700 text-sm">Edit</a>
                                            <form action="{{ route('super-admin.sub-categories.destroy', $subCategory) }}"
                                                method="POST" class="inline"
                                                onsubmit="return confirm('Delete this sub-category?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="text-red-500 hover:text-red-700 text-sm">Delete</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 italic">No sub-categories yet. <a
                                href="{{ route('super-admin.sub-categories.create') }}?category_id={{ $category->id }}"
                                class="text-blue-500 hover:underline">Create one</a></p>
                    @endif
                </div>
            </div>

            <!-- Products Section -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded shadow-md p-6">
                    <h3 class="text-xl font-bold mb-4">Products
                        ({{ $category->products_count ?? $category->products()->count() }})</h3>

                    @if ($category->products->count() > 0)
                        <div class="space-y-2 max-h-96 overflow-y-auto">
                            @foreach ($category->products as $product)
                                <div class="p-3 border border-gray-200 rounded hover:bg-gray-50">
                                    <p class="font-semibold text-sm">{{ $product->name }}</p>
                                    <p class="text-xs text-gray-600">by {{ $product->user->name }}</p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 italic">No products yet</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
