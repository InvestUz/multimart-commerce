@extends('layouts.vendor')

@section('title', 'Product Reviews')

@section('content')
<div class="p-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Product Reviews</h1>
            <p class="text-gray-600 mt-1">Manage reviews for your products</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <form method="GET" action="{{ route('vendor.reviews.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Product name, customer..."
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">All Reviews</option>
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
                            @if($review->user)
                                <span class="text-sm font-semibold text-gray-600">{{ substr($review->user->name, 0, 2) }}</span>
                            @else
                                <span class="text-sm font-semibold text-gray-600">??</span>
                            @endif
                        </div>

                        <!-- Review Content -->
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-sm font-medium text-gray-900">{{ $review->user->name ?? 'Anonymous' }}</h3>
                                    <p class="text-sm text-gray-500">{{ $review->created_at->format('M d, Y') }}</p>
                                </div>
                                <div class="flex items-center">
                                    <!-- Rating Stars -->
                                    <div class="flex text-yellow-400">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star{{ $i <= $review->rating ? '' : '-o' }} text-sm"></i>
                                        @endfor
                                    </div>
                                    <span class="ml-2 text-sm text-gray-600">{{ $review->rating }}/5</span>
                                </div>
                            </div>

                            <!-- Product Info -->
                            <div class="mt-2">
                                <p class="text-sm text-gray-600">
                                    <span class="font-medium">Product:</span> {{ $review->product->name }}
                                </p>
                            </div>

                            <!-- Review Comment -->
                            <div class="mt-3">
                                <p class="text-gray-700">{{ $review->comment }}</p>
                            </div>

                            <!-- Vendor Response -->
                            @if($review->vendor_response)
                                <div class="mt-4 ml-8 p-4 bg-gray-50 rounded">
                                    <p class="text-sm font-semibold text-gray-900">Your Response:</p>
                                    <p class="text-sm text-gray-600 mt-1">{{ $review->vendor_response }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Status and Actions -->
                    <div class="flex flex-col items-end space-y-3">
                        <span class="px-2 py-1 {{ $review->is_approved ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }} text-xs font-medium rounded">
                            {{ $review->is_approved ? 'Approved' : 'Pending' }}
                        </span>

                        @if(!$review->vendor_response)
                            <button onclick="openResponseModal({{ $review->id }})"
                                    class="px-3 py-1 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                                Respond
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
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

<!-- Response Modal -->
<div id="response-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md mx-4 w-full">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Respond to Review</h3>
        <form id="response-form" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Your Response
                </label>
                <textarea name="vendor_response"
                          id="vendor-response"
                          rows="4"
                          required
                          placeholder="Write your response to this review..."
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" 
                        onclick="closeResponseModal()" 
                        class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300">
                    Cancel
                </button>
                <button type="submit" 
                        class="px-4 py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                    Submit Response
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    let currentReviewId = null;

    function openResponseModal(reviewId) {
        currentReviewId = reviewId;
        document.getElementById('response-modal').classList.remove('hidden');
        document.getElementById('response-modal').classList.add('flex');
        document.getElementById('vendor-response').value = '';
        document.getElementById('response-form').action = `/vendor/reviews/${reviewId}/respond`;
    }

    function closeResponseModal() {
        document.getElementById('response-modal').classList.add('hidden');
        document.getElementById('response-modal').classList.remove('flex');
        currentReviewId = null;
    }

    // Close modal when clicking outside
    document.getElementById('response-modal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeResponseModal();
        }
    });
</script>
@endsection