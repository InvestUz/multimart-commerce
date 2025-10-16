@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-md">
    <h1 class="text-2xl font-bold mb-6">Edit Category</h1>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('super-admin.categories.update', $category) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label for="name" class="block font-semibold mb-2">Name *</label>
            <input type="text" name="name" id="name" value="{{ old('name', $category->name) }}" required class="w-full border border-gray-300 rounded px-3 py-2" placeholder="Category name">
            @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="icon" class="block font-semibold mb-2">Icon (FontAwesome)</label>
            <input type="text" name="icon" id="icon" value="{{ old('icon', $category->icon) }}" class="w-full border border-gray-300 rounded px-3 py-2" placeholder="fa-box">
            @error('icon') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="color" class="block font-semibold mb-2">Color</label>
            <input type="color" name="color" id="color" value="{{ old('color', $category->color) }}" class="w-full h-10 border border-gray-300 rounded cursor-pointer">
            @error('color') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="order" class="block font-semibold mb-2">Order</label>
            <input type="number" name="order" id="order" value="{{ old('order', $category->order) }}" min="0" class="w-full border border-gray-300 rounded px-3 py-2" placeholder="0">
            @error('order') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="flex items-center">
                <input type="checkbox" name="is_active" {{ old('is_active', $category->is_active) ? 'checked' : '' }} class="mr-2">
                <span class="font-semibold">Active</span>
            </label>
        </div>

        <div class="flex gap-4">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 flex-1">Update</button>
            <a href="{{ route('super-admin.categories.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 flex-1 text-center">Cancel</a>
        </div>
    </form>
</div>
@endsection
