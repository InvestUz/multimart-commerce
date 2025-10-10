@extends('layouts.app')

@section('title', 'Home - Online Marketplace')

@section('content')
<!-- Hero Section -->
<div class="bg-gradient-to-r from-blue-600 to-blue-800 text-white">
    <div class="container mx-auto px-4 py-16">
        <div class="max-w-3xl">
            <h1 class="text-5xl font-bold mb-4">Welcome to Our Marketplace</h1>
            <p class="text-xl mb-8">Discover amazing products from trusted vendors</p>
            <div class="flex gap-4">
                <a href="#featured" class="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition">
                    Shop Now
                </a>
                @guest
                    <a href="{{ route('register') }}" class="bg-transparent border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition">
                        Sign Up
                    </a>
                @endguest
            </div>
        </div>
    </div>
</div>

<!-- Categories Section -->
@if(isset($categories) && $categories->count() > 0)
    <div class="container mx-auto px-4 py-12">
        <h2 class="text-3xl font-bold mb-8">Shop by Category</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
            @foreach($categories as $category)
                <a href="{{ route('category.show', $category->slug) }}" class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow p-6 text-center group">
                    @if($category->image)
                        <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" class="w-full h-24 object-cover rounded-lg mb-3 group-hover:scale-105 transition-transform">
                    @else
                        <div class="w-full h-24 bg-gradient-to-br from-blue-100 to-blue-200 rounded-lg mb-3 flex items-center justify-center">
                            <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                            </svg>
                        </div>
                    @endif
                    <h3 class="font-semibold text-gray-900 group-hover:text-blue-600 transition">{{ $category->name }}</h3>
                    @if(isset($category->products_count))
                        <p class="text-sm text-gray-500 mt-1">{{ $category->products_count }} products</p>
                    @endif
                </a>
            @endforeach
        </div>
    </div>
@endif

<!-- Featured Products -->
@if(isset($featuredProducts) && $featuredProducts->count() > 0)
    <div id="featured" class="bg-gray-50 py-12">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-3xl font-bold">Featured Products</h2>
                <a href="{{ route('search') }}" class="text-blue-600 hover:text-blue-800 font-semibold">View All →</a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($featuredProducts as $product)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow">
                        <div class="relative">
                            <a href="{{ route('product.show', $product->slug) }}">
                                @if($product->images->first())
                                    @php
                                        $imagePath = $product->images->first()->image_path;
                                        $imageUrl = Str::startsWith($imagePath, 'http') ? $imagePath : asset('storage/' . $imagePath);
                                    @endphp
                                    <img src="{{ $imageUrl }}" alt="{{ $product->name }}" class="w-full h-64 object-cover">
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

                            <!-- Featured Badge -->
                            <div class="absolute top-2 left-2">
                                <span class="bg-yellow-500 text-white text-xs px-2 py-1 rounded font-semibold">★ Featured</span>
                            </div>
                        </div>

                        <div class="p-4">
                            <a href="{{ route('product.show', $product->slug) }}" class="block">
                                <h3 class="text-lg font-semibold text-gray-900 hover:text-blue-600 mb-2 h-12 overflow-hidden">
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
        </div>
    </div>
@endif

<!-- Latest Products -->
@if(isset($latestProducts) && $latestProducts->count() > 0)
    <div class="container mx-auto px-4 py-12">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-3xl font-bold">New Arrivals</h2>
            <a href="{{ route('search', ['sort' => 'newest']) }}" class="text-blue-600 hover:text-blue-800 font-semibold">View All →</a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($latestProducts as $product)
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow">
                    <div class="relative">
                        <a href="{{ route('product.show', $product->slug) }}">
                            @if($product->images->first())
                                @php
                                    $imagePath = $product->images->first()->image_path;
                                    $imageUrl = Str::startsWith($imagePath, 'http') ? $imagePath : asset('storage/' . $imagePath);
                                @endphp
                                <img src="{{ $imageUrl }}" alt="{{ $product->name }}" class="w-full h-64 object-cover">
                            @else
                                <div class="w-full h-64 bg-gray-200 flex items-center justify-center">
                                    <span class="text-gray-400">No image</span>
                                </div>
                            @endif
                        </a>

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

                        <div class="absolute top-2 left-2">
                            <span class="bg-green-500 text-white text-xs px-2 py-1 rounded font-semibold">New</span>
                        </div>
                    </div>

                    <div class="p-4">
                        <a href="{{ route('product.show', $product->slug) }}" class="block">
                            <h3 class="text-lg font-semibold text-gray-900 hover:text-blue-600 mb-2 h-12 overflow-hidden">
                                {{ Str::limit($product->name, 50) }}
                            </h3>
                        </a>

                        <p class="text-sm text-gray-600 mb-2">{{ $product->vendor->shop_name ?? $product->vendor->name }}</p>

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

                        <div class="mb-4">
                            <span class="text-2xl font-bold text-gray-900">${{ number_format($product->price, 2) }}</span>
                        </div>

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
    </div>
@endif

<!-- Call to Action for Vendors -->
<div class="bg-blue-600 text-white py-16">
    <div class="container mx-auto px-4 text-center">
        <h2 class="text-4xl font-bold mb-4">Start Selling Today</h2>
        <p class="text-xl mb-8 max-w-2xl mx-auto">Join thousands of vendors and grow your business with our platform</p>
        @guest
            <a href="{{ route('register') }}" class="inline-block bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition">
                Become a Vendor
            </a>
        @endguest
    </div>
</div>
@endsection