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

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <!-- Vite Assets (Tailwind CSS) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

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
                            <a href="{{ route('home') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('home') ? 'border-primary text-primary' : 'border-transparent text-gray-500' }} hover:text-primary hover:border-primary text-sm font-medium transition-colors">
                                {{ __('messages.home') }}
                            </a>
                            <a href="{{ route('brands.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('brands.*') ? 'border-primary text-primary' : 'border-transparent text-gray-500' }} hover:text-primary hover:border-primary text-sm font-medium transition-colors">
                                {{ __('messages.brands') }}
                            </a>
                            <a href="{{ route('about') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('about') ? 'border-primary text-primary' : 'border-transparent text-gray-500' }} hover:text-primary hover:border-primary text-sm font-medium transition-colors">
                                {{ __('messages.about') }}
                            </a>
                            <a href="{{ route('contact') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('contact') ? 'border-primary text-primary' : 'border-transparent text-gray-500' }} hover:text-primary hover:border-primary text-sm font-medium transition-colors">
                                {{ __('messages.contact') }}
                            </a>
                        </div>
                    </div>

                    <!-- Mobile menu button -->
                    <div class="flex items-center sm:hidden">
                        <button @click="mobileMenuOpen = !mobileMenuOpen" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-primary hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-primary transition-colors">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                    </div>

                    <!-- Right Side -->
                    <div class="hidden sm:flex sm:items-center sm:ml-6 space-x-4">
                        <!-- Language Switcher -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center text-sm font-medium text-gray-500 hover:text-primary p-2 rounded-full hover:bg-gray-100 transition-colors">
                                <span class="capitalize">{{ app()->getLocale() }}</span>
                                <i class="fas fa-chevron-down ml-1 text-xs"></i>
                            </button>

                            <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-32 bg-white rounded-md shadow-lg py-1 z-50" x-cloak>
                                <a href="{{ route('lang.switch', ['locale' => 'en']) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-primary {{ app()->getLocale() === 'en' ? 'bg-blue-50 text-primary' : '' }}">
                                    English
                                </a>
                                <a href="{{ route('lang.switch', ['locale' => 'ru']) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-primary {{ app()->getLocale() === 'ru' ? 'bg-blue-50 text-primary' : '' }}">
                                    Русский
                                </a>
                                <a href="{{ route('lang.switch', ['locale' => 'uz']) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-primary {{ app()->getLocale() === 'uz' ? 'bg-blue-50 text-primary' : '' }}">
                                    O'zbek
                                </a>
                            </div>
                        </div>

                        <!-- Search -->
                        <form action="{{ route('search') }}" method="GET" class="flex items-center">
                            <div class="relative">
                                <input type="text" name="q" placeholder="{{ __('messages.search_placeholder') }}"
                                       class="w-64 rounded-full border-gray-300 focus:border-primary focus:ring-primary pl-4 pr-10 py-2 text-sm transition-colors" 
                                       value="{{ request()->get('q') }}" />
                                <button type="submit" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-primary transition-colors">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </form>

                        @auth
                            <!-- Wishlist -->
                            <a href="{{ route('wishlist.index') }}" class="relative text-gray-500 hover:text-primary p-2 rounded-full hover:bg-gray-100 transition-colors">
                                <i class="fas fa-heart text-xl"></i>
                                <span id="wishlist-count" class="absolute -top-1 -right-1 bg-primary text-white text-xs rounded-full h-5 w-5 flex items-center justify-center text-[10px] font-semibold">
                                    0
                                </span>
                            </a>

                            <!-- Cart -->
                            <a href="{{ route('cart.index') }}" class="relative text-gray-500 hover:text-primary p-2 rounded-full hover:bg-gray-100 transition-colors">
                                <i class="fas fa-shopping-cart text-xl"></i>
                                <span id="cart-count" class="absolute -top-1 -right-1 bg-primary text-white text-xs rounded-full h-5 w-5 flex items-center justify-center text-[10px] font-semibold">
                                    0
                                </span>
                            </a>

                            <!-- Profile Dropdown -->
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" class="flex items-center text-sm font-medium text-gray-500 hover:text-primary p-2 rounded-full hover:bg-gray-100 transition-colors">
                                    <span>{{ Auth::user()->name }}</span>
                                    <i class="fas fa-chevron-down ml-1 text-xs"></i>
                                </button>

                                <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50" x-cloak>
                                    @if(Auth::user()->role === 'super_admin')
                                        <a href="{{ route('super-admin.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-primary transition-colors">
                                            {{ __('messages.admin_dashboard') }}
                                        </a>
                                    @elseif(Auth::user()->role === 'vendor')
                                        <a href="{{ route('vendor.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-primary transition-colors">
                                            {{ __('messages.vendor_dashboard') }}
                                        </a>
                                    @endif

                                    <a href="{{ route('orders.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-primary transition-colors">
                                        {{ __('messages.my_orders') }}
                                    </a>
                                    <a href="{{ route('account.profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-primary transition-colors">
                                        {{ __('messages.profile') }}
                                    </a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-primary transition-colors">
                                            {{ __('messages.logout') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="btn-secondary text-sm">{{ __('messages.login') }}</a>
                            <a href="{{ route('register') }}" class="btn-primary text-sm">
                                {{ __('messages.register') }}
                            </a>
                        @endauth
                    </div>
                </div>
            </div>

            <!-- Mobile menu -->
            <div x-show="mobileMenuOpen" class="sm:hidden" x-cloak>
                <div class="pt-2 pb-3 space-y-1">
                    <a href="{{ route('home') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('home') ? 'border-primary text-primary bg-blue-50' : 'border-transparent text-gray-600' }} hover:text-primary hover:bg-blue-50 hover:border-primary text-base font-medium transition-colors">{{ __('messages.home') }}</a>
                    <a href="{{ route('brands.index') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('brands.*') ? 'border-primary text-primary bg-blue-50' : 'border-transparent text-gray-600' }} hover:text-primary hover:bg-blue-50 hover:border-primary text-base font-medium transition-colors">{{ __('messages.brands') }}</a>
                    <a href="{{ route('about') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('about') ? 'border-primary text-primary bg-blue-50' : 'border-transparent text-gray-600' }} hover:text-primary hover:bg-blue-50 hover:border-primary text-base font-medium transition-colors">{{ __('messages.about') }}</a>
                    <a href="{{ route('contact') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('contact') ? 'border-primary text-primary bg-blue-50' : 'border-transparent text-gray-600' }} hover:text-primary hover:bg-blue-50 hover:border-primary text-base font-medium transition-colors">{{ __('messages.contact') }}</a>
                    
                    <!-- Mobile Search -->
                    <div class="px-4 py-2">
                        <form action="{{ route('search') }}" method="GET" class="flex items-center">
                            <div class="relative w-full">
                                <input type="text" name="q" placeholder="{{ __('messages.search_placeholder') }}"
                                       class="w-full rounded-full border-gray-300 focus:border-primary focus:ring-primary pl-4 pr-10 py-2 text-sm transition-colors" 
                                       value="{{ request()->get('q') }}" />
                                <button type="submit" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-primary transition-colors">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Mobile Language Switcher -->
                    <div class="px-4 py-2">
                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Language / Til</div>
                        <div class="flex gap-2">
                            <a href="{{ route('lang.switch', ['locale' => 'en']) }}" 
                               class="flex-1 px-3 py-2 text-center text-sm font-medium rounded-lg {{ app()->getLocale() === 'en' ? 'bg-primary text-white' : 'bg-gray-100 text-gray-700' }} transition-colors">
                                EN
                            </a>
                            <a href="{{ route('lang.switch', ['locale' => 'ru']) }}" 
                               class="flex-1 px-3 py-2 text-center text-sm font-medium rounded-lg {{ app()->getLocale() === 'ru' ? 'bg-primary text-white' : 'bg-gray-100 text-gray-700' }} transition-colors">
                                RU
                            </a>
                            <a href="{{ route('lang.switch', ['locale' => 'uz']) }}" 
                               class="flex-1 px-3 py-2 text-center text-sm font-medium rounded-lg {{ app()->getLocale() === 'uz' ? 'bg-primary text-white' : 'bg-gray-100 text-gray-700' }} transition-colors">
                                UZ
                            </a>
                        </div>
                    </div>
                    
                    @auth
                        <!-- Mobile Cart & Wishlist -->
                        <div class="px-4 py-2 flex gap-3">
                            <a href="{{ route('wishlist.index') }}" class="flex-1 relative flex items-center justify-center px-4 py-3 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                                <i class="fas fa-heart text-lg mr-2 text-primary"></i>
                                <span class="font-medium text-gray-700">Wishlist</span>
                                <span id="mobile-wishlist-badge" class="absolute top-1 right-1 bg-primary text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-semibold">0</span>
                            </a>
                            <a href="{{ route('cart.index') }}" class="flex-1 relative flex items-center justify-center px-4 py-3 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                                <i class="fas fa-shopping-cart text-lg mr-2 text-primary"></i>
                                <span class="font-medium text-gray-700">Cart</span>
                                <span id="mobile-cart-badge" class="absolute top-1 right-1 bg-primary text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-semibold">0</span>
                            </a>
                        </div>
                    @endauth
                    
                    @auth
                        <div class="border-t border-gray-200 pt-4 pb-3">
                            <div class="flex items-center px-4">
                                <div class="flex-shrink-0">
                                    <span class="text-gray-800 font-medium">{{ Auth::user()->name }}</span>
                                </div>
                            </div>
                            <div class="mt-3 space-y-1">
                                @if(Auth::user()->role === 'super_admin')
                                    <a href="{{ route('super-admin.dashboard') }}" class="block px-4 py-2 text-base font-medium text-gray-600 hover:text-primary hover:bg-blue-50 transition-colors">{{ __('messages.admin_dashboard') }}</a>
                                @elseif(Auth::user()->role === 'vendor')
                                    <a href="{{ route('vendor.dashboard') }}" class="block px-4 py-2 text-base font-medium text-gray-600 hover:text-primary hover:bg-blue-50 transition-colors">{{ __('messages.vendor_dashboard') }}</a>
                                @endif
                                <a href="{{ route('orders.index') }}" class="block px-4 py-2 text-base font-medium text-gray-600 hover:text-primary hover:bg-blue-50 transition-colors">{{ __('messages.my_orders') }}</a>
                                <a href="{{ route('account.profile') }}" class="block px-4 py-2 text-base font-medium text-gray-600 hover:text-primary hover:bg-blue-50 transition-colors">{{ __('messages.profile') }}</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-base font-medium text-gray-600 hover:text-primary hover:bg-blue-50 transition-colors">{{ __('messages.logout') }}</button>
                                </form>
                            </div>
                        </div>
                    @else
                        <div class="border-t border-gray-200 pt-4 pb-3">
                            <div class="space-y-2 px-4">
                                <a href="{{ route('login') }}" class="btn-secondary block text-center">{{ __('messages.login') }}</a>
                                <a href="{{ route('register') }}" class="btn-primary block text-center">{{ __('messages.register') }}</a>
                            </div>
                        </div>
                    @endauth
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main class="mt-6 pb-20 md:pb-6">
            @if (session('success'))
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                    <div class="badge badge-success px-4 py-3 rounded-lg" role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                    <div class="badge badge-error px-4 py-3 rounded-lg" role="alert">
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            @yield('content')
        </main>

        <!-- Mobile Bottom Navigation (Mobile Only) -->
        @auth
        <nav class="mobile-bottom-nav md:hidden">
            <div class="flex justify-around items-center px-2">
                <!-- Home -->
                <a href="{{ route('home') }}" class="flex flex-col items-center py-2 px-3 {{ request()->routeIs('home') ? 'text-primary' : 'text-gray-500' }} hover:text-primary transition-colors">
                    <i class="fas fa-home text-xl mb-1"></i>
                    <span class="text-xs font-medium">{{ __('messages.home') }}</span>
                </a>
                
                <!-- Wishlist -->
                <a href="{{ route('wishlist.index') }}" class="flex flex-col items-center py-2 px-3 relative {{ request()->routeIs('wishlist.*') ? 'text-primary' : 'text-gray-500' }} hover:text-primary transition-colors">
                    <i class="fas fa-heart text-xl mb-1"></i>
                    <span class="text-xs font-medium">Wishlist</span>
                    <span id="mobile-wishlist-count" class="absolute top-0 right-1 bg-primary text-white text-xs rounded-full h-4 w-4 flex items-center justify-center text-[9px] font-semibold">0</span>
                </a>
                
                <!-- Cart -->
                <a href="{{ route('cart.index') }}" class="flex flex-col items-center py-2 px-3 relative {{ request()->routeIs('cart.*') ? 'text-primary' : 'text-gray-500' }} hover:text-primary transition-colors">
                    <i class="fas fa-shopping-cart text-xl mb-1"></i>
                    <span class="text-xs font-medium">Cart</span>
                    <span id="mobile-cart-count" class="absolute top-0 right-1 bg-primary text-white text-xs rounded-full h-4 w-4 flex items-center justify-center text-[9px] font-semibold">0</span>
                </a>
                
                <!-- Orders -->
                <a href="{{ route('orders.index') }}" class="flex flex-col items-center py-2 px-3 {{ request()->routeIs('orders.*') ? 'text-primary' : 'text-gray-500' }} hover:text-primary transition-colors">
                    <i class="fas fa-box text-xl mb-1"></i>
                    <span class="text-xs font-medium">Orders</span>
                </a>
                
                <!-- Profile -->
                <a href="{{ route('account.profile') }}" class="flex flex-col items-center py-2 px-3 {{ request()->routeIs('account.*') ? 'text-primary' : 'text-gray-500' }} hover:text-primary transition-colors">
                    <i class="fas fa-user text-xl mb-1"></i>
                    <span class="text-xs font-medium">Profile</span>
                </a>
            </div>
        </nav>
        @endauth

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
                            <button type="submit" class="mt-2 w-full btn-primary">
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
                    const mobileCount = document.getElementById('mobile-cart-count');
                    const mobileBadge = document.getElementById('mobile-cart-badge');
                    if (mobileCount) mobileCount.textContent = data.count;
                    if (mobileBadge) mobileBadge.textContent = data.count;
                });

            fetch('{{ route("wishlist.count") }}')
                .then(res => res.json())
                .then(data => {
                    document.getElementById('wishlist-count').textContent = data.count;
                    const mobileCount = document.getElementById('mobile-wishlist-count');
                    const mobileBadge = document.getElementById('mobile-wishlist-badge');
                    if (mobileCount) mobileCount.textContent = data.count;
                    if (mobileBadge) mobileBadge.textContent = data.count;
                });
        }

        updateCounts();
        @endauth
    </script>
</body>
</html>