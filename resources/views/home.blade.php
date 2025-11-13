@extends('layouts.app')

@section('title', 'Home - ' . config('app.name'))

@section('content')
<div class="bg-white">
    <!-- Hero Banners -->
    @if($banners->isNotEmpty())
    <div class="relative">
        <div id="banner-carousel" class="relative h-96 overflow-hidden">
            @foreach($banners as $index => $banner)
            <div class="banner-slide {{ $index === 0 ? 'active' : '' }} absolute inset-0 transition-opacity duration-700">
                <img src="{{ Storage::url($banner->image) }}" alt="{{ $banner->title }}" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center">
                    <div class="text-center text-white">
                        <h2 class="text-4xl font-bold mb-4">{{ $banner->title }}</h2>
                        @if($banner->link)
                        <a href="{{ $banner->link }}" class="px-6 py-3 bg-white text-gray-900 rounded-lg font-semibold hover:bg-gray-100">
                            Shop Now
                        </a>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Banner Navigation -->
        @if($banners->count() > 1)
        <button onclick="prevSlide()" class="absolute top-1/2 left-4 transform -translate-y-1/2 bg-white bg-opacity-50 hover:bg-opacity-75 rounded-full p-2">
            <i class="fas fa-chevron-left text-gray-800"></i>
        </button>
        <button onclick="nextSlide()" class="absolute top-1/2 right-4 transform -translate-y-1/2 bg-white bg-opacity-50 hover:bg-opacity-75 rounded-full p-2">
            <i class="fas fa-chevron-right text-gray-800"></i>
        </button>
        @endif
    </div>
    @endif

    <!-- Categories -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h2 class="text-3xl font-bold text-gray-900 mb-8">Shop by Category</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-4">
            @foreach($categories as $category)
            <a href="{{ route('category.show', $category->slug) }}" class="text-center group">
                <div class="bg-gray-100 rounded-lg p-6 group-hover:bg-gray-200 transition">
                    @if($category->image)
                    <img src="{{ Storage::url($category->image) }}" alt="{{ $category->name }}" class="w-16 h-16 mx-auto mb-2 object-cover rounded">
                    @else
                    <i class="{{ $category->icon ?? 'fas fa-box' }} text-4xl text-gray-600 mb-2"></i>
                    @endif
                </div>
                <p class="mt-2 text-sm font-medium text-gray-900">{{ $category->name }}</p>
                <p class="text-xs text-gray-500">{{ $category->products_count }} items</p>
            </a>
            @endforeach
        </div>
    </div>

    <!-- Flash Sale -->
    @if($flashSale)
    <div class="bg-red-50 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-3xl font-bold text-red-600">
                    <i class="fas fa-bolt"></i> Flash Sale
                </h2>
                <div class="text-lg font-semibold text-gray-900">
                    Ends in: <span id="countdown"></span>
                </div>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($flashSale->products->take(8) as $product)
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition">
                    <a href="{{ route('product.show', $product->slug) }}">
                        @if($product->images->first())
                        <img src="{{ Storage::url($product->images->first()->image_path) }}"
                             alt="{{ $product->name }}"
                             class="w-full h-48 object-cover">
                        @endif
                        <div class="p-4">
                            <h3 class="text-sm font-medium text-gray-900 mb-2 line-clamp-2">{{ $product->name }}</h3>
                            <div class="flex items-center space-x-2">
                                <span class="text-lg font-bold text-red-600">${{ number_format($product->price * 0.8, 2) }}</span>
                                <span class="text-sm text-gray-500 line-through">${{ number_format($product->price, 2) }}</span>
                                <span class="text-sm text-red-600 font-semibold">-20%</span>
                            </div>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <script>
        // Countdown timer
        const endDate = new Date('{{ $flashSale->end_date }}').getTime();

        function updateCountdown() {
            const now = new Date().getTime();
            const distance = endDate - now;

            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            document.getElementById('countdown').innerHTML =
                `${days}d ${hours}h ${minutes}m ${seconds}s`;

            if (distance < 0) {
                document.getElementById('countdown').innerHTML = 'EXPIRED';
            }
        }

        updateCountdown();
        setInterval(updateCountdown, 1000);
    </script>
    @endif

    <!-- Featured Products -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h2 class="text-3xl font-bold text-gray-900 mb-8">Featured Products</h2>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($featuredProducts as $product)
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition">
                <a href="{{ route('product.show', $product->slug) }}">
                    @if($product->images->first())
                    <img src="{{ Storage::url($product->images->first()->image_path) }}"
                         alt="{{ $product->name }}"
                         class="w-full h-48 object-cover">
                    @endif
                    <div class="p-4">
                        <p class="text-xs text-gray-500 mb-1">{{ $product->vendor->shop_name }}</p>
                        <h3 class="text-sm font-medium text-gray-900 mb-2 line-clamp-2">{{ $product->name }}</h3>
                        <div class="flex items-center mb-2">
                            <div class="flex text-yellow-400">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star{{ $i <= ($product->reviews_avg_rating ?? 0) ? '' : '-o' }} text-xs"></i>
                                @endfor
                            </div>
                            <span class="text-xs text-gray-500 ml-1">({{ $product->reviews_count }})</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-lg font-bold text-gray-900">${{ number_format($product->price, 2) }}</span>
                            @auth
                            <button onclick="addToCart({{ $product->id }})" class="text-indigo-600 hover:text-indigo-800">
                                <i class="fas fa-shopping-cart"></i>
                            </button>
                            @endauth
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>

    <!-- New Arrivals -->
    <div class="bg-gray-50 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-gray-900 mb-8">New Arrivals</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($newArrivals as $product)
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition">
                    <a href="{{ route('product.show', $product->slug) }}">
                        @if($product->images->first())
                        <img src="{{ Storage::url($product->images->first()->image_path) }}"
                             alt="{{ $product->name }}"
                             class="w-full h-48 object-cover">
                        @endif
                        <div class="p-4">
                            <span class="inline-block px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full mb-2">New</span>
                            <p class="text-xs text-gray-500 mb-1">{{ $product->vendor->shop_name }}</p>
                            <h3 class="text-sm font-medium text-gray-900 mb-2 line-clamp-2">{{ $product->name }}</h3>
                            <span class="text-lg font-bold text-gray-900">${{ number_format($product->price, 2) }}</span>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Brands -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h2 class="text-3xl font-bold text-gray-900 mb-8">Popular Brands</h2>
        <div class="grid grid-cols-3 md:grid-cols-6 lg:grid-cols-12 gap-4">
            @foreach($brands as $brand)
            <a href="{{ route('brand.show', $brand->slug) }}" class="bg-white p-4 rounded-lg shadow hover:shadow-md transition text-center">
                @if($brand->logo)
                <img src="{{ Storage::url($brand->logo) }}" alt="{{ $brand->name }}" class="h-12 mx-auto object-contain">
                @else
                <p class="text-sm font-medium text-gray-900">{{ $brand->name }}</p>
                @endif
            </a>
            @endforeach
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Banner carousel
    let currentSlide = 0;
    const slides = document.querySelectorAll('.banner-slide');

    function showSlide(n) {
        slides[currentSlide].classList.remove('active', 'opacity-100');
        slides[currentSlide].classList.add('opacity-0');
        currentSlide = (n + slides.length) % slides.length;
        slides[currentSlide].classList.remove('opacity-0');
        slides[currentSlide].classList.add('active', 'opacity-100');
    }

    function nextSlide() {
        showSlide(currentSlide + 1);
    }

    function prevSlide() {
        showSlide(currentSlide - 1);
    }

    // Auto-advance slides
    setInterval(nextSlide, 5000);

    // Add to cart function
    @auth
    function addToCart(productId) {
        fetch('{{ route("cart.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                product_id: productId,
                quantity: 1
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.getElementById('cart-count').textContent = data.cart_count;
                alert('Product added to cart!');
            }
        });
    }
    @endauth
</script>
@endpush
@endsection
