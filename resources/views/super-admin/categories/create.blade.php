@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Create Category Form -->
            <div class="lg:col-span-2">
                <h1 class="text-2xl font-bold mb-6">Create Category</h1>

                @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('super-admin.categories.store') }}" method="POST"
                    class="bg-white p-6 rounded shadow-md space-y-4">
                    @csrf

                    <div>
                        <label for="name" class="block font-semibold mb-2">Name *</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                            class="w-full border border-gray-300 rounded px-3 py-2" placeholder="Category name">
                        @error('name')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="icon" class="block font-semibold mb-2">Icon (FontAwesome)</label>
                        <input type="text" name="icon" id="icon" value="{{ old('icon', 'fa-box') }}"
                            class="w-full border border-gray-300 rounded px-3 py-2" placeholder="fa-box">
                        @error('icon')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="color" class="block font-semibold mb-2">Color</label>
                        <input type="color" name="color" id="color" value="{{ old('color', '#4CAF50') }}"
                            class="w-full h-10 border border-gray-300 rounded cursor-pointer">
                        @error('color')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="order" class="block font-semibold mb-2">Order</label>
                        <input type="number" name="order" id="order" value="{{ old('order', 0) }}" min="0"
                            class="w-full border border-gray-300 rounded px-3 py-2" placeholder="0">
                        @error('order')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="is_active" {{ old('is_active') ? 'checked' : '' }} class="mr-2">
                            <span class="font-semibold">Active</span>
                        </label>
                    </div>

                    <div class="flex gap-4 pt-4">
                        <button type="submit"
                            class="bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600 flex-1">Create
                            Category</button>
                        <a href="{{ route('super-admin.categories.index') }}"
                            class="bg-gray-500 text-white px-6 py-2 rounded hover:bg-gray-600 flex-1 text-center">Cancel</a>
                    </div>
                </form>
            </div>

            <!-- Quick Reference -->
            <div>
                <h2 class="text-xl font-bold mb-4">Info</h2>
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 space-y-4">
                    <div>
                        <h3 class="font-semibold text-blue-900 mb-2">SubCategories</h3>
                        <p class="text-sm text-blue-800">
                            After creating, add sub-categories to organize products.
                        </p>
                    </div>
                    <a href="{{ route('super-admin.sub-categories.index') }}"
                        class="block text-center bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 text-sm font-semibold">
                        Manage Sub-Categories
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
