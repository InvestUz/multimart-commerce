@extends('layouts.vendor')

@section('title', __('Edit Product'))

@section('content')
<div class="p-6">
    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center justify-between" role="alert">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
            <button type="button" class="text-green-600 hover:text-green-800" onclick="this.parentElement.remove()">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex items-center justify-between" role="alert">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
            <button type="button" class="text-red-600 hover:text-red-800" onclick="this.parentElement.remove()">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg" role="alert">
            <div class="flex items-center mb-2">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <span class="font-medium">Iltimos, quyidagi xatolarni to'g'rilang:</span>
            </div>
            <ul class="list-disc list-inside ml-7 space-y-1">
                @foreach($errors->all() as $error)
                    <li class="text-sm">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">@lang('Edit Product')</h1>
                <p class="text-gray-600 mt-1">@lang('Update your product information')</p>
            </div>
            <a href="{{ route('vendor.products.show', $product) }}"
               class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300">
                @lang('Back to Product')
            </a>
        </div>
    </div>

    <!-- Form -->
    <form method="POST" action="{{ route('vendor.products.update', $product) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Language Tabs -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">@lang('Language Selection')</h3>
                    
                    <div class="mb-4 border-b border-gray-200">
                        <nav class="flex space-x-8" aria-label="Tabs">
                            @foreach($locales as $localeCode => $localeName)
                            <button type="button" 
                                    class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm {{ $loop->first ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                                    data-tab="{{ $localeCode }}">
                                {{ $localeName }}
                            </button>
                            @endforeach
                        </nav>
                    </div>
                    
                    <!-- Language-specific content -->
                    @foreach($locales as $localeCode => $localeName)
                    <div class="tab-content {{ $loop->first ? 'block' : 'hidden' }}" id="tab-{{ $localeCode }}">
                        <!-- Product Name -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                @lang('Product Name') <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   name="{{ $localeCode === 'en' ? 'name' : 'name_translations[' . $localeCode . ']' }}"
                                   value="{{ old($localeCode === 'en' ? 'name' : 'name_translations.' . $localeCode, $localeCode === 'en' ? $product->getOriginal('name') : ($product->name_translations[$localeCode] ?? '')) }}"
                                   {{ $localeCode === 'en' ? 'required' : '' }}
                                   placeholder="@lang('e.g., iPhone 15 Pro Max')"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error(($localeCode === 'en' ? 'name' : 'name_translations.' . $localeCode)) border-red-500 @enderror">
                            @error($localeCode === 'en' ? 'name' : 'name_translations.' . $localeCode)
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                @lang('Description') <span class="text-red-500">*</span>
                            </label>
                            <textarea name="{{ $localeCode === 'en' ? 'description' : 'description_translations[' . $localeCode . ']' }}"
                                      rows="6"
                                      {{ $localeCode === 'en' ? 'required' : '' }}
                                      placeholder="@lang('Describe your product in detail...')"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error(($localeCode === 'en' ? 'description' : 'description_translations.' . $localeCode)) border-red-500 @enderror">{{ old($localeCode === 'en' ? 'description' : 'description_translations.' . $localeCode, $localeCode === 'en' ? $product->getOriginal('description') : ($product->description_translations[$localeCode] ?? '')) }}</textarea>
                            @error($localeCode === 'en' ? 'description' : 'description_translations.' . $localeCode)
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Short Description -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                @lang('Short Description')
                            </label>
                            <textarea name="{{ $localeCode === 'en' ? 'short_description' : 'short_description_translations[' . $localeCode . ']' }}"
                                      rows="3"
                                      placeholder="@lang('Brief description of your product...')"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error(($localeCode === 'en' ? 'short_description' : 'short_description_translations.' . $localeCode)) border-red-500 @enderror">{{ old($localeCode === 'en' ? 'short_description' : 'short_description_translations.' . $localeCode, $localeCode === 'en' ? $product->getOriginal('short_description') : ($product->short_description_translations[$localeCode] ?? '')) }}</textarea>
                            @error($localeCode === 'en' ? 'short_description' : 'short_description_translations.' . $localeCode)
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Product Images -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">@lang('Product Images')</h3>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            @lang('Upload New Images')
                        </label>
                        <input type="file"
                               name="images[]"
                               multiple
                               accept="image/*"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('images') border-red-500 @enderror">
                        @error('images')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">@lang('You can select multiple images. First image will be the primary image.')</p>
                    </div>

                    @if(($product->images ?? false) && $product->images->count() > 0)
                        <div class="mt-6">
                            <h4 class="text-md font-medium text-gray-900 mb-3">@lang('Current Images')</h4>
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                                @foreach($product->images as $image)
                                    <div class="relative group">
                                        <img src="{{ asset('storage/' . $image->image_path) }}" 
                                             alt="{{ $product->name ?? 'Product' }}" 
                                             class="w-full h-32 object-cover rounded-lg">
                                        
                                        @if($image->is_primary)
                                            <span class="absolute top-2 left-2 bg-blue-600 text-white text-xs px-2 py-1 rounded">
                                                @lang('Primary')
                                            </span>
                                        @endif
                                        
                                        <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity rounded-lg flex items-center justify-center">
                                            <button type="button"
                                                    onclick="confirmDeleteImage({{ $image->id }})"
                                                    class="text-white hover:text-red-300">
                                                <i class="fas fa-trash"></i> @lang('Delete')
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Pricing -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">@lang('Pricing')</h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Price -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                @lang('Price') <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-2.5 text-gray-500">$</span>
                                <input type="number"
                                       name="price"
                                       value="{{ old('price', $product->price ?? '') }}"
                                       step="0.01"
                                       min="0"
                                       required
                                       placeholder="0.00"
                                       class="w-full pl-8 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('price') border-red-500 @enderror">
                            </div>
                            @error('price')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Compare Price -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                @lang('Compare Price (Optional)')
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-2.5 text-gray-500">$</span>
                                <input type="number"
                                       name="compare_price"
                                       value="{{ old('compare_price', $product->old_price ?? '') }}"
                                       step="0.01"
                                       min="0"
                                       placeholder="0.00"
                                       class="w-full pl-8 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('compare_price') border-red-500 @enderror">
                            </div>
                            @error('compare_price')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Cost Price -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                @lang('Cost Price (Optional)')
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-2.5 text-gray-500">$</span>
                                <input type="number"
                                       name="cost_price"
                                       value="{{ old('cost_price', $product->cost_price ?? '') }}"
                                       step="0.01"
                                       min="0"
                                       placeholder="0.00"
                                       class="w-full pl-8 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('cost_price') border-red-500 @enderror">
                            </div>
                            @error('cost_price')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Inventory -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">@lang('Inventory')</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- SKU -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                @lang('SKU') <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   name="sku"
                                   value="{{ old('sku', $product->sku ?? '') }}"
                                   required
                                   placeholder="@lang('e.g., IPH15PM-256-BLK')"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('sku') border-red-500 @enderror">
                            @error('sku')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Stock -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                @lang('Stock Quantity') <span class="text-red-500">*</span>
                            </label>
                            <input type="number"
                                   name="stock"
                                   value="{{ old('stock', $product->stock ?? '') }}"
                                   min="0"
                                   required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('stock') border-red-500 @enderror">
                            @error('stock')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Publish Settings -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">@lang('Publish Settings')</h3>

                    <div class="space-y-4">
                        <!-- Status -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                @lang('Status')
                            </label>
                            <div class="flex items-center">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox"
                                       name="is_active"
                                       value="1"
                                       {{ old('is_active', $product->is_active ?? false) ? 'checked' : '' }}
                                       class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <label class="ml-2 block text-sm text-gray-900">
                                    @lang('Active')
                                </label>
                            </div>
                            <p class="mt-1 text-sm text-gray-500">@lang('Uncheck to make product inactive')</p>
                        </div>

                        <!-- Featured -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                @lang('Featured')
                            </label>
                            <div class="flex items-center">
                                <input type="hidden" name="is_featured" value="0">
                                <input type="checkbox"
                                       name="is_featured"
                                       value="1"
                                       {{ old('is_featured', $product->is_featured ?? false) ? 'checked' : '' }}
                                       class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <label class="ml-2 block text-sm text-gray-900">
                                    @lang('Featured Product')
                                </label>
                            </div>
                            <p class="mt-1 text-sm text-gray-500">@lang('Check to feature this product')</p>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="mt-6 pt-4 border-t border-gray-200">
                        <button type="submit"
                                class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                            @lang('Update Product')
                        </button>
                    </div>
                </div>

                <!-- Categories -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">@lang('Categories')</h3>

                    <div class="space-y-4">
                        <!-- Category -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                @lang('Category') <span class="text-red-500">*</span>
                            </label>
                            <select name="category_id"
                                    id="category-select"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('category_id') border-red-500 @enderror">
                                <option value="">@lang('Select Category')</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $product->category_id ?? '') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Subcategory -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                @lang('Subcategory')
                            </label>
                            <select name="sub_category_id"
                                    id="subcategory-select"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('sub_category_id') border-red-500 @enderror">
                                <option value="">@lang('Select Subcategory')</option>
                                @if($subCategories ?? false)
                                    @foreach($subCategories as $subcategory)
                                        <option value="{{ $subcategory->id }}" {{ old('sub_category_id', $product->sub_category_id ?? '') == $subcategory->id ? 'selected' : '' }}>
                                            {{ $subcategory->name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @error('sub_category_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Brand -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                @lang('Brand')
                            </label>
                            <select name="brand_id"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('brand_id') border-red-500 @enderror">
                                <option value="">@lang('Select Brand')</option>
                                @foreach($brands as $brand)
                                    <option value="{{ $brand->id }}" {{ old('brand_id', $product->brand_id ?? '') == $brand->id ? 'selected' : '' }}>
                                        {{ $brand->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('brand_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- SEO -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">@lang('SEO')</h3>
                    
                    <!-- Language Tabs for SEO -->
                    <div class="mb-4 border-b border-gray-200">
                        <nav class="flex space-x-8" aria-label="Tabs">
                            @foreach($locales as $localeCode => $localeName)
                            <button type="button" 
                                    class="seo-tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm {{ $loop->first ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                                    data-tab="{{ $localeCode }}">
                                {{ $localeName }}
                            </button>
                            @endforeach
                        </nav>
                    </div>
                    
                    <!-- Language-specific SEO content -->
                    @foreach($locales as $localeCode => $localeName)
                    <div class="seo-tab-content {{ $loop->first ? 'block' : 'hidden' }}" id="seo-tab-{{ $localeCode }}">
                        <!-- Meta Title -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                @lang('Meta Title')
                            </label>
                            <input type="text"
                                   name="{{ $localeCode === 'en' ? 'meta_title' : 'meta_title_translations[' . $localeCode . ']' }}"
                                   value="{{ old($localeCode === 'en' ? 'meta_title' : 'meta_title_translations.' . $localeCode, $localeCode === 'en' ? $product->getOriginal('meta_title') : ($product->meta_title_translations[$localeCode] ?? '')) }}"
                                   placeholder="@lang('Product meta title')"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error(($localeCode === 'en' ? 'meta_title' : 'meta_title_translations.' . $localeCode)) border-red-500 @enderror">
                            @error($localeCode === 'en' ? 'meta_title' : 'meta_title_translations.' . $localeCode)
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Meta Description -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                @lang('Meta Description')
                            </label>
                            <textarea name="{{ $localeCode === 'en' ? 'meta_description' : 'meta_description_translations[' . $localeCode . ']' }}"
                                      rows="3"
                                      placeholder="@lang('Product meta description')"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error(($localeCode === 'en' ? 'meta_description' : 'meta_description_translations.' . $localeCode)) border-red-500 @enderror">{{ old($localeCode === 'en' ? 'meta_description' : 'meta_description_translations.' . $localeCode, $localeCode === 'en' ? $product->getOriginal('meta_description') : ($product->meta_description_translations[$localeCode] ?? '')) }}</textarea>
                            @error($localeCode === 'en' ? 'meta_description' : 'meta_description_translations.' . $localeCode)
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Meta Keywords -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                @lang('Meta Keywords')
                            </label>
                            <input type="text"
                                   name="{{ $localeCode === 'en' ? 'meta_keywords' : 'meta_keywords_translations[' . $localeCode . ']' }}"
                                   value="{{ old($localeCode === 'en' ? 'meta_keywords' : 'meta_keywords_translations.' . $localeCode, $localeCode === 'en' ? $product->getOriginal('meta_keywords') : ($product->meta_keywords_translations[$localeCode] ?? '')) }}"
                                   placeholder="@lang('keyword1, keyword2, keyword3')"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error(($localeCode === 'en' ? 'meta_keywords' : 'meta_keywords_translations.' . $localeCode)) border-red-500 @enderror">
                            @error($localeCode === 'en' ? 'meta_keywords' : 'meta_keywords_translations.' . $localeCode)
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Delete Image Confirmation Modal -->
<div id="delete-image-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md mx-4">
        <h3 class="text-lg font-semibold text-gray-900 mb-2">@lang('Delete Image')</h3>
        <p class="text-gray-600 mb-6">@lang('Are you sure you want to delete this image? This action cannot be undone.')</p>
        <div class="flex justify-end space-x-3">
            <button onclick="closeDeleteModal()" 
                    class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300">
                @lang('Cancel')
            </button>
            <button id="confirm-delete-btn" 
                    class="px-4 py-2 text-white bg-red-600 rounded-lg hover:bg-red-700">
                @lang('Delete')
            </button>
        </div>
    </div>
</div>

<script>
    // Handle tab switching functionality
    document.addEventListener('DOMContentLoaded', function() {
        // Main content tabs
        const tabButtons = document.querySelectorAll('.tab-button');
        const tabContents = document.querySelectorAll('.tab-content');
        
        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                const tabId = this.getAttribute('data-tab');
                
                // Remove active classes from all buttons and contents
                tabButtons.forEach(btn => btn.classList.remove('border-blue-500', 'text-blue-600'));
                tabButtons.forEach(btn => btn.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300'));
                tabContents.forEach(content => content.classList.add('hidden'));
                
                // Add active classes to current button and content
                this.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
                this.classList.add('border-blue-500', 'text-blue-600');
                document.getElementById('tab-' + tabId).classList.remove('hidden');
                document.getElementById('tab-' + tabId).classList.add('block');
            });
        });
        
        // SEO tabs
        const seoTabButtons = document.querySelectorAll('.seo-tab-button');
        const seoTabContents = document.querySelectorAll('.seo-tab-content');
        
        seoTabButtons.forEach(button => {
            button.addEventListener('click', function() {
                const tabId = this.getAttribute('data-tab');
                
                // Remove active classes from all buttons and contents
                seoTabButtons.forEach(btn => btn.classList.remove('border-blue-500', 'text-blue-600'));
                seoTabButtons.forEach(btn => btn.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300'));
                seoTabContents.forEach(content => content.classList.add('hidden'));
                
                // Add active classes to current button and content
                this.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
                this.classList.add('border-blue-500', 'text-blue-600');
                document.getElementById('seo-tab-' + tabId).classList.remove('hidden');
                document.getElementById('seo-tab-' + tabId).classList.add('block');
            });
        });
        
        // Handle subcategory loading based on category selection
        document.getElementById('category-select').addEventListener('change', function() {
            const categoryId = this.value;
            const subcategorySelect = document.getElementById('subcategory-select');
            
            // Clear existing options
            subcategorySelect.innerHTML = '<option value="">@lang('Select Subcategory')</option>';
            
            if (categoryId) {
                // Fetch subcategories for selected category
                fetch(`{{ route('vendor.products.sub-categories', ['categoryId' => '__categoryId__']) }}`.replace('__categoryId__', categoryId))
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(subcategory => {
                            const option = document.createElement('option');
                            option.value = subcategory.id;
                            option.textContent = subcategory.name;
                            subcategorySelect.appendChild(option);
                        });
                    })
                    .catch(error => console.error('Error:', error));
            }
        });

        // Handle image deletion
        let imageToDelete = null;

        function confirmDeleteImage(imageId) {
            imageToDelete = imageId;
            document.getElementById('delete-image-modal').classList.remove('hidden');
            document.getElementById('delete-image-modal').classList.add('flex');
        }

        function closeDeleteModal() {
            document.getElementById('delete-image-modal').classList.add('hidden');
            document.getElementById('delete-image-modal').classList.remove('flex');
            imageToDelete = null;
        }

        document.getElementById('confirm-delete-btn').addEventListener('click', function() {
            if (imageToDelete) {
                // Submit delete request
                fetch(`/vendor/products/{{ $product->id ?? '' }}/images/${imageToDelete}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Failed to delete image: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while deleting the image.');
                });
                
                closeDeleteModal();
            }
        });

        // Close modal when clicking outside
        document.getElementById('delete-image-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDeleteModal();
            }
        });
    });
</script>
@endsection