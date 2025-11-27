@extends('layouts.admin')

@section('title', 'Categories Management')

@section('content')
<div class="p-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Categories</h1>
            <p class="text-gray-600 mt-1">Manage product categories</p>
        </div>
        <a href="{{ route('super-admin.categories.create') }}"
           class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add Category
        </a>
    </div>

    <!-- Categories Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($categories as $category)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-shadow">
                <!-- Category Image -->
                @if($category->image)
                    <div class="w-full h-32 bg-gray-100 rounded-lg mb-4 overflow-hidden">
                        <img src="{{ asset('storage/' . $category->image) }}"
                             alt="{{ $category->name }}"
                             class="w-full h-full object-cover">
                    </div>
                @else
                    <div class="w-full h-32 bg-gray-100 rounded-lg mb-4 flex items-center justify-center">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                    </div>
                @endif

                <!-- Category Info -->
                <div class="mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ $category->name }}</h3>
                    @if($category->description)
                        <p class="text-sm text-gray-600 line-clamp-2">{{ $category->description }}</p>
                    @endif
                </div>

                <!-- Stats -->
                <div class="flex items-center justify-between mb-4 text-sm text-gray-600">
                    <div>
                        <span>{{ $category->products_count }} products</span>
                        <span class="ml-2">•</span>
                        <span class="ml-2">{{ $category->sub_categories_count }} sub-categories</span>
                    </div>
                    <span class="px-2 py-1 rounded-full text-xs font-medium
                        {{ $category->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $category->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>

                <!-- Actions -->
                <div class="flex items-center space-x-2">
                    <a href="{{ route('super-admin.categories.show', $category) }}"
                       class="flex-1 bg-blue-50 text-blue-600 px-4 py-2 rounded-lg hover:bg-blue-100 text-center text-sm font-medium">
                        View
                    </a>
                    <a href="{{ route('super-admin.sub-categories.index') }}?category={{ $category->id }}"
                       class="flex-1 bg-indigo-50 text-indigo-600 px-4 py-2 rounded-lg hover:bg-indigo-100 text-center text-sm font-medium">
                        Sub-Categories
                    </a>
                </div>
                <div class="flex items-center space-x-2 mt-2">
                    <a href="{{ route('super-admin.categories.edit', $category) }}"
                       class="flex-1 bg-blue-50 text-blue-600 px-4 py-2 rounded-lg hover:bg-blue-100 text-center text-sm font-medium">
                        Edit
                    </a>
                    <form method="POST" action="{{ route('super-admin.categories.destroy', $category) }}"
                          onsubmit="return confirm('Are you sure you want to delete this category?');"
                          class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="w-full bg-red-50 text-red-600 px-4 py-2 rounded-lg hover:bg-red-100 text-sm font-medium">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-lg shadow-sm p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">No categories found</h3>
                <p class="mt-2 text-sm text-gray-500">Get started by creating a new category.</p>
                <div class="mt-6">
                    <a href="{{ route('super-admin.categories.create') }}"
                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Add Category
                    </a>
                </div>
            </div>
        @endforelse
    </div>


    <!-- Pagination -->
    @if($categories->hasPages())
        <div class="mt-6">
            {{ $categories->links() }}
        </div>
    @endif
</div>
@endsection
