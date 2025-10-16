@extends('layouts.app')

@section('title', $category->name)

@section('content')
    <div class="container mx-auto px-4 py-8">
        <!-- Breadcrumb -->
        <nav class="mb-6 text-sm">
            <ol class="flex items-center space-x-2 text-gray-600">
                <li><a href="{{ route('dashboard') }}" class="hover:text-primary">Home</a></li>
                <li><span class="mx-2">/</span></li>
                <li class="text-gray-900">{{ $category->name }}</li>
            </ol>
        </nav>

        <!-- Category Header -->
        <div class="mb-8">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <h1 class="text-4xl font-bold text-gray-900 mb-2">{{ $category->name }}</h1>
                    <p class="text-gray-600">Browse our collection of {{ $products->total() }} products</p>
                </div>
            </div>
        </div>

        <!-- Sub-Categories Section -->
        @if ($subCategories->count() > 0)
            <div class="mb-8">
                <h2 class="text-xl font-semibold mb-4">Filter by Sub-Category</h2>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3">
                    <a href="{{ route('category.show', $category->slug) }}"
                        class="p-3 border-2 rounded-lg text-center transition {{ !request('sub_category') ? 'border-primary bg-blue-50' : 'border-gray-200 hover:border-gray-300' }}">
                        <p class="font-semibold">All</p>
                        <p class="text-xs text-gray-600">{{ $products->total() }} products</p>
                    </a>
                    @foreach ($subCategories as $subCategory)
                        <a href="{{ route('category.show', $category->slug) }}?sub_category={{ $subCategory->id }}"
                            class="p-3 border-2 rounded-lg text-center transition {{ request('sub_category') == $subCategory->id ? 'border-primary bg-blue-50' : 'border-gray-200 hover:border-gray-300' }}">
                            <p class="font-semibold text-sm">{{ $subCategory->name }}</p>
                            <p class="text-xs text-gray-600">{{ $subCategory->products_count }} products</p>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Filters and Sorting -->
        <div class="bg-white rounded-lg shadow p-4 mb-6">
            <form action="{{ route('category.show', $category->slug) }}" method="GET" class="flex flex-wrap gap-4">
                <!-- Keep sub_category filter in URL -->
                @if (request('sub_category'))
                    <input type="hidden" name="sub_category" value="{{ request('sub_category') }}">
                @endif

                <div class="flex-1 min-w-[200px]">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>

                <div>
                    <select name="sort"
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                        <option value="">Sort By</option>
                        <option value="featured" {{ request('sort') == 'featured' ? 'selected' : '' }}>Featured</option>
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest</option>
                        <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High
                        </option>
                        <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to
                            Low</option>
                        <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Highest Rated</option>
                        <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Most Popular</option>
                    </select>
                </div>

                <div>
                    <select name="price_range"
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                        <option value="">All Prices</option>
                        <option value="0-50" {{ request('price_range') == '0-50' ? 'selected' : '' }}>Under $50</option>
                        <option value="50-100" {{ request('price_range') == '50-100' ? 'selected' : '' }}>$50 - $100
                        </option>
                        <option value="100-200" {{ request('price_range') == '100-200' ? 'selected' : '' }}>$100 - $200
                        </option>
                        <option value="200-500" {{ request('price_range') == '200-500' ? 'selected' : '' }}>$200 - $500
                        </option>
                        <option value="500" {{ request('price_range') == '500' ? 'selected' : '' }}>$500+</option>
                    </select>
                </div>

                <button type="submit" class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-green-600">
                    Apply
                </button>

                @if (request()->has('search') ||
                        request()->has('sort') ||
                        request()->has('price_range') ||
                        request()->has('sub_category'))
                    <a href="{{ route('category.show', $category->slug) }}"
                        class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                        Clear
                    </a>
                @endif
            </form>
        </div>

        <!-- Products Count -->
        @if ($products->count() > 0)
            <div class="mb-4">
                <p class="text-gray-600">
                    Showing {{ ($products->currentPage() - 1) * $products->perPage() + 1 }} to
                    {{ min($products->currentPage() * $products->perPage(), $products->total()) }} of
                    {{ $products->total() }} {{ Str::plural('product', $products->total()) }}
                </p>
            </div>
        @endif

        <!-- Products Grid -->
        @if ($products->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach ($products as $product)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                        <div class="relative">
                            <a href="{{ route('product.show', $product->slug) }}">
                                @if ($product->primaryImage)
                                    <img src="{{ asset('storage/' . $product->primaryImage->image_path) }}"
                                        alt="{{ $product->name }}" class="w-full h-64 object-cover">
                                @else
                                    <div class="w-full h-64 bg-gray-200 flex items-center justify-center">
                                        <span class="text-gray-400">No image</span>
                                    </div>
                                @endif
                            </a>

                            <!-- Wishlist Button -->
                            @auth
                                <button onclick="toggleWishlist({{ $product->id }})" data-wishlist-btn="{{ $product->id }}"
                                    class="absolute top-2 right-2 bg-white rounded-full p-2 shadow-md hover:bg-gray-50 transition">
                                    <i
                                        class="fas fa-heart text-2xl {{ auth()->check() && auth()->user()->wishlists()->where('product_id', $product->id)->exists() ? 'text-red-500' : 'text-gray-400' }}"></i>
                                </button>
                            @endauth

                            <!-- Badges -->
                            <div class="absolute top-2 left-2 space-y-2">
                                @if ($product->is_featured)
                                    <span class="block bg-yellow-500 text-white text-xs px-2 py-1 rounded">Featured</span>
                                @endif
                                @if ($product->stock < 1)
                                    <span class="block bg-red-500 text-white text-xs px-2 py-1 rounded">Out of Stock</span>
                                @elseif($product->stock < 10)
                                    <span class="block bg-orange-500 text-white text-xs px-2 py-1 rounded">Low Stock</span>
                                @endif
                                @if ($product->old_price && $product->old_price > $product->price)
                                    <span class="block bg-green-500 text-white text-xs px-2 py-1 rounded">
                                        -{{ round((($product->old_price - $product->price) / $product->old_price) * 100) }}%
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="p-4">
                            <!-- Sub-Category Badge -->
                            @if ($product->subCategory)
                                <div class="mb-2">
                                    <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">
                                        {{ $product->subCategory->name }}
                                    </span>
                                </div>
                            @endif

                            <a href="{{ route('product.show', $product->slug) }}" class="block">
                                <h3
                                    class="text-lg font-semibold text-gray-900 hover:text-primary mb-2 h-14 overflow-hidden">
                                    {{ Str::limit($product->name, 50) }}
                                </h3>
                            </a>

                            <p class="text-sm text-gray-600 mb-2">{{ $product->user->store_name ?? $product->user->name }}
                            </p>

                            <!-- Rating -->
                            @if ($product->total_reviews > 0)
                                <div class="flex items-center mb-3">
                                    <div class="flex">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i
                                                class="fas fa-star {{ $i <= round($product->average_rating) ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                        @endfor
                                    </div>
                                    <span class="text-sm text-gray-600 ml-2">({{ $product->total_reviews }})</span>
                                </div>
                            @endif

                            <!-- Price -->
                            <div class="mb-4">
                                <div class="flex items-center">
                                    <span
                                        class="text-2xl font-bold text-gray-900">${{ number_format($product->price, 2) }}</span>
                                    @if ($product->old_price && $product->old_price > $product->price)
                                        <span
                                            class="text-sm text-gray-500 line-through ml-2">${{ number_format($product->old_price, 2) }}</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Add to Cart Button -->
                            @if ($product->stock > 0 && $product->is_active)
                                @auth
                                    <form action="{{ route('cart.store') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit"
                                            class="w-full bg-primary hover:bg-green-600 text-white font-semibold py-2 px-4 rounded-lg transition">
                                            Add to Cart
                                        </button>
                                    </form>
                                @else
                                    <a href="{{ route('login') }}"
                                        class="block w-full bg-primary hover:bg-green-600 text-white font-semibold py-2 px-4 rounded-lg text-center transition">
                                        Login to Add
                                    </a>
                                @endauth
                            @else
                                <button disabled
                                    class="w-full bg-gray-300 text-gray-500 font-semibold py-2 px-4 rounded-lg cursor-not-allowed">
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
                <i class="fas fa-box-open text-6xl text-gray-300 mb-4"></i>
                <h2 class="text-2xl font-semibold text-gray-900 mb-2">No products found</h2>
                <p class="text-gray-600 mb-6">Try adjusting your filters or search terms.</p>
                @if (request()->has('search') ||
                        request()->has('sort') ||
                        request()->has('price_range') ||
                        request()->has('sub_category'))
                    <a href="{{ route('category.show', $category->slug) }}"
                        class="inline-block bg-primary hover:bg-green-600 text-white font-semibold py-3 px-6 rounded-lg transition">
                        Clear Filters
                    </a>
                @else
                    <a href="{{ route('home') }}"
                        class="inline-block bg-primary hover:bg-green-600 text-white font-semibold py-3 px-6 rounded-lg transition">
                        Browse All Products
                    </a>
                @endif
            </div>
        @endif
    </div>

    @push('scripts')
        <script>
            function toggleWishlist(productId) {
                fetch('{{ route('wishlist.toggle') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            product_id: productId
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.in_wishlist) {
                            document.querySelector(`[data-wishlist-btn="${productId}"] i`).classList.add('text-red-500');
                            document.querySelector(`[data-wishlist-btn="${productId}"] i`).classList.remove(
                            'text-gray-400');
                        } else {
                            document.querySelector(`[data-wishlist-btn="${productId}"] i`).classList.remove('text-red-500');
                            document.querySelector(`[data-wishlist-btn="${productId}"] i`).classList.add('text-gray-400');
                        }
                    })
                    .catch(err => console.error('Error:', err));
            }
        </script>
    @endpush
@endsection
