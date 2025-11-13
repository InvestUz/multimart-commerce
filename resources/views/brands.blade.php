@extends('layouts.app')

@section('title', 'All Brands - ' . config('app.name'))

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Breadcrumb -->
    <div class="bg-white border-b">
        <div class="container mx-auto px-4 py-4">
            <nav class="flex items-center space-x-2 text-sm">
                <a href="{{ route('home') }}" class="text-gray-600 hover:text-gray-900">Home</a>
                <span class="text-gray-400">/</span>
                <span class="text-gray-900 font-medium">Brands</span>
            </nav>
        </div>
    </div>

    <!-- Page Header -->
    <div class="bg-white border-b">
        <div class="container mx-auto px-4 py-8">
            <h1 class="text-3xl font-bold text-gray-900">All Brands</h1>
            <p class="mt-2 text-gray-600">Discover products from your favorite brands</p>
        </div>
    </div>

    <!-- Brands Grid -->
    <div class="container mx-auto px-4 py-8">
        @if($brands->count() > 0)
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-6">
                @foreach($brands as $brand)
                    <a href="{{ route('brand.show', $brand->slug) }}"
                       class="bg-white rounded-lg border border-gray-200 hover:border-blue-500 hover:shadow-lg transition-all duration-200 p-6 flex flex-col items-center justify-center group">
                        <!-- Brand Logo -->
                        @if($brand->logo)
                            <img src="{{ asset('storage/' . $brand->logo) }}"
                                 alt="{{ $brand->name }}"
                                 class="w-20 h-20 object-contain mb-4 group-hover:scale-110 transition-transform duration-200">
                        @else
                            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                <span class="text-2xl font-bold text-gray-400">
                                    {{ substr($brand->name, 0, 1) }}
                                </span>
                            </div>
                        @endif

                        <!-- Brand Name -->
                        <h3 class="text-sm font-semibold text-gray-900 text-center group-hover:text-blue-600 transition-colors">
                            {{ $brand->name }}
                        </h3>

                        <!-- Product Count -->
                        <p class="text-xs text-gray-500 mt-1">
                            {{ $brand->products_count }} {{ Str::plural('product', $brand->products_count) }}
                        </p>
                    </a>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $brands->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">No brands found</h3>
                <p class="mt-2 text-sm text-gray-500">There are no brands available at the moment.</p>
                <div class="mt-6">
                    <a href="{{ route('home') }}"
                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Back to Home
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
