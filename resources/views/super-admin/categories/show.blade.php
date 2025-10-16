@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <a href="{{ route('super-admin.categories.index') }}" class="text-blue-500 hover:underline">&larr; Back to Categories</a>
    </div>

    <div class="bg-white rounded shadow-md p-6 max-w-2xl">
        <h1 class="text-3xl font-bold mb-6">{{ $category->name }}</h1>

        <div class="grid grid-cols-2 gap-4 mb-6">
            <div>
                <p class="text-gray-600">Icon</p>
                <p class="font-semibold"><i class="fas {{ $category->icon }}"></i> {{ $category->icon }}</p>
            </div>
            <div>
                <p class="text-gray-600">Color</p>
                <div class="flex items-center gap-2">
                    <span class="inline-block w-8 h-8 rounded border border-gray-300" style="background-color: {{ $category->color }};"></span>
                    <p class="font-semibold">{{ $category->color }}</p>
                </div>
            </div>
            <div>
                <p class="text-gray-600">Order</p>
                <p class="font-semibold">{{ $category->order }}</p>
            </div>
            <div>
                <p class="text-gray-600">Status</p>
                <span class="px-3 py-1 rounded text-sm {{ $category->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ $category->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>
        </div>

        <div class="mb-6">
            <p class="text-gray-600">Products ({{ $category->products()->count() }})</p>
            <div class="mt-2 space-y-2">
                @forelse ($category->products as $product)
                    <div class="flex items-center gap-3 p-2 bg-gray-50 rounded">
                        @if ($product->primaryImage)
                            <img src="{{ asset('storage/' . $product->primaryImage->path) }}" alt="{{ $product->name }}" class="w-12 h-12 object-cover rounded">
                        @endif
                        <div class="flex-1">
                            <p class="font-semibold">{{ $product->name }}</p>
                            <p class="text-sm text-gray-600">by {{ $product->user->name }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 italic">No products in this category</p>
                @endforelse
            </div>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('super-admin.categories.edit', $category) }}" class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">Edit</a>
            <form action="{{ route('super-admin.categories.destroy', $category) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Delete</button>
            </form>
        </div>
    </div>
</div>
@endsection