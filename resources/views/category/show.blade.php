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

    <!-- Category Header + Grid Buttons -->
    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
            <div class="flex-1">
                <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">{{ $category->name }}</h1>
                <p class="text-gray-600">Browse our collection of {{ $products->total() }} products</p>
            </div>

            <!-- Grid Toggle Buttons (1, 2, 3) -->
            @if($products->count() > 0)
                <div x-data x-init="
                    const saved = localStorage.getItem('productGridCols');
                    if (saved && [1,2,3].includes(parseInt(saved))) {
                        $store.grid.cols = parseInt(saved);
                    }
                ">
                    <div class="inline-flex rounded-lg shadow-sm" role="group">
                        <template x-for="col in [1,2,3]" :key="col">
                            <button 
                                @click="$store.grid.setCols(col)"
                                :class="{
                                    'bg-primary text-white': $store.grid.cols === col,
                                    'bg-white text-gray-700 border-gray-300': $store.grid.cols !== col
                                }"
                                class="px-3 py-2 text-xs font-medium border hover:bg-gray-100 focus:z-10 focus:ring-2 focus:ring-primary transition"
                                :class="{
                                    'rounded-l-lg': col === 1,
                                    'rounded-r-lg': col === 3,
                                    'border-l-0': col !== 1
                                }"
                            >
                                <i :class="col === 1 ? 'fas fa-th-large' : 'fas fa-th'"></i>
                                <span x-text="col"></span>
                            </button>
                        </template>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Sub-Categories -->
    @if($subCategories->count() > 0)
        <div class="mb-8">
            <h2 class="text-xl font-semibold mb-4">Filter by Sub-Category</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3">
                <a href="{{ route('category.show', $category->slug) }}" 
                   class="p-3 border-2 rounded-lg text-center transition {{ !request('sub_category') ? 'border-primary bg-blue-50' : 'border-gray-200 hover:border-gray-300' }}">
                    <p class="font-semibold">All</p>
                    <p class="text-xs text-gray-600">{{ $products->total() }} products</p>
                </a>
                @foreach($subCategories as $subCategory)
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
            @if(request('sub_category'))
                <input type="hidden" name="sub_category" value="{{ request('sub_category') }}">
            @endif

            <div class="flex-1 min-w-[180px]">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Search..." 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>

            <div>
                <select name="sort" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="">Sort By</option>
                    <option value="featured" {{ request('sort') == 'featured' ? 'selected' : '' }}>Featured</option>
                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest</option>
                    <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Low to High</option>
                    <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>High to Low</option>
                    <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Rating</option>
                    <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Popular</option>
                </select>
            </div>

            <div>
                <select name="price_range" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="">All Prices</option>
                    <option value="0-50" {{ request('price_range') == '0-50' ? 'selected' : '' }}>Under $50</option>
                    <option value="50-100" {{ request('price_range') == '50-100' ? 'selected' : '' }}>$50 - $100</option>
                    <option value="100-200" {{ request('price_range') == '100-200' ? 'selected' : '' }}>$100+</option>
                </select>
            </div>

            <button type="submit" class="px-5 py-2 bg-primary text-white rounded-lg text-sm hover:bg-green-600">
                Apply
            </button>

            @if(request()->hasAny(['search', 'sort', 'price_range', 'sub_category']))
                <a href="{{ route('category.show', $category->slug) }}" class="px-5 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm hover:bg-gray-300">
                    Clear
                </a>
            @endif
        </form>
    </div>

    <!-- Products Count -->
    @if($products->count() > 0)
        <div class="mb-4 text-sm text-gray-600">
            Showing {{ ($products->currentPage() - 1) * $products->perPage() + 1 }}–{{ min($products->currentPage() * $products->perPage(), $products->total()) }} of {{ $products->total() }} products
        </div>
    @endif

    <!-- Products Grid (Mobil Auto-Size) -->
    <div x-data x-init="
        const saved = localStorage.getItem('productGridCols');
        if (saved && [1,2,3].includes(parseInt(saved))) {
            $store.grid.cols = parseInt(saved);
        } else {
            $store.grid.cols = 2; // Default: 2x2 mobil uchun
        }
    ">
        @if($products->count() > 0)
            <div 
                :class="{
                    'grid grid-cols-1 gap-4 sm:gap-6': $store.grid.cols === 1,
                    'grid grid-cols-2 gap-4 sm:gap-6': $store.grid.cols === 2,
                    'grid grid-cols-2 sm:grid-cols-3 gap-4 sm:gap-6': $store.grid.cols === 3
                }"
            >
                @foreach($products as $product)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                        <div class="relative">
                            <a href="{{ route('product.show', $product->slug) }}">
                                @if($product->primaryImage)
                                    <img src="{{ asset('storage/' . $product->primaryImage->image_path) }}" 
                                         alt="{{ $product->name }}" 
                                         class="w-full h-48 sm:h-56 object-cover">
                                @else
                                    <div class="w-full h-48 sm:h-56 bg-gray-200 flex items-center justify-center">
                                        <span class="text-gray-400 text-xs">No image</span>
                                    </div>
                                @endif
                            </a>

                            @auth
                                <button onclick="toggleWishlist({{ $product->id }})" 
                                        data-wishlist-btn="{{ $product->id }}"
                                        class="absolute top-2 right-2 bg-white rounded-full p-1.5 sm:p-2 shadow-md hover:bg-gray-50 transition text-sm sm:text-base">
                                    <i class="fas fa-heart text-lg sm:text-2xl {{ auth()->user()->wishlists()->where('product_id', $product->id)->exists() ? 'text-red-500' : 'text-gray-400' }}"></i>
                                </button>
                            @endauth

                            <div class="absolute top-2 left-2 space-y-1 text-xs">
                                @if($product->is_featured)
                                    <span class="block bg-yellow-500 text-white px-1.5 py-0.5 rounded">Featured</span>
                                @endif
                                @if($product->stock < 1)
                                    <span class="block bg-red-500 text-white px-1.5 py-0.5 rounded">Out</span>
                                @elseif($product->stock < 10)
                                    <span class="block bg-orange-500 text-white px-1.5 py-0.5 rounded">Low</span>
                                @endif
                                @if($product->old_price && $product->old_price > $product->price)
                                    <span class="block bg-green-500 text-white px-1.5 py-0.5 rounded">
                                        -{{ round((($product->old_price - $product->price) / $product->old_price) * 100) }}%
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="p-3 sm:p-4">
                            @if($product->subCategory)
                                <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-0.5 rounded mb-1">
                                    {{ $product->subCategory->name }}
                                </span>
                            @endif

                            <a href="{{ route('product.show', $product->slug) }}" class="block">
                                <h3 class="text-sm sm:text-lg font-semibold text-gray-900 hover:text-primary line-clamp-2 mb-1">
                                    {{ Str::limit($product->name, 40) }}
                                </h3>
                            </a>

                            <p class="text-xs sm:text-sm text-gray-600 mb-1">{{ $product->user->store_name ?? $product->user->name }}</p>

                            @if($product->total_reviews > 0)
                                <div class="flex items-center mb-2 text-xs sm:text-sm">
                                    <div class="flex text-yellow-400">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star {{ $i <= round($product->average_rating) ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                        @endfor
                                    </div>
                                    <span class="text-gray-600 ml-1">({{ $product->total_reviews }})</span>
                                </div>
                            @endif

                            <div class="mb-2">
                                <span class="text-lg sm:text-2xl font-bold text-gray-900">${{ number_format($product->price, 2) }}</span>
                                @if($product->old_price && $product->old_price > $product->price)
                                    <span class="text-xs sm:text-sm text-gray-500 line-through ml-1">${{ number_format($product->old_price, 2) }}</span>
                                @endif
                            </div>

                            @if($product->stock > 0 && $product->is_active)
                                @auth
                                    <form action="{{ route('cart.store') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="w-full bg-primary hover:bg-green-600 text-white text-xs sm:text-sm font-semibold py-1.5 sm:py-2 px-3 sm:px-4 rounded-lg transition">
                                            Add to Cart
                                        </button>
                                    </form>
                                @else
                                    <a href="{{ route('login') }}" class="block w-full bg-primary hover:bg-green-600 text-white text-xs sm:text-sm font-semibold py-1.5 sm:py-2 px-3 sm:px-4 rounded-lg text-center transition">
                                        Login to Add
                                    </a>
                                @endauth
                            @else
                                <button disabled class="w-full bg-gray-300 text-gray-500 text-xs sm:text-sm font-semibold py-1.5 sm:py-2 px-3 sm:px-4 rounded-lg cursor-not-allowed">
                                    Out of Stock
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6 sm:mt-8">
                {{ $products->appends(request()->query())->links() }}
            </div>
        @else
            <div class="bg-white rounded-lg shadow-md p-8 sm:p-12 text-center">
                <i class="fas fa-box-open text-5xl sm:text-6xl text-gray-300 mb-4"></i>
                <h2 class="text-xl sm:text-2xl font-semibold text-gray-900 mb-2">No products found</h2>
                <p class="text-gray-600 mb-4 text-sm sm:text-base">Try adjusting your filters.</p>
                @if(request()->hasAny(['search', 'sort', 'price_range', 'sub_category']))
                    <a href="{{ route('category.show', $category->slug) }}" class="inline-block bg-primary hover:bg-green-600 text-white font-semibold py-2 sm:py-3 px-5 sm:px-6 rounded-lg text-sm sm:text-base transition">
                        Clear Filters
                    </a>
                @else
                    <a href="{{ route('home') }}" class="inline-block bg-primary hover:bg-green-600 text-white font-semibold py-2 sm:py-3 px-5 sm:px-6 rounded-lg text-sm sm:text-base transition">
                        Browse All
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.store('grid', {
            cols: 2, // Mobil uchun default: 2x2
            setCols(value) {
                if ([1,2,3].includes(value)) {
                    this.cols = value;
                    localStorage.setItem('productGridCols', value);
                }
            }
        });
    });

    function toggleWishlist(productId) {
        fetch('{{ route("wishlist.toggle") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ product_id: productId })
        })
        .then(res => res.json())
        .then(data => {
            const icon = document.querySelector(`[data-wishlist-btn="${productId}"] i`);
            if (data.in_wishlist) {
                icon.classList.remove('text-gray-400');
                icon.classList.add('text-red-500');
            } else {
                icon.classList.remove('text-red-500');
                icon.classList.add('text-gray-400');
            }
        })
        .catch(err => console.error('Wishlist error:', err));
    }
</script>
@endpush
@endsection