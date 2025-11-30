@extends('layouts.vendor')

@section('title', __('Add Product'))

@section('content')
<div class="p-6">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">@lang('Add Product')</h1>
                <p class="text-gray-600 mt-1">@lang('Add a new product to your store')</p>
            </div>
            <a href="{{ route('vendor.products.index') }}"
               class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300">
                @lang('Back to Products')
            </a>
        </div>
    </div>

    <!-- Form -->
    <form method="POST" action="{{ route('vendor.products.store') }}" enctype="multipart/form-data">
        @csrf

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
                                   value="{{ old($localeCode === 'en' ? 'name' : 'name_translations.' . $localeCode) }}"
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
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error(($localeCode === 'en' ? 'description' : 'description_translations.' . $localeCode)) border-red-500 @enderror">{{ old($localeCode === 'en' ? 'description' : 'description_translations.' . $localeCode) }}</textarea>
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
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error(($localeCode === 'en' ? 'short_description' : 'short_description_translations.' . $localeCode)) border-red-500 @enderror">{{ old($localeCode === 'en' ? 'short_description' : 'short_description_translations.' . $localeCode) }}</textarea>
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
                            @lang('Upload Images') <span class="text-red-500">*</span>
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
                                       value="{{ old('price') }}"
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
                                       value="{{ old('compare_price') }}"
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
                                       value="{{ old('cost_price') }}"
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
                                   value="{{ old('sku') }}"
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
                                   value="{{ old('stock', 0) }}"
                                   min="0"
                                   required
                                   placeholder="0"
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
                <!-- Organization -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">@lang('Organization')</h3>

                    <div class="space-y-4">
                        <!-- Category -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                @lang('Category') <span class="text-red-500">*</span>
                            </label>
                            <select name="category_id" 
                                    id="category_id"
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('category_id') border-red-500 @enderror">
                                <option value="">@lang('Select a category')</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Sub Category -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                @lang('Sub Category')
                            </label>
                            <select name="sub_category_id" 
                                    id="sub_category_id"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('sub_category_id') border-red-500 @enderror">
                                <option value="">@lang('Select a sub-category')</option>
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
                                <option value="">@lang('Select a brand')</option>
                                @foreach($brands as $brand)
                                    <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
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

                <!-- Visibility -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">@lang('Visibility')</h3>

                    <div class="space-y-4">
                        <!-- Status -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                @lang('Status')
                            </label>
                            <div class="flex items-center">
                                <input type="checkbox" 
                                       name="is_active" 
                                       id="is_active"
                                       {{ old('is_active', true) ? 'checked' : '' }}
                                       class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <label for="is_active" class="ml-2 block text-sm text-gray-900">
                                    @lang('Active')
                                </label>
                            </div>
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
                                   value="{{ old($localeCode === 'en' ? 'meta_title' : 'meta_title_translations.' . $localeCode) }}"
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
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error(($localeCode === 'en' ? 'meta_description' : 'meta_description_translations.' . $localeCode)) border-red-500 @enderror">{{ old($localeCode === 'en' ? 'meta_description' : 'meta_description_translations.' . $localeCode) }}</textarea>
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
                                   value="{{ old($localeCode === 'en' ? 'meta_keywords' : 'meta_keywords_translations.' . $localeCode) }}"
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

        <!-- Submit Button -->
        <div class="mt-6">
            <button type="submit" 
                    class="px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                @lang('Create Product')
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab switching functionality
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
    
    // SEO Tab switching functionality
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
    
    // Category/Sub-category functionality
    const categorySelect = document.getElementById('category_id');
    const subCategorySelect = document.getElementById('sub_category_id');
    
    categorySelect.addEventListener('change', function() {
        const categoryId = this.value;
        
        // Clear sub-category options
        subCategorySelect.innerHTML = '<option value="">@lang('Select a sub-category')</option>';
        
        if (categoryId) {
            // Fetch sub-categories for selected category
            fetch(`{{ route('vendor.products.sub-categories', ['categoryId' => '__categoryId__']) }}`.replace('__categoryId__', categoryId))
                .then(response => response.json())
                .then(data => {
                    data.forEach(subCategory => {
                        const option = document.createElement('option');
                        option.value = subCategory.id;
                        option.textContent = subCategory.name;
                        subCategorySelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error:', error));
        }
    });
});
</script>
@endsection