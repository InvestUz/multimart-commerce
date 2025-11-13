@extends('layouts.admin')

@section('title', 'Brands Management')

@section('content')
<div class="p-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Brands</h1>
            <p class="text-gray-600 mt-1">Manage product brands</p>
        </div>
        <a href="{{ route('super-admin.brands.create') }}"
           class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add Brand
        </a>
    </div>

    <!-- Brands Grid -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-6">
        @forelse($brands as $brand)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-shadow">
                <!-- Brand Logo -->
                @if($brand->logo)
                    <div class="w-full h-24 bg-gray-50 rounded-lg mb-4 flex items-center justify-center overflow-hidden">
                        <img src="{{ asset('storage/' . $brand->logo) }}"
                             alt="{{ $brand->name }}"
                             class="max-w-full max-h-full object-contain">
                    </div>
                @else
                    <div class="w-full h-24 bg-gray-100 rounded-lg mb-4 flex items-center justify-center">
                        <span class="text-2xl font-bold text-gray-400">{{ substr($brand->name, 0, 1) }}</span>
                    </div>
                @endif

                <!-- Brand Info -->
                <div class="mb-4">
                    <h3 class="text-sm font-semibold text-gray-900 text-center mb-2">{{ $brand->name }}</h3>
                    <p class="text-xs text-gray-500 text-center">{{ $brand->products_count ?? 0 }} products</p>
                </div>

                <!-- Status -->
                <div class="mb-4 flex justify-center">
                    <span class="px-2 py-1 rounded-full text-xs font-medium
                        {{ $brand->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $brand->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>

                <!-- Actions -->
                <div class="flex flex-col space-y-2">
                    <a href="{{ route('super-admin.brands.edit', $brand) }}"
                       class="bg-blue-50 text-blue-600 px-3 py-2 rounded-lg hover:bg-blue-100 text-center text-xs font-medium">
                        Edit
                    </a>
                    <form method="POST" action="{{ route('super-admin.brands.destroy', $brand) }}"
                          onsubmit="return confirm('Are you sure you want to delete this brand?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="w-full bg-red-50 text-red-600 px-3 py-2 rounded-lg hover:bg-red-100 text-xs font-medium">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-lg shadow-sm p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">No brands found</h3>
                <p class="mt-2 text-sm text-gray-500">Get started by creating a new brand.</p>
                <div class="mt-6">
                    <a href="{{ route('super-admin.brands.create') }}"
                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Add Brand
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($brands->hasPages())
        <div class="mt-6">
            {{ $brands->links() }}
        </div>
    @endif
</div>
@endsection
