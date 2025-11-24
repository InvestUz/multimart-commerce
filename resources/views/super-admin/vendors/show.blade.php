@extends('layouts.admin')

@section('title', 'Vendor Details - ' . $vendor->name)

@section('content')
<div class="p-6">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Vendor Details</h1>
                <p class="text-gray-600 mt-1">View and manage vendor information</p>
            </div>
            <a href="{{ route('super-admin.vendors.index') }}"
               class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300">
                Back to Vendors
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Vendor Profile Card -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <!-- Avatar -->
                <div class="flex justify-center mb-4">
                    <div class="w-24 h-24 bg-blue-100 rounded-full flex items-center justify-center">
                        @if($vendor->avatar)
                            <img src="{{ asset('storage/' . $vendor->avatar) }}"
                                 alt="{{ $vendor->name }}"
                                 class="w-full h-full rounded-full object-cover">
                        @else
                            <span class="text-3xl font-bold text-blue-600">{{ substr($vendor->name, 0, 2) }}</span>
                        @endif
                    </div>
                </div>

                <!-- Vendor Info -->
                <div class="text-center mb-6">
                    <h2 class="text-xl font-bold text-gray-900">{{ $vendor->name }}</h2>
                    <p class="text-sm text-gray-600">{{ $vendor->email }}</p>
                    @if($vendor->phone)
                        <p class="text-sm text-gray-600 mt-1">{{ $vendor->phone }}</p>
                    @endif
                    @if($vendor->store_name)
                        <p class="text-sm text-gray-500 mt-2 font-medium">{{ $vendor->store_name }}</p>
                    @endif
                </div>

                <!-- Status Badge -->
                <div class="flex justify-center mb-6">
                    <span class="px-3 py-1 text-sm font-medium rounded-full
                        {{ $vendor->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $vendor->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>

                <!-- Details -->
                <div class="space-y-3 mb-6">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Joined:</span>
                        <span class="font-medium text-gray-900">{{ $vendor->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Email Verified:</span>
                        <span class="font-medium {{ $vendor->email_verified_at ? 'text-green-600' : 'text-red-600' }}">
                            {{ $vendor->email_verified_at ? 'Yes' : 'No' }}
                        </span>
                    </div>
                    @if($vendor->address)
                        <div class="pt-3 border-t border-gray-200">
                            <p class="text-xs text-gray-600 mb-1">Address:</p>
                            <p class="text-sm text-gray-900">{{ $vendor->address }}</p>
                        </div>
                    @endif
                </div>

                <!-- Actions -->
                <div class="space-y-2">
                    <form method="POST" action="{{ route('super-admin.vendors.toggle-status', $vendor) }}">
                        @csrf
                        <button type="submit"
                                class="w-full {{ $vendor->is_active ? 'bg-red-50 text-red-600 hover:bg-red-100' : 'bg-green-50 text-green-600 hover:bg-green-100' }} px-4 py-2 rounded-lg text-sm font-medium">
                            {{ $vendor->is_active ? 'Deactivate Vendor' : 'Activate Vendor' }}
                        </button>
                    </form>

                    @if(!$vendor->email_verified_at)
                        <form method="POST" action="{{ route('super-admin.vendors.approve', $vendor) }}">
                            @csrf
                            <button type="submit"
                                    class="w-full bg-blue-50 text-blue-600 px-4 py-2 rounded-lg hover:bg-blue-100 text-sm font-medium">
                                Approve & Verify Email
                            </button>
                        </form>
                    @endif

                    <form method="POST" action="{{ route('super-admin.vendors.destroy', $vendor) }}"
                          onsubmit="return confirm('Are you sure you want to delete this vendor? This action cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="w-full bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 text-sm font-medium">
                            Delete Vendor
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Vendor Activity -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Statistics -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Vendor Statistics</h3>
                <div class="grid grid-cols-3 gap-4">
                    <div class="bg-blue-50 rounded-lg p-4">
                        <p class="text-sm text-blue-600 mb-1">Total Products</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $vendor->products_count ?? 0 }}</p>
                    </div>
                    <div class="bg-green-50 rounded-lg p-4">
                        <p class="text-sm text-green-600 mb-1">Total Revenue</p>
                        <p class="text-2xl font-bold text-gray-900">${{ number_format($totalRevenue, 2) }}</p>
                    </div>
                    <div class="bg-purple-50 rounded-lg p-4">
                        <p class="text-sm text-purple-600 mb-1">Total Orders</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalOrders }}</p>
                    </div>
                </div>
            </div>

            <!-- Store Information -->
            @if($vendor->store_name || $vendor->store_description)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Store Information</h3>

                    @if($vendor->store_name)
                        <div class="mb-4">
                            <p class="text-sm text-gray-600">Store Name</p>
                            <p class="text-base font-medium text-gray-900">{{ $vendor->store_name }}</p>
                        </div>
                    @endif

                    @if($vendor->store_description)
                        <div>
                            <p class="text-sm text-gray-600 mb-2">Store Description</p>
                            <p class="text-gray-700">{{ $vendor->store_description }}</p>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Recent Products -->
            @if($vendor->products && $vendor->products->count() > 0)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Recent Products</h3>
                        <a href="{{ route('super-admin.products.index', ['vendor' => $vendor->id]) }}"
                           class="text-sm text-blue-600 hover:text-blue-800">
                            View All →
                        </a>
                    </div>

                    <div class="space-y-3">
                        @foreach($vendor->products as $product)
                            <div class="flex items-center space-x-4 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                <div class="w-12 h-12 bg-gray-200 rounded-lg overflow-hidden flex-shrink-0">
                                    @if($product->images->count() > 0)
                                        <img src="{{ asset('storage/' . $product->images->first()->image_path) }}"
                                             alt="{{ $product->name }}"
                                             class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $product->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $product->category->name ?? 'No Category' }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-semibold text-gray-900">${{ number_format($product->price, 2) }}</p>
                                    <p class="text-xs text-gray-500">Stock: {{ $product->stock }}</p>
                                </div>
                                <div>
                                    <span class="px-2 py-1 text-xs rounded-full
                                        {{ $product->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $product->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                                <a href="{{ route('super-admin.products.show', $product) }}"
                                   class="text-blue-600 hover:text-blue-800">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            </div>
                        @endforeach
                    </div>

                    @if($vendor->products->count() >= 10)
                        <div class="mt-4 text-center">
                            <a href="{{ route('super-admin.products.index', ['vendor' => $vendor->id]) }}"
                               class="text-sm text-blue-600 hover:text-blue-800">
                                View all {{ $vendor->products_count }} products →
                            </a>
                        </div>
                    @endif
                </div>
            @else
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">No Products Yet</h3>
                    <p class="mt-2 text-sm text-gray-500">This vendor hasn't added any products.</p>
                </div>
            @endif

            <!-- Payment Information -->
            @if($vendor->bank_name || $vendor->bank_account)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Payment Information</h3>

                    <div class="space-y-3">
                        @if($vendor->bank_name)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Bank Name:</span>
                                <span class="font-medium text-gray-900">{{ $vendor->bank_name }}</span>
                            </div>
                        @endif
                        @if($vendor->bank_account)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Account Number:</span>
                                <span class="font-medium text-gray-900">{{ $vendor->bank_account }}</span>
                            </div>
                        @endif
                        @if($vendor->bank_holder_name)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Account Holder:</span>
                                <span class="font-medium text-gray-900">{{ $vendor->bank_holder_name }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
