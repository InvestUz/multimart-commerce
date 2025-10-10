@extends('layouts.app')

@section('title', 'My Wishlist')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">My Wishlist</h1>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if($wishlists->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($wishlists as $wishlist)
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                    <div class="relative">
                        <a href="{{ route('product.show', $wishlist->product->slug) }}">
                            @if($wishlist->product->images->first())
                                <img src="{{ asset('storage/' . $wishlist->product->images->first()->image_path) }}" 
                                     alt="{{ $wishlist->product->name }}" 
                                     class="w-full h-64 object-cover">
                            @else
                                <div class="w-full h-64 bg-gray-200 flex items-center justify-center">
                                    <span class="text-gray-400">No image</span>
                                </div>
                            @endif
                        </a>
                        
                        <!-- Remove from Wishlist Button -->
                        <form action="{{ route('wishlist.destroy', $wishlist) }}" method="POST" class="absolute top-2 right-2">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-white rounded-full p-2 shadow-md hover:bg-red-50 transition">
                                <svg class="w-6 h-6 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path>
                                </svg>
                            </button>
                        </form>

                        <!-- Stock Badge -->
                        @if($wishlist->product->stock < 1)
                            <div class="absolute top-2 left-2">
                                <span class="bg-red-500 text-white text-xs px-2 py-1 rounded">Out of Stock</span>
                            </div>
                        @elseif($wishlist->product->stock < 10)
                            <div class="absolute top-2 left-2">
                                <span class="bg-yellow-500 text-white text-xs px-2 py-1 rounded">Low Stock</span>
                            </div>
                        @endif
                    </div>

                    <div class="p-4">
                        <a href="{{ route('product.show', $wishlist->product->slug) }}" class="block">
                            <h3 class="text-lg font-semibold text-gray-900 hover:text-blue-600 mb-2">
                                {{ Str::limit($wishlist->product->name, 50) }}
                            </h3>
                        </a>

                        <p class="text-sm text-gray-600 mb-2">{{ $wishlist->product->vendor->shop_name ?? $wishlist->product->vendor->name }}</p>

                        <!-- Rating -->
                        @if($wishlist->product->reviews_count > 0)
                            <div class="flex items-center mb-3">
                                <div class="flex">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-4 h-4 {{ $i <= round($wishlist->product->reviews_avg_rating) ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                    @endfor
                                </div>
                                <span class="text-sm text-gray-600 ml-2">({{ $wishlist->product->reviews_count }})</span>
                            </div>
                        @endif

                        <!-- Price -->
                        <div class="mb-4">
                            <div class="flex items-center">
                                <span class="text-2xl font-bold text-gray-900">${{ number_format($wishlist->product->price, 2) }}</span>
                                @if($wishlist->product->compare_price && $wishlist->product->compare_price > $wishlist->product->price)
                                    <span class="text-sm text-gray-500 line-through ml-2">${{ number_format($wishlist->product->compare_price, 2) }}</span>
                                @endif
                            </div>
                            @if($wishlist->product->compare_price && $wishlist->product->compare_price > $wishlist->product->price)
                                <span class="text-sm text-green-600 font-semibold">
                                    Save {{ round((($wishlist->product->compare_price - $wishlist->product->price) / $wishlist->product->compare_price) * 100) }}%
                                </span>
                            @endif
                        </div>

                        <!-- Add to Cart Button -->
                        @if($wishlist->product->stock > 0 && $wishlist->product->is_active)
                            <form action="{{ route('cart.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $wishlist->product->id }}">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition">
                                    Add to Cart
                                </button>
                            </form>
                        @else
                            <button disabled class="w-full bg-gray-300 text-gray-500 font-semibold py-2 px-4 rounded-lg cursor-not-allowed">
                                Out of Stock
                            </button>
                        @endif

                        <p class="text-xs text-gray-500 mt-2">Added {{ $wishlist->created_at->diffForHumans() }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $wishlists->links() }}
        </div>
    @else
        <div class="bg-white rounded-lg shadow-md p-12 text-center">
            <svg class="w-24 h-24 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
            </svg>
            <h2 class="text-2xl font-semibold text-gray-900 mb-2">Your wishlist is empty</h2>
            <p class="text-gray-600 mb-6">Start adding products you love to your wishlist!</p>
            <a href="{{ route('home') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition">
                Continue Shopping
            </a>
        </div>
    @endif
</div>
@endsection