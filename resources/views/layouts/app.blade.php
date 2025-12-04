<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Onebazar'))</title>

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
                    gold: {
                        50: '#FFFBEB',
                        100: '#FEF3C7',
                        200: '#FDE68A',
                        300: '#FCD34D',
                        400: '#FBBF24',
                        500: '#F59E0B',
                        600: '#D97706',
                        700: '#B45309',
                        800: '#92400E',
                        900: '#78350F',
                    },
                }
            }
        }
    }
</script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    @stack('styles')
</head>
<body class="font-sans antialiased" x-data="{ mobileMenuOpen: false }">
    <div class="min-h-screen bg-gray-100">
        <!-- Navigation -->
        <nav class="bg-white border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="flex-shrink-0 flex items-center">
                            <a href="{{ route('home') }}" class="text-xl font-bold text-gray-800">
                                {{ config('app.name', 'Onebazar') }}
                            </a>
                        </div>

                        <!-- Navigation Links -->
                        <div class="hidden space-y-2 sm:-my-px sm:ml-10 sm:flex sm:flex-col md:flex-row md:space-y-0 md:space-x-8">
                            <a href="{{ route('home') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('home') ? 'border-gold-500 text-gold-600' : 'border-transparent text-gray-500' }} hover:text-gold-600 hover:border-gold-300 text-sm font-medium">
                                {{ __('messages.home') }}
                            </a>
                            <a href="{{ route('brands.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('brands.*') ? 'border-gold-500 text-gold-600' : 'border-transparent text-gray-500' }} hover:text-gold-600 hover:border-gold-300 text-sm font-medium">
                                {{ __('messages.brands') }}
                            </a>
                            <a href="{{ route('about') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('about') ? 'border-gold-500 text-gold-600' : 'border-transparent text-gray-500' }} hover:text-gold-600 hover:border-gold-300 text-sm font-medium">
                                {{ __('messages.about') }}
                            </a>
                            <a href="{{ route('contact') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('contact') ? 'border-gold-500 text-gold-600' : 'border-transparent text-gray-500' }} hover:text-gold-600 hover:border-gold-300 text-sm font-medium">
                                {{ __('messages.contact') }}
                            </a>
                        </div>
                    </div>

                    <!-- Mobile menu button -->
                    <div class="flex items-center sm:hidden">
                        <button @click="mobileMenuOpen = !mobileMenuOpen" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-gold-500">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                    </div>

                    <!-- Right Side -->
                    <div class="hidden sm:flex sm:items-center sm:ml-6 space-x-4">
                        <!-- Language Switcher -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center text-sm font-medium text-gray-500 hover:text-gold-600 p-2 rounded-full hover:bg-gray-100">
                                <span class="capitalize">{{ app()->getLocale() }}</span>
                                <i class="fas fa-chevron-down ml-1 text-xs"></i>
                            </button>

                            <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-32 bg-white rounded-md shadow-lg py-1 z-50">
                                <a href="{{ route('lang.switch', ['locale' => 'en']) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ app()->getLocale() === 'en' ? 'bg-gray-100' : '' }}">
                                    English
                                </a>
                                <a href="{{ route('lang.switch', ['locale' => 'ru']) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ app()->getLocale() === 'ru' ? 'bg-gray-100' : '' }}">
                                    Русский
                                </a>
                                <a href="{{ route('lang.switch', ['locale' => 'uz']) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ app()->getLocale() === 'uz' ? 'bg-gray-100' : '' }}">
                                    O'zbek
                                </a>
                            </div>
                        </div>

                        <!-- Search -->
                        <form action="{{ route('search') }}" method="GET" class="flex items-center">
                            <div class="relative">
                                <input type="text" name="q" placeholder="{{ __('messages.search_placeholder') }}"
                                       class="w-64 rounded-full border-gray-300 focus:border-gold-500 focus:ring-gold-500 pl-4 pr-10 py-2 text-sm" 
                                       value="{{ request()->get('q') }}" />
                                <button type="submit" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gold-600">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </form>

                        @auth
                            <!-- Wishlist -->
                            <a href="{{ route('wishlist.index') }}" class="relative text-gray-500 hover:text-gold-600 p-2 rounded-full hover:bg-gray-100">
                                <i class="fas fa-heart text-xl"></i>
                                <span id="wishlist-count" class="absolute -top-1 -right-1 bg-gold-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center text-[10px]">
                                    0
                                </span>
                            </a>

                            <!-- Cart -->
                            <a href="{{ route('cart.index') }}" class="relative text-gray-500 hover:text-gold-600 p-2 rounded-full hover:bg-gray-100">
                                <i class="fas fa-shopping-cart text-xl"></i>
                                <span id="cart-count" class="absolute -top-1 -right-1 bg-gold-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center text-[10px]">
                                    0
                                </span>
                            </a>

                            <!-- Profile Dropdown -->
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" class="flex items-center text-sm font-medium text-gray-500 hover:text-gold-600 p-2 rounded-full hover:bg-gray-100">
                                    <span>{{ Auth::user()->name }}</span>
                                    <i class="fas fa-chevron-down ml-1 text-xs"></i>
                                </button>

                                <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                                    @if(Auth::user()->role === 'super_admin')
                                        <a href="{{ route('super-admin.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            {{ __('messages.admin_dashboard') }}
                                        </a>
                                    @elseif(Auth::user()->role === 'vendor')
                                        <a href="{{ route('vendor.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            {{ __('messages.vendor_dashboard') }}
                                        </a>
                                    @endif

                                    <a href="{{ route('orders.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        {{ __('messages.my_orders') }}
                                    </a>
                                    <a href="{{ route('account.profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        {{ __('messages.profile') }}
                                    </a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            {{ __('messages.logout') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="px-4 py-2 bg-blue-600 text-white rounded-full hover:bg-blue-700 text-sm font-medium transition duration-300 ease-in-out transform hover:scale-105">{{ __('messages.login') }}</a>
                            <a href="{{ route('register') }}" class="px-4 py-2 bg-gradient-to-r from-gold-500 to-gold-600 text-white rounded-full hover:from-gold-600 hover:to-gold-700 text-sm font-medium transition duration-300 ease-in-out transform hover:scale-105 shadow-lg">
                                {{ __('messages.register') }}
                            </a>
                        @endauth
                    </div>
                </div>
            </div>

            <!-- Mobile menu -->
            <div x-show="mobileMenuOpen" class="sm:hidden" x-cloak>
                <div class="pt-2 pb-3 space-y-1">
                    <a href="{{ route('home') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('home') ? 'border-gold-500 text-gold-600 bg-gold-50' : 'border-transparent text-gray-600' }} hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 text-base font-medium">{{ __('messages.home') }}</a>
                    <a href="{{ route('brands.index') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('brands.*') ? 'border-gold-500 text-gold-600 bg-gold-50' : 'border-transparent text-gray-600' }} hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 text-base font-medium">{{ __('messages.brands') }}</a>
                    <a href="{{ route('about') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('about') ? 'border-gold-500 text-gold-600 bg-gold-50' : 'border-transparent text-gray-600' }} hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 text-base font-medium">{{ __('messages.about') }}</a>
                    <a href="{{ route('contact') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('contact') ? 'border-gold-500 text-gold-600 bg-gold-50' : 'border-transparent text-gray-600' }} hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 text-base font-medium">{{ __('messages.contact') }}</a>
                    
                    <!-- Mobile Search -->
                    <div class="px-4 py-2">
                        <form action="{{ route('search') }}" method="GET" class="flex items-center">
                            <div class="relative w-full">
                                <input type="text" name="q" placeholder="{{ __('messages.search_placeholder') }}"
                                       class="w-full rounded-full border-gray-300 focus:border-gold-500 focus:ring-gold-500 pl-4 pr-10 py-2 text-sm" 
                                       value="{{ request()->get('q') }}" />
                                <button type="submit" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gold-600">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    @auth
                        <div class="border-t border-gray-200 pt-4 pb-3">
                            <div class="flex items-center px-4">
                                <div class="flex-shrink-0">
                                    <span class="text-gray-800 font-medium">{{ Auth::user()->name }}</span>
                                </div>
                            </div>
                            <div class="mt-3 space-y-1">
                                @if(Auth::user()->role === 'super_admin')
                                    <a href="{{ route('super-admin.dashboard') }}" class="block px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100">{{ __('messages.admin_dashboard') }}</a>
                                @elseif(Auth::user()->role === 'vendor')
                                    <a href="{{ route('vendor.dashboard') }}" class="block px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100">{{ __('messages.vendor_dashboard') }}</a>
                                @endif
                                <a href="{{ route('orders.index') }}" class="block px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100">{{ __('messages.my_orders') }}</a>
                                <a href="{{ route('account.profile') }}" class="block px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100">{{ __('messages.profile') }}</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100">{{ __('messages.logout') }}</button>
                                </form>
                            </div>
                        </div>
                    @else
                        <div class="border-t border-gray-200 pt-4 pb-3">
                            <div class="space-y-1 px-4">
                                <a href="{{ route('login') }}" class="block px-4 py-2 text-base font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md text-center transition duration-300 ease-in-out">{{ __('messages.login') }}</a>
                                <a href="{{ route('register') }}" class="block px-4 py-2 text-base font-medium text-white bg-gradient-to-r from-gold-500 to-gold-600 hover:from-gold-600 hover:to-gold-700 rounded-md text-center transition duration-300 ease-in-out shadow-md">{{ __('messages.register') }}</a>
                            </div>
                        </div>
                    @endauth
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main class="mt-6">
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
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                    <div>
                        <h3 class="text-lg font-semibold mb-4">{{ __('messages.about_us') }}</h3>
                        <p class="text-gray-400">Your trusted multi-vendor e-commerce platform.</p>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold mb-4">{{ __('messages.quick_links') }}</h3>
                        <ul class="space-y-2">
                            <li><a href="{{ route('about') }}" class="text-gray-400 hover:text-white">{{ __('messages.about') }}</a></li>
                            <li><a href="{{ route('contact') }}" class="text-gray-400 hover:text-white">{{ __('messages.contact') }}</a></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold mb-4">{{ __('messages.customer_service') }}</h3>
                        <ul class="space-y-2">
                            <li><a href="#" class="text-gray-400 hover:text-white">Help Center</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white">Returns</a></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold mb-4">{{ __('messages.newsletter') }}</h3>
                        <form action="{{ route('newsletter.subscribe') }}" method="POST">
                            @csrf
                            <input type="email" name="email" placeholder="Your email"
                                   class="w-full px-3 py-2 text-gray-900 rounded-md" required />
                            <button type="submit" class="mt-2 w-full px-4 py-2 bg-gold-600 text-white rounded-md hover:bg-gold-700">
                                {{ __('messages.subscribe') }}
                            </button>
                        </form>
                    </div>
                </div>
                <div class="mt-8 pt-8 border-t border-gray-700 text-center text-gray-400">
                    <p>&copy; {{ date('Y') }} {{ config('app.name') }}. {{ __('messages.all_rights_reserved') }}</p>
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