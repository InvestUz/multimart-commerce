@extends('layouts.admin')

@section('title', 'Reviews Management')

@section('content')
<div class="p-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Reviews</h1>
            <p class="text-gray-600 mt-1">Manage product reviews</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <form method="GET" action="{{ route('super-admin.reviews.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Product, user..."
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
                <select name="rating" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">All Ratings</option>
                    <option value="5" {{ request('rating') == '5' ? 'selected' : '' }}>5 Stars</option>
                    <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>4 Stars</option>
                    <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>3 Stars</option>
                    <option value="2" {{ request('rating') == '2' ? 'selected' : '' }}>2 Stars</option>
                    <option value="1" {{ request('rating') == '1' ? 'selected' : '' }}>1 Star</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">All</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Reviews List -->
    <div class="space-y-4">
        @forelse($reviews as $review)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-start space-x-4 flex-1">
                        <!-- User Avatar -->
                        <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-sm font-semibold text-gray-600">{{ substr($review->user->name, 0, 2) }}</span>
                        </div>

                        <!-- Review Content -->
                        <div class="flex-1">
                            <div class="flex items-center mb-2">
                                <h3 class="text-sm font-medium text-gray-900 mr-2">{{ $review->user->name }}</h3>
                                <div class="flex items-center">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }} fill-current" viewBox="0 0 20 20">
                                            <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                        </svg>
                                    @endfor
                                    <span class="ml-2 text-sm text-gray-500">{{ $review->created_at->diffForHumans() }}</span>
                                </div>
                            </div>

                            <p class="text-sm text-gray-700 mb-2">{{ $review->comment }}</p>

                            <!-- Product Info -->
                            <div class="flex items-center text-sm text-gray-500">
                                <span class="font-medium">Product:</span>
                                <a href="{{ route('product.show', $review->product->slug) }}"
                                   target="_blank"
                                   class="ml-1 text-blue-600 hover:text-blue-800">
                                    {{ $review->product->name }}
                                </a>
                            </div>

                            <!-- Badges -->
                            <div class="flex items-center space-x-2 mt-2">
                                @if($review->is_verified_purchase)
                                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded">
                                        Verified Purchase
                                    </span>
                                @endif
                                <span class="px-2 py-1 {{ $review->is_approved ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }} text-xs font-medium rounded">
                                    {{ $review->is_approved ? 'Approved' : 'Pending' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center space-x-2 ml-4">
                        @if(!$review->is_approved)
                            <form method="POST" action="{{ route('super-admin.reviews.approve', $review) }}">
                                @csrf
                                <button type="submit"
                                        class="px-3 py-1 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm">
                                    Approve
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('super-admin.reviews.reject', $review) }}">
                                @csrf
                                <button type="submit"
                                        class="px-3 py-1 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 text-sm">
                                    Unapprove
                                </button>
                            </form>
                        @endif

                        <form method="POST" action="{{ route('super-admin.reviews.destroy', $review) }}"
                              onsubmit="return confirm('Are you sure you want to delete this review?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="px-3 py-1 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">No reviews found</h3>
                <p class="mt-2 text-sm text-gray-500">There are no reviews to display.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($reviews->hasPages())
        <div class="mt-6">
            {{ $reviews->links() }}
        </div>
    @endif
</div>
@endsection
