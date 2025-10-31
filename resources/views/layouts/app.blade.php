<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} - @yield('title', 'Multi-Vendor E-Commerce')</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary': '#4CAF50',
                        'pastel-blue': '#E1F5FE',
                        'pastel-pink': '#FCE4EC',
                        'pastel-beige': '#F5F5DC',
                        'pastel-green': '#E8F5E8',
                        'soft-green': '#A5D6A7',
                    }
                }
            }
        }
    </script>
    @stack('styles')
</head>

<body class="bg-gray-50">
    <!-- Header -->
<header class="bg-white shadow-sm sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-wrap justify-between items-center h-16 gap-2">
            <!-- Logo -->
            <div class="flex items-center flex-shrink-0">
                <a href="{{ route('dashboard') }}" class="text-xl sm:text-2xl font-bold text-primary">
                    <i class="fas fa-shopping-bag"></i> OneBazar
                </a>
            </div>

            <!-- Search (mobil uchun kichrayadi, yashirmaymiz) -->
            <div class="flex-1 max-w-md mx-2 sm:mx-8">
                <form action="{{ route('search') }}" method="GET" class="relative">
                    <input type="text" name="query" placeholder="Search..."
                        class="w-full px-3 sm:px-4 py-1.5 sm:py-2 rounded-full border text-sm focus:outline-none focus:ring-2 focus:ring-primary"
                        value="{{ request('query') }}">
                    <button type="submit"
                        class="absolute right-1 sm:right-2 top-1/2 transform -translate-y-1/2 bg-primary text-white px-2 sm:px-4 py-1 rounded-full text-xs sm:text-sm">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>

            <!-- Navigation -->
            <nav class="flex items-center space-x-2 sm:space-x-4 text-sm">
                @auth
                    <a href="{{ route('wishlist.index') }}" class="text-gray-600 hover:text-primary relative">
                        <i class="fas fa-heart text-lg sm:text-xl"></i>
                        <span class="absolute -top-1.5 -right-1.5 bg-red-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center text-[10px]"
                            id="wishlist-count">0</span>
                    </a>
                    <a href="{{ route('cart.index') }}" class="text-gray-600 hover:text-primary relative">
                        <i class="fas fa-shopping-cart text-lg sm:text-xl"></i>
                    <span class="absolute -top-1.5 -right-1.5 bg-primary text-white text-xs rounded-full w-4 h-4 flex items-center justify-center text-[10px] font-bold"
                            id="cart-count">0</span>
                    </a>
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open"
                            class="flex items-center space-x-1 text-gray-700 hover:text-primary focus:outline-none text-xs sm:text-sm">
                            <i class="fas fa-user-circle text-lg sm:text-2xl"></i>
                            <span class="hidden sm:inline">{{ auth()->user()->name }}</span>
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>

                        <div x-show="open" @click.away="open = false" x-transition
                            class="absolute right-0 mt-2 w-40 sm:w-48 bg-white rounded-lg shadow-lg py-2 z-50 text-xs sm:text-sm">
                            @if (auth()->user()->isSuperAdmin())
                                <a href="{{ route('super-admin.dashboard') }}"
                                    class="block px-3 sm:px-4 py-2 hover:bg-gray-100">
                                    <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                                </a>
                            @elseif(auth()->user()->isVendor())
                                <a href="{{ route('vendor.dashboard') }}" class="block px-3 sm:px-4 py-2 hover:bg-gray-100">
                                    <i class="fas fa-store mr-2"></i> My Store
                                </a>
                            @endif
                            <a href="{{ route('orders.index') }}" class="block px-3 sm:px-4 py-2 hover:bg-gray-100">
                                <i class="fas fa-box mr-2"></i> My Orders
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="w-full text-left px-3 sm:px-4 py-2 hover:bg-gray-100 text-red-600">
                                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="text-gray-600 hover:text-primary text-xs sm:text-sm">
                        <i class="fas fa-sign-in-alt mr-1"></i> Login
                    </a>
                    <a href="{{ route('register') }}"
                        class="bg-primary text-white px-2 sm:px-4 py-1 sm:py-2 rounded-lg hover:bg-green-600 text-xs sm:text-sm">
                        Register
                    </a>
                @endauth
            </nav>
        </div>
    </div>
</header>

    <!-- Main Content -->
    <main class="min-h-screen">
        @if (session('success'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative"
                    role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white mt-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 text-sm">
            <div>
                <h3 class="text-lg font-bold mb-3">MultiMart</h3>
                <p class="text-gray-400 text-xs">Your one-stop marketplace for everything you need.</p>
            </div>
            <div>
                <h4 class="font-semibold mb-3">Quick Links</h4>
                <ul class="space-y-1 text-gray-400 text-xs">
                    <li><a href="{{ route('dashboard') }}" class="hover:text-white">Home</a></li>
                    <li><a href="{{ route('search') }}" class="hover:text-white">Shop</a></li>
                    <li><a href="#" class="hover:text-white">About Us</a></li>
                    <li><a href="#" class="hover:text-white">Contact</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold mb-3">Customer Service</h4>
                <ul class="space-y-1 text-gray-400 text-xs">
                    <li><a href="#" class="hover:text-white">Help Center</a></li>
                    <li><a href="#" class="hover:text-white">Track Order</a></li>
                    <li><a href="#" class="hover:text-white">Returns</a></li>
                    <li><a href="#" class="hover:text-white">Shipping Info</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold mb-3">Contact Us</h4>
                <ul class="space-y-1 text-gray-400 text-xs">
                    <li><i class="fas fa-phone mr-2"></i> +998 90 123 45 67</li>
                    <li><i class="fas fa-envelope mr-2"></i> info@multimart.com</li>
                    <li><i class="fas fa-map-marker-alt mr-2"></i> Tashkent, Uzbekistan</li>
                </ul>
            </div>
        </div>
        <div class="border-t border-gray-700 mt-6 pt-6 text-center text-gray-400 text-xs">
            <p>&copy; 2025 MultiMart. All rights reserved.</p>
        </div>
    </div>
</footer>

    <script>
        // Update cart and wishlist counts
        function updateCounts() {
            @auth
            // Update cart count
            fetch('{{ route('cart.count') }}')
                .then(res => res.json())
                .then(data => {
                    const cartCountElement = document.getElementById('cart-count');
                    if (cartCountElement) {
                        cartCountElement.textContent = data.count || '0';
                    }
                })
                .catch(err => console.error('Error updating cart count:', err));

            // Update wishlist count
            fetch('{{ route('wishlist.count') }}')
                .then(res => res.json())
                .then(data => {
                    const wishlistCountElement = document.getElementById('wishlist-count');
                    if (wishlistCountElement) {
                        wishlistCountElement.textContent = data.count || '0';
                    }
                })
                .catch(err => console.error('Error updating wishlist count:', err));
        @endauth
        }

        // Update on page load
        document.addEventListener('DOMContentLoaded', updateCounts);
    </script>

    
    @stack('scripts')
</body>

</html>
