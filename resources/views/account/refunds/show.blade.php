@extends('layouts.app')

@section('title', 'Refund Request #' . $refund->id . ' - ' . config('app.name'))

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row gap-8">
        <!-- Sidebar -->
        @include('account.sidebar')
        
        <!-- Main Content -->
        <div class="md:w-3/4">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Refund Request #{{ $refund->id }}</h1>
                        <p class="text-gray-600">For Order #{{ $refund->order->order_number }}</p>
                    </div>
                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full 
                        @if($refund->status == 'pending') bg-yellow-100 text-yellow-800
                        @elseif($refund->status == 'approved') bg-blue-100 text-blue-800
                        @elseif($refund->status == 'processing') bg-purple-100 text-purple-800
                        @elseif($refund->status == 'completed') bg-green-100 text-green-800
                        @elseif($refund->status == 'rejected') bg-red-100 text-red-800
                        @else bg-gray-100 text-gray-800 @endif">
                        {{ ucfirst($refund->status) }}
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-3">Refund Details</h3>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Amount:</span>
                                <span class="font-medium">{{ config('app.currency_symbol') }}{{ number_format($refund->amount, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Requested:</span>
                                <span class="font-medium">{{ $refund->created_at->format('M d, Y H:i') }}</span>
                            </div>
                            @if($refund->updated_at != $refund->created_at)
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Last Updated:</span>
                                    <span class="font-medium">{{ $refund->updated_at->format('M d, Y H:i') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="border border-gray-200 rounded-lg p-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-3">Order Details</h3>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Order Number:</span>
                                <a href="{{ route('orders.show', $refund->order) }}" class="font-medium text-blue-600 hover:text-blue-800">
                                    #{{ $refund->order->order_number }}
                                </a>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Order Total:</span>
                                <span class="font-medium">{{ config('app.currency_symbol') }}{{ number_format($refund->order->total, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Order Date:</span>
                                <span class="font-medium">{{ $refund->order->created_at->format('M d, Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-200 pt-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Reason for Refund</h3>
                    <p class="text-gray-700 whitespace-pre-wrap">{{ $refund->reason }}</p>
                </div>

                @if($refund->status == 'rejected' && $refund->rejection_reason)
                    <div class="border-t border-gray-200 pt-4 mt-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-3">Rejection Reason</h3>
                        <p class="text-gray-700 whitespace-pre-wrap bg-red-50 p-3 rounded">{{ $refund->rejection_reason }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection