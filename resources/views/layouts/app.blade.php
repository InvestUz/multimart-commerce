<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Multi-Vendor E-Commerce'))</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Styles -->
    {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}
<!-- Tailwind CSS -->
<script src="https://cdn.tailwindcss.com"></script>

<!-- Alpine.js (if you're using it) -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<!-- If you have custom CSS, you can link it separately -->
<link rel="stylesheet" href="{{ asset('css/custom.css') }}">

<!-- If you have custom JavaScript, you can link it separately -->
<script src="{{ asset('js/custom.js') }}"></script>

<!-- Optional: Tailwind CSS Configuration (if you need custom config) -->
<script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    primary: '#3490dc',
                    secondary: '#ffed4e',
                    danger: '#e3342f',
                }
            }
        }
    }
</script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    @stack('styles')
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        <!-- Navigation -->
        <nav class="bg-white border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="flex-shrink-0 flex items-center">
                            <a href="{{ route('home') }}" class="text-xl font-bold text-gray-800">
                                {{ config('app.name', 'E-Commerce') }}
                            </a>
                        </div>

                        <!-- Navigation Links -->
                        <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                            <a href="{{ route('home') }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                                Home
                            </a>
                            <a href="{{ route('brands.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                                Brands
                            </a>
                            <a href="{{ route('about') }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                                About
                            </a>
                            <a href="{{ route('contact') }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                                Contact
                            </a>
                        </div>
                    </div>

                    <!-- Right Side -->
                    <div class="hidden sm:flex sm:items-center sm:ml-6 space-x-4">
                        <!-- Search -->
                        <form action="{{ route('search') }}" method="GET" class="flex">
                            <input type="text" name="q" placeholder="Search products..."
                                   class="rounded-l-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" />
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-r-md hover:bg-indigo-700">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>

                        @auth
                            <!-- Wishlist -->
                            <a href="{{ route('wishlist.index') }}" class="relative text-gray-500 hover:text-gray-700">
                                <i class="fas fa-heart text-xl"></i>
                                <span id="wishlist-count" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                    0
                                </span>
                            </a>

                            <!-- Cart -->
                            <a href="{{ route('cart.index') }}" class="relative text-gray-500 hover:text-gray-700">
                                <i class="fas fa-shopping-cart text-xl"></i>
                                <span id="cart-count" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                    0
                                </span>
                            </a>

                            <!-- Profile Dropdown -->
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700">
                                    <span>{{ Auth::user()->name }}</span>
                                    <i class="fas fa-chevron-down ml-1"></i>
                                </button>

                                <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                                    @if(Auth::user()->isSuperAdmin())
                                        <a href="{{ route('super-admin.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            Admin Dashboard
                                        </a>
                                    @elseif(Auth::user()->isVendor())
                                        <a href="{{ route('vendor.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            Vendor Dashboard
                                        </a>
                                    @endif

                                    <a href="{{ route('orders.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        My Orders
                                    </a>
                                    <a href="{{ route('account.profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        Profile
                                    </a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="text-gray-500 hover:text-gray-700">Login</a>
                            <a href="{{ route('register') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                                Register
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main>
            @if (session('success'))
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
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
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <div>
                        <h3 class="text-lg font-semibold mb-4">About Us</h3>
                        <p class="text-gray-400">Your trusted multi-vendor e-commerce platform.</p>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
                        <ul class="space-y-2">
                            <li><a href="{{ route('about') }}" class="text-gray-400 hover:text-white">About</a></li>
                            <li><a href="{{ route('contact') }}" class="text-gray-400 hover:text-white">Contact</a></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Customer Service</h3>
                        <ul class="space-y-2">
                            <li><a href="#" class="text-gray-400 hover:text-white">Help Center</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white">Returns</a></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Newsletter</h3>
                        <form action="{{ route('newsletter.subscribe') }}" method="POST">
                            @csrf
                            <input type="email" name="email" placeholder="Your email"
                                   class="w-full px-3 py-2 text-gray-900 rounded-md" required />
                            <button type="submit" class="mt-2 w-full px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                                Subscribe
                            </button>
                        </form>
                    </div>
                </div>
                <div class="mt-8 pt-8 border-t border-gray-700 text-center text-gray-400">
                    <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </div>

    @stack('scripts')

    <script>
        // Update cart and wishlist counts
        @auth
        function updateCounts() {
            fetch('{{ route("cart.count") }}')
                .then(res => res.json())
                .then(data => {
                    document.getElementById('cart-count').textContent = data.count;
                });

            fetch('{{ route("wishlist.count") }}')
                .then(res => res.json())
                .then(data => {
                    document.getElementById('wishlist-count').textContent = data.count;
                });
        }

        updateCounts();
        @endauth
    </script>
</body>
</html>
