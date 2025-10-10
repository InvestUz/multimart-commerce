@extends('layouts.app')

@section('title', 'Search Results')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Search Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold mb-4">Search Results</h1>
        
        <!-- Search Form -->
        <form action="{{ route('search') }}" method="GET" class="mb-4">
            <div class="flex gap-2">
                <input type="text" 
                       name="q" 
                       value="{{ request('q') }}" 
                       placeholder="Search for products..." 
                       class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       required>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-semibold">
                    Search
                </button>
            </div>
        </form>

        @if(request('q'))
            <p class="text-gray-600">
                Showing results for: <span class="font-semibold">"{{ request('q') }}"</span>
                @if(isset($products))
                    <span class="text-sm">({{ $products->total() }} {{ Str::plural('result', $products->total()) }})</span>
                @endif
            </p>
        @endif
    </div>

    <!-- Filters and Sorting -->
    @if(isset($products) && $products->count() > 0)
        <div class="bg-white rounded-lg shadow p-4 mb-6">
            <form action="{{ route('search') }}" method="GET" class="flex flex-wrap gap-4">
                <input type="hidden" name="q" value="{{ request('q') }}">
                
                <div class="flex-1 min-w-[200px]">
                    <select name="category" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">All Categories</option>
                        @foreach($categories ?? [] as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <select name="sort" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Sort By</option>
                        <option value="relevance" {{ request('sort') == 'relevance' ? 'selected' : '' }}>Relevance</option>
                        <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest</option>
                        <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Highest Rated</option>
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
                    Apply Filters
                </button>
            </form>
        </div>
    @endif

    <!-- Results -->
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

                        <!-- Stock Badge -->
                        @if($product->stock < 1)
                            <div class="absolute top-2 left-2">
                                <span class="bg-red-500 text-white text-xs px-2 py-1 rounded">Out of Stock</span>
                            </div>
                        @elseif($product->is_featured)
                            <div class="absolute top-2 left-2">
                                <span class="bg-yellow-500 text-white text-xs px-2 py-1 rounded">Featured</span>
                            </div>
                        @endif
                    </div>

                    <div class="p-4">
                        <a href="{{ route('product.show', $product->slug) }}" class="block">
                            <h3 class="text-lg font-semibold text-gray-900 hover:text-blue-600 mb-2">
                                {{ Str::limit($product->name, 50) }}
                            </h3>
                        </a>

                        <p class="text-sm text-gray-600 mb-2">{{ $product->vendor->shop_name ?? $product->vendor->name }}</p>

                        <!-- Category -->
                        @if($product->category)
                            <p class="text-xs text-gray-500 mb-2">{{ $product->category->name }}</p>
                        @endif

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
                            @if($product->compare_price && $product->compare_price > $product->price)
                                <span class="text-sm text-green-600 font-semibold">
                                    Save {{ round((($product->compare_price - $product->price) / $product->compare_price) * 100) }}%
                                </span>
                            @endif
                        </div>

                        <!-- Add to Cart Button -->
                        @if($product->stock > 0)
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
                                    Login to Add to Cart
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
    @elseif(request('q'))
        <!-- No Results -->
        <div class="bg-white rounded-lg shadow-md p-12 text-center">
            <svg class="w-24 h-24 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <h2 class="text-2xl font-semibold text-gray-900 mb-2">No products found</h2>
            <p class="text-gray-600 mb-6">We couldn't find any products matching "{{ request('q') }}"</p>
            <div class="space-y-2">
                <p class="text-sm text-gray-600">Try:</p>
                <ul class="text-sm text-gray-600 list-disc list-inside">
                    <li>Checking your spelling</li>
                    <li>Using more general terms</li>
                    <li>Using fewer keywords</li>
                </ul>
            </div>
            <a href="{{ route('dashboard') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition mt-6">
                Browse All Products
            </a>
        </div>
    @else
        <!-- Initial State -->
        <div class="bg-white rounded-lg shadow-md p-12 text-center">
            <svg class="w-24 h-24 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <h2 class="text-2xl font-semibold text-gray-900 mb-2">Search for products</h2>
            <p class="text-gray-600 mb-6">Enter keywords to find what you're looking for</p>
        </div>
    @endif
</div>
@endsection