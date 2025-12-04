<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Vendor Panel') - {{ config('app.name') }}</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>

    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gray-50">
    <div x-data="{ sidebarOpen: true }" class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
               class="fixed inset-y-0 left-0 z-50 w-64 bg-gray-900 transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0">

            <!-- Sidebar Header -->
            <div class="flex items-center justify-between h-16 px-6 bg-gray-800">
                <span class="text-xl font-bold text-white">Vendor Panel</span>
                <button @click="sidebarOpen = false" class="text-gray-400 lg:hidden">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-4 py-6 overflow-y-auto">
                <div class="space-y-1">
                    <!-- Dashboard -->
                    <a href="{{ route('vendor.dashboard') }}"
                       class="flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ request()->routeIs('vendor.dashboard') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        @lang('Dashboard')
                    </a>

                    <!-- Products -->
                    <a href="{{ route('vendor.products.index') }}"
                       class="flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ request()->routeIs('vendor.products.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        @lang('Products')
                    </a>

                    <!-- Orders -->
                    <a href="{{ route('vendor.orders.index') }}"
                       class="flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ request()->routeIs('vendor.orders.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        @lang('Orders')
                    </a>

                    <!-- Earnings -->
                    <a href="{{ route('vendor.earnings.index') }}"
                       class="flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ request()->routeIs('vendor.earnings.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        @lang('Earnings')
                    </a>

                    <!-- Reviews -->
                    <a href="{{ route('vendor.reviews.index') }}"
                       class="flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ request()->routeIs('vendor.reviews.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                        </svg>
                        @lang('Reviews')
                    </a>

                    <!-- Store Settings -->
                    <a href="{{ route('vendor.store.settings') }}"
                       class="flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ request()->routeIs('vendor.store.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        @lang('Store Settings')
                    </a>

                    <!-- Reports -->
                    <div x-data="{ open: {{ request()->routeIs('vendor.reports.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open" 
                                class="flex items-center justify-between w-full px-4 py-3 text-sm font-medium rounded-lg {{ request()->routeIs('vendor.reports.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                                @lang('Reports')
                            </div>
                            <svg :class="{'rotate-180': open}" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        
                        <div x-show="open" class="mt-1 ml-4 space-y-1">
                            <a href="{{ route('vendor.reports.sales') }}"
                               class="block px-4 py-2 text-sm rounded-lg {{ request()->routeIs('vendor.reports.sales') ? 'bg-gray-700 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                                @lang('Sales Report')
                            </a>
                            <a href="{{ route('vendor.reports.products') }}"
                               class="block px-4 py-2 text-sm rounded-lg {{ request()->routeIs('vendor.reports.products') ? 'bg-gray-700 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                                @lang('Product Report')
                            </a>
                            <a href="{{ route('vendor.reports.orders') }}"
                               class="block px-4 py-2 text-sm rounded-lg {{ request()->routeIs('vendor.reports.orders') ? 'bg-gray-700 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                                @lang('Orders Report')
                            </a>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- User Profile -->
            <div class="p-4 border-t border-gray-800">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-gray-700 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-sm font-semibold text-white">{{ substr(auth()->user()->name, 0, 2) }}</span>
                    </div>
                    <div class="ml-3 flex-1 min-w-0">
                        <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-400 truncate">Vendor</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="mt-3">
                    @csrf
                    <button type="submit"
                            class="w-full flex items-center px-4 py-2 text-sm font-medium text-gray-300 rounded-lg hover:bg-gray-800 hover:text-white">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        @lang('Logout')
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Header -->
            <header class="bg-white border-b border-gray-200">
                <div class="flex items-center justify-between h-16 px-6">
                    <!-- Mobile Menu Button -->
                    <button @click="sidebarOpen = true" class="text-gray-500 lg:hidden">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>

                    <div class="flex items-center space-x-4 ml-auto">
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

                        <!-- View Store -->
                        <a href="{{ route('home') }}"
                           target="_blank"
                           class="text-sm text-gray-600 hover:text-gray-900">
                            <svg class="w-5 h-5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                            </svg>
                            @lang('View Store')
                        </a>

                        <!-- Notifications -->
                        <a href="{{ route('vendor.notifications.view') }}" class="text-gray-500 hover:text-gray-900 relative">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            @if(auth()->user()->unreadNotifications->count() > 0)
                                <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full">
                                    {{ auth()->user()->unreadNotifications->count() }}
                                </span>
                            @endif
                        </a>
                    </div>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="flex-1 overflow-y-auto">
                <!-- Success Message -->
                @if(session('success'))
                    <div class="m-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                        <p class="text-sm text-green-800">{{ session('success') }}</p>
                    </div>
                @endif

                <!-- Error Message -->
                @if(session('error'))
                    <div class="m-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <p class="text-sm text-red-800">{{ session('error') }}</p>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>