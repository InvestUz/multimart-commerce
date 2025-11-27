@extends('layouts.admin')

@section('title', 'Vendors Management')

@section('content')
<div class="p-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Vendors</h1>
            <p class="text-gray-600 mt-1">Manage all vendors</p>
        </div>
        <div>
            <a href="{{ route('super-admin.vendors.create') }}" 
               class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                Create Vendor
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <form method="GET" action="{{ route('super-admin.vendors.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Vendor name, email..."
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">All</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Vendors Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($vendors as $vendor)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <!-- Vendor Info -->
                <div class="flex items-start space-x-4 mb-4">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                        @if($vendor->avatar)
                            <img src="{{ asset('storage/' . $vendor->avatar) }}"
                                 alt="{{ $vendor->name }}"
                                 class="w-full h-full rounded-full object-cover">
                        @else
                            <span class="text-xl font-bold text-blue-600">{{ substr($vendor->name, 0, 2) }}</span>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-lg font-semibold text-gray-900 truncate">{{ $vendor->name }}</h3>
                        <p class="text-sm text-gray-600 truncate">{{ $vendor->email }}</p>
                        @if($vendor->store_name)
                            <p class="text-sm text-gray-500 truncate mt-1">{{ $vendor->store_name }}</p>
                        @endif
                    </div>
                </div>

                <!-- Stats -->
                <div class="grid grid-cols-2 gap-4 mb-4 p-4 bg-gray-50 rounded-lg">
                    <div>
                        <p class="text-xs text-gray-600">Products</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $vendor->products_count ?? 0 }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-600">Revenue</p>
                        <p class="text-lg font-semibold text-gray-900">${{ number_format($vendor->total_revenue ?? 0, 0) }}</p>
                    </div>
                </div>

                <!-- Status Badge -->
                <div class="mb-4">
                    <span class="px-3 py-1 rounded-full text-xs font-medium
                        {{ $vendor->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $vendor->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>

                <!-- Actions -->
                <div class="flex items-center space-x-2">
                    <a href="{{ route('super-admin.vendors.show', $vendor) }}"
                       class="flex-1 bg-blue-50 text-blue-600 px-4 py-2 rounded-lg hover:bg-blue-100 text-center text-sm font-medium">
                        View Details
                    </a>
                    <form method="POST" action="{{ route('super-admin.vendors.toggle-status', $vendor) }}" class="flex-1">
                        @csrf
                        <button type="submit"
                                class="w-full {{ $vendor->is_active ? 'bg-red-50 text-red-600 hover:bg-red-100' : 'bg-green-50 text-green-600 hover:bg-green-100' }} px-4 py-2 rounded-lg text-sm font-medium">
                            {{ $vendor->is_active ? 'Deactivate' : 'Activate' }}
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-lg shadow-sm p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">No vendors found</h3>
                <p class="mt-2 text-sm text-gray-500">No vendors have registered yet.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($vendors->hasPages())
        <div class="mt-6">
            {{ $vendors->links() }}
        </div>
    @endif
</div>
@endsection
