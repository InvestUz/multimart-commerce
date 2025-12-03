@extends('layouts.app')

@section('title', 'Request Refund - ' . config('app.name'))

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row gap-8">
        <!-- Sidebar -->
        @include('account.sidebar')
        
        <!-- Main Content -->
        <div class="md:w-3/4">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h1 class="text-2xl font-bold text-gray-800 mb-6">Request Refund for Order #{{ $order->order_number }}</h1>

                <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <h3 class="font-medium text-gray-900 mb-2">Order Summary</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Order Date</p>
                            <p class="font-medium">{{ $order->created_at->format('M d, Y') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Total Amount</p>
                            <p class="font-medium">{{ config('app.currency_symbol') }}{{ number_format($order->total, 2) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Payment Status</p>
                            <p class="font-medium">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($order->payment_status == 'paid') bg-green-100 text-green-800
                                    @elseif($order->payment_status == 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($order->payment_status == 'failed') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ ucfirst($order->payment_status) }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>

                <form action="{{ route('account.refunds.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="order_id" value="{{ $order->id }}">
                    
                    <div class="mb-6">
                        <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                            Refund Amount (Max: {{ config('app.currency_symbol') }}{{ number_format($order->total, 2) }})
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">{{ config('app.currency_symbol') }}</span>
                            </div>
                            <input type="number" name="amount" id="amount" 
                                class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                min="0.01" max="{{ $order->total }}" step="0.01" 
                                value="{{ old('amount', $order->total) }}" required>
                        </div>
                        @error('amount')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">
                            Reason for Refund
                        </label>
                        <textarea name="reason" id="reason" rows="4" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Please explain why you are requesting a refund..." required>{{ old('reason') }}</textarea>
                        @error('reason')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end space-x-4">
                        <a href="{{ route('account.refunds.index') }}" class="px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none">
                            Cancel
                        </a>
                        <button type="submit" class="px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none">
                            Submit Refund Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection