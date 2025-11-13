@extends('layouts.admin')

@section('title', 'User Details - ' . $user->name)

@section('content')
<div class="p-6">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">User Details</h1>
                <p class="text-gray-600 mt-1">View and manage user information</p>
            </div>
            <a href="{{ route('super-admin.users.index') }}"
               class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300">
                Back to Users
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- User Profile Card -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <!-- Avatar -->
                <div class="flex justify-center mb-4">
                    <div class="w-24 h-24 bg-gray-200 rounded-full flex items-center justify-center">
                        @if($user->avatar)
                            <img src="{{ asset('storage/' . $user->avatar) }}"
                                 alt="{{ $user->name }}"
                                 class="w-full h-full rounded-full object-cover">
                        @else
                            <span class="text-3xl font-bold text-gray-600">{{ substr($user->name, 0, 2) }}</span>
                        @endif
                    </div>
                </div>

                <!-- User Info -->
                <div class="text-center mb-6">
                    <h2 class="text-xl font-bold text-gray-900">{{ $user->name }}</h2>
                    <p class="text-sm text-gray-600">{{ $user->email }}</p>
                    @if($user->phone)
                        <p class="text-sm text-gray-600 mt-1">{{ $user->phone }}</p>
                    @endif
                </div>

                <!-- Role Badge -->
                <div class="flex justify-center mb-6">
                    <span class="px-3 py-1 text-sm font-medium rounded-full
                        @if($user->role === 'super_admin') bg-purple-100 text-purple-800
                        @elseif($user->role === 'admin') bg-blue-100 text-blue-800
                        @elseif($user->role === 'vendor') bg-green-100 text-green-800
                        @else bg-gray-100 text-gray-800
                        @endif">
                        {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                    </span>
                </div>

                <!-- Details -->
                <div class="space-y-3 mb-6">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Status:</span>
                        <span class="font-medium {{ $user->is_active ? 'text-green-600' : 'text-red-600' }}">
                            {{ $user->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Joined:</span>
                        <span class="font-medium text-gray-900">{{ $user->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Email Verified:</span>
                        <span class="font-medium {{ $user->email_verified_at ? 'text-green-600' : 'text-red-600' }}">
                            {{ $user->email_verified_at ? 'Yes' : 'No' }}
                        </span>
                    </div>
                </div>

                <!-- Actions -->
                <div class="space-y-2">
                    @if(auth()->id() !== $user->id)
                        <form method="POST" action="{{ route('super-admin.users.toggle-status', $user) }}">
                            @csrf
                            <button type="submit"
                                    class="w-full {{ $user->is_active ? 'bg-red-50 text-red-600 hover:bg-red-100' : 'bg-green-50 text-green-600 hover:bg-green-100' }} px-4 py-2 rounded-lg text-sm font-medium">
                                {{ $user->is_active ? 'Deactivate User' : 'Activate User' }}
                            </button>
                        </form>

                        @if($user->role !== 'super_admin')
                            <form method="POST" action="{{ route('super-admin.users.destroy', $user) }}"
                                  onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="w-full bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 text-sm font-medium">
                                    Delete User
                                </button>
                            </form>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        <!-- User Activity -->
        <div class="lg:col-span-2 space-y-6">
            @if($user->role === 'vendor')
                <!-- Vendor Statistics -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Vendor Statistics</h3>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="bg-blue-50 rounded-lg p-4">
                            <p class="text-sm text-blue-600 mb-1">Total Products</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $user->products_count ?? 0 }}</p>
                        </div>
                        <div class="bg-green-50 rounded-lg p-4">
                            <p class="text-sm text-green-600 mb-1">Total Revenue</p>
                            <p class="text-2xl font-bold text-gray-900">${{ number_format($stats['totalRevenue'] ?? 0, 2) }}</p>
                        </div>
                        <div class="bg-purple-50 rounded-lg p-4">
                            <p class="text-sm text-purple-600 mb-1">Total Orders</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['totalOrders'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>

                <!-- Recent Products -->
                @if($user->products && $user->products->count() > 0)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Products</h3>
                        <div class="space-y-3">
                            @foreach($user->products as $product)
                                <div class="flex items-center space-x-4 p-3 bg-gray-50 rounded-lg">
                                    <div class="w-12 h-12 bg-gray-200 rounded-lg overflow-hidden flex-shrink-0">
                                        @if($product->images->count() > 0)
                                            <img src="{{ asset('storage/' . $product->images->first()->image_path) }}"
                                                 alt="{{ $product->name }}"
                                                 class="w-full h-full object-cover">
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ $product->name }}</p>
                                        <p class="text-xs text-gray-500">${{ number_format($product->price, 2) }}</p>
                                    </div>
                                    <span class="px-2 py-1 text-xs rounded-full
                                        {{ $product->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $product->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

            @elseif($user->role === 'customer')
                <!-- Customer Statistics -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Customer Statistics</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-blue-50 rounded-lg p-4">
                            <p class="text-sm text-blue-600 mb-1">Total Orders</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['totalOrders'] ?? 0 }}</p>
                        </div>
                        <div class="bg-green-50 rounded-lg p-4">
                            <p class="text-sm text-green-600 mb-1">Total Spent</p>
                            <p class="text-2xl font-bold text-gray-900">${{ number_format($stats['totalSpent'] ?? 0, 2) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Recent Orders -->
                @if($user->orders && $user->orders->count() > 0)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Orders</h3>
                        <div class="space-y-3">
                            @foreach($user->orders as $order)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $order->order_number }}</p>
                                        <p class="text-xs text-gray-500">{{ $order->created_at->format('M d, Y') }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-semibold text-gray-900">${{ number_format($order->total, 2) }}</p>
                                        <span class="px-2 py-1 text-xs rounded-full
                                            @if($order->status === 'delivered') bg-green-100 text-green-800
                                            @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                                            @else bg-blue-100 text-blue-800
                                            @endif">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection
