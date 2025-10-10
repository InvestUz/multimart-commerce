@extends('layouts.app')

@section('title', $category->name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <nav class="mb-6 text-sm">
        <ol class="flex items-center space-x-2 text-gray-600">
            <li><a href="{{ route('dashboard') }}" class="hover:text-blue-600">Home</a></li>
            <li><span class="mx-2">/</span></li>
            @if($category->parent)
                <li><a href="{{ route('category.show', $category->parent->slug) }}" class="hover:text-blue-600">{{ $category->parent->name }}</a></li>
                <li><span class="mx-2">/</span></li>
            @endif
            <li class="text-gray-900">{{ $category->name }}</li>
        </ol>
    </nav>

    <!-- Category Header -->
    <div class="mb-8">
        <div class="flex items-start justify-between">
            <div class="flex-1">
                <h1 class="text-4xl font-bold text-gray-900 mb-2">{{ $category->name }}</h1>
                @if($category->description)
                    <p class="text-gray-600 max-w-3xl">{{ $category->description }}</p>
                @endif
            </div>
            @if($category->image)
                <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" class="w-32 h-32 object-cover rounded-lg ml-6">
            @endif
        </div>
    </div>

    <!-- Subcategories -->
    @if($category->children && $category->children->count() > 0)
        <div class="mb-8">
            <h2 class="text-xl font-semibold mb-4">Shop by Subcategory</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                @foreach($category->children as $subcategory)
                    <a href="{{ route('category.show', $subcategory->slug) }}" class="bg-white rounded-lg shadow hover:shadow-md transition-shadow p-4 text-center">
                        @if($subcategory->image)
                            <img src="{{ asset('storage/' . $subcategory->image) }}" alt="{{ $subcategory->name }}" class="w-full h-24 object-cover rounded-lg mb-2">
                        @else
                            <div class="w-full h-24 bg-gray-200 rounded-lg mb-2 flex items-center justify-center">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                                </svg>
                            </div>
                        @endif
                        <h3 class="text-sm font-semibold text-gray-900">{{ $subcategory->name }}</h3>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Filters and Sorting -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form action="{{ route('category.show', $category->slug) }}" method="GET" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search in {{ $category->name }}..." 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <div>
                <select name="sort" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Sort By</option>
                    <option value="featured" {{ request('sort') == 'featured' ? 'selected' : '' }}>Featured</option>
                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest</option>
                    <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                    <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                    <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Highest Rated</option>
                    <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Most Popular</option>
                </select>
            </div>

            <div>
                <select name="price_range" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Prices</option>
                    <option value="0-50" {{ request('price_range') == '0-50' ? 'selected' : '' }}>Under $50</option>
                    <option value="50-100" {{ request('price_range') == '50-100' ? 'selected' : '' }}>$50 - $100</option>
                    <option value="100-200" {{ request('price_range') == '100-200' ? 'selected' : '' }}>$100 - $200</option>
                    <option value="200-500" {{ request('price_range') == '200-500' ? 'selected' : '' }}>$200 - $500</option>
                    <option value="500" {{ request('price_range') == '500' ? 'selected' : '' }}>$500+</option>
                </select>
            </div>

            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Apply
            </button>

            @if(request()->has('search') || request()->has('sort') || request()->has('price_range'))
                <a href="{{ route('category.show', $category->slug) }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    Clear
                </a>
            @endif
        </form>
    </div>

    <!-- Products Count -->
    @if(isset($products))
        <div class="mb-4">
            <p class="text-gray-600">
                Showing {{ $products->count() }} of {{ $products->total() }} {{ Str::plural('product', $products->total()) }}
            </p>
        </div>
    @endif

    <!-- Products Grid -->
    @if(isset($products) && $products->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($products as $product)
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                    <div class="relative">
                        <a href="{{ route('product.show', $product->slug) }}">
                            @if($product->images->first())
                                <img src="{{ asset('storage/' . $product->images->first()->image_path) }}" 
                                     alt="{{ $product->name }}" 
                                     class="w-full h-64 object-cover">
                            @else
                                <div class="w-full h-64 bg-gray-200 flex items-center justify-center">
                                    <span class="text-gray-400">No image</span>
                                </div>
                            @endif
                        </a>

                        <!-- Wishlist Button -->
                        @auth
                            <form action="{{ route('wishlist.toggle') }}" method="POST" class="absolute top-2 right-2">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <button type="submit" class="bg-white rounded-full p-2 shadow-md hover:bg-gray-50 transition">
                                    <svg class="w-6 h-6 {{ $product->is_wishlisted ?? false ? 'text-red-500 fill-current' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                    </svg>
                                </button>
                            </form>
                        @endauth

                        <!-- Badges -->
                        <div class="absolute top-2 left-2 space-y-2">
                            @if($product->is_featured)
                                <span class="block bg-yellow-500 text-white text-xs px-2 py-1 rounded">Featured</span>
                            @endif
                            @if($product->stock < 1)
                                <span class="block bg-red-500 text-white text-xs px-2 py-1 rounded">Out of Stock</span>
                            @elseif($product->stock < 10)
                                <span class="block bg-orange-500 text-white text-xs px-2 py-1 rounded">Low Stock</span>
                            @endif
                            @if($product->compare_price && $product->compare_price > $product->price)
                                <span class="block bg-green-500 text-white text-xs px-2 py-1 rounded">
                                    -{{ round((($product->compare_price - $product->price) / $product->compare_price) * 100) }}%
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="p-4">
                        <a href="{{ route('product.show', $product->slug) }}" class="block">
                            <h3 class="text-lg font-semibold text-gray-900 hover:text-blue-600 mb-2 h-14 overflow-hidden">
                                {{ Str::limit($product->name, 50) }}
                            </h3>
                        </a>

                        <p class="text-sm text-gray-600 mb-2">{{ $product->vendor->shop_name ?? $product->vendor->name }}</p>

                        <!-- Rating -->
                        @if($product->reviews_count > 0)
                            <div class="flex items-center mb-3">
                                <div class="flex">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-4 h-4 {{ $i <= round($product->reviews_avg_rating) ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                    @endfor
                                </div>
                                <span class="text-sm text-gray-600 ml-2">({{ $product->reviews_count }})</span>
                            </div>
                        @endif

                        <!-- Price -->
                        <div class="mb-4">
                            <div class="flex items-center">
                                <span class="text-2xl font-bold text-gray-900">${{ number_format($product->price, 2) }}</span>
                                @if($product->compare_price && $product->compare_price > $product->price)
                                    <span class="text-sm text-gray-500 line-through ml-2">${{ number_format($product->compare_price, 2) }}</span>
                                @endif
                            </div>
                        </div>

                        <!-- Add to Cart Button -->
                        @if($product->stock > 0 && $product->is_active)
                            @auth
                                <form action="{{ route('cart.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition">
                                        Add to Cart
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('login') }}" class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg text-center transition">
                                    Login to Add
                                </a>
                            @endauth
                        @else
                            <button disabled class="w-full bg-gray-300 text-gray-500 font-semibold py-2 px-4 rounded-lg cursor-not-allowed">
                                Out of Stock
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $products->appends(request()->query())->links() }}
        </div>
    @else
        <!-- No Products -->
        <div class="bg-white rounded-lg shadow-md p-12 text-center">
            <svg class="w-24 h-24 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
            </svg>
            <h2 class="text-2xl font-semibold text-gray-900 mb-2">No products found</h2>
            <p class="text-gray-600 mb-6">We couldn't find any products in this category.</p>
            @if(request()->has('search') || request()->has('sort') || request()->has('price_range'))
                <a href="{{ route('category.show', $category->slug) }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition">
                    Clear Filters
                </a>
            @else
                <a href="{{ route('dashboard') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition">
                    Browse All Products
                </a>
            @endif
        </div>
    @endif
</div>
@endsection