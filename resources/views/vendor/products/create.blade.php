@extends('layouts.app')

@section('title', 'Add New Product')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center mb-8">
        <a href="{{ route('vendor.products.index') }}" class="text-primary hover:underline mr-4">
            <i class="fas fa-arrow-left"></i> Back to Products
        </a>
        <h1 class="text-3xl font-bold">Add New Product</h1>
    </div>

    <form action="{{ route('vendor.products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="space-y-6">
            <!-- Basic Information -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-bold mb-4">Basic Information</h2>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Product Name *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                               class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent"
                               placeholder="Enter product name">
                        @error('name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Category *</label>
                        <select name="category_id" required
                                class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent">
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Description</label>
                        <textarea name="description" rows="4"
                                  class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent"
                                  placeholder="Enter product description">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Pricing -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-bold mb-4">Pricing</h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Price *</label>
                        <input type="number" name="price" step="0.01" min="0" value="{{ old('price') }}" required
                               class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent"
                               placeholder="0.00">
                        @error('price')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Old Price (for discount)</label>
                        <input type="number" name="old_price" step="0.01" min="0" value="{{ old('old_price') }}"
                               class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent"
                               placeholder="0.00">
                        @error('old_price')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Stock Quantity *</label>
                        <input type="number" name="stock" min="0" value="{{ old('stock') }}" required
                               class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent"
                               placeholder="0">
                        @error('stock')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Product Details -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-bold mb-4">Product Details</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Brand</label>
                        <input type="text" name="brand" value="{{ old('brand') }}"
                               class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent"
                               placeholder="Brand name">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Model</label>
                        <input type="text" name="model" value="{{ old('model') }}"
                               class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent"
                               placeholder="Model number">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Condition</label>
                        <select name="condition"
                                class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent">
                            <option value="new" {{ old('condition') === 'new' ? 'selected' : '' }}>New</option>
                            <option value="used" {{ old('condition') === 'used' ? 'selected' : '' }}>Used</option>
                            <option value="refurbished" {{ old('condition') === 'refurbished' ? 'selected' : '' }}>Refurbished</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Weight (kg)</label>
                        <input type="number" name="weight" step="0.01" min="0" value="{{ old('weight') }}"
                               class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent"
                               placeholder="0.00">
                    </div>
                </div>
            </div>

            <!-- Variants -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-bold mb-4">Product Variants (Optional)</h2>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Sizes</label>
                        <input type="text" name="sizes[]" value="{{ old('sizes.0') }}"
                               class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent mb-2"
                               placeholder="e.g., S, M, L, XL or 6, 7, 8, 9">
                        <div id="size-container"></div>
                        <button type="button" onclick="addSize()" class="text-primary text-sm hover:underline">
                            <i class="fas fa-plus mr-1"></i> Add Another Size
                        </button>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Colors</label>
                        <input type="text" name="colors[]" value="{{ old('colors.0') }}"
                               class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent mb-2"
                               placeholder="e.g., Red, Blue, Green">
                        <div id="color-container"></div>
                        <button type="button" onclick="addColor()" class="text-primary text-sm hover:underline">
                            <i class="fas fa-plus mr-1"></i> Add Another Color
                        </button>
                    </div>
                </div>
            </div>

            <!-- Product Images -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-bold mb-4">Product Images *</h2>
                <p class="text-sm text-gray-600 mb-4">Upload up to 10 images. First image will be the primary image.</p>

                <div>
                    <label class="block w-full border-2 border-dashed border-gray-300 rounded-lg p-8 text-center cursor-pointer hover:border-primary transition">
                        <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-2"></i>
                        <p class="text-gray-600">Click to upload images</p>
                        <p class="text-sm text-gray-500">PNG, JPG, GIF up to 2MB each</p>
                        <input type="file" name="images[]" multiple accept="image/*" required class="hidden" id="imageInput" onchange="previewImages(event)">
                    </label>
                    @error('images')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Image Preview -->
                <div id="imagePreview" class="grid grid-cols-2 md:grid-cols-5 gap-4 mt-4"></div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex gap-4">
                <button type="submit" class="flex-1 bg-primary text-white py-3 rounded-lg hover:bg-green-600 font-semibold">
                    <i class="fas fa-save mr-2"></i> Create Product
                </button>
                <a href="{{ route('vendor.products.index') }}" class="flex-1 border border-gray-300 text-center py-3 rounded-lg hover:bg-gray-50">
                    Cancel
                </a>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
function addSize() {
    const container = document.getElementById('size-container');
    const input = document.createElement('input');
    input.type = 'text';
    input.name = 'sizes[]';
    input.className = 'w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent mb-2';
    input.placeholder = 'Enter size';
    container.appendChild(input);
}

function addColor() {
    const container = document.getElementById('color-container');
    const input = document.createElement('input');
    input.type = 'text';
    input.name = 'colors[]';
    input.className = 'w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent mb-2';
    input.placeholder = 'Enter color';
    container.appendChild(input);
}

function previewImages(event) {
    const preview = document.getElementById('imagePreview');
    preview.innerHTML = '';
    
    const files = event.target.files;
    
    if (files.length > 10) {
        alert('Maximum 10 images allowed');
        event.target.value = '';
        return;
    }
    
    for (let i = 0; i < files.length; i++) {
        const file = files[i];
        const reader = new FileReader();
        
        reader.onload = function(e) {
            const div = document.createElement('div');
            div.className = 'relative';
            div.innerHTML = `
                <img src="${e.target.result}" class="w-full h-32 object-cover rounded-lg border">
                ${i === 0 ? '<span class="absolute top-1 left-1 bg-primary text-white text-xs px-2 py-1 rounded">Primary</span>' : ''}
            `;
            preview.appendChild(div);
        };
        
        reader.readAsDataURL(file);
    }
}
</script>
@endpush
@endsection