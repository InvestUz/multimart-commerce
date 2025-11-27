@extends('layouts.vendor')

@section('title', 'Payouts')

@section('content')
<div class="p-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Payouts</h1>
            <p class="text-gray-600 mt-1">View your payout history</p>
        </div>
        <a href="{{ route('vendor.earnings.index') }}" 
           class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300">
            Back to Earnings
        </a>
    </div>

    <!-- Payouts Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Payout History</h3>
        </div>
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Payout #</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($payouts as $payout)
                    <tr>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $payout->payout_number }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $payout->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">${{ number_format($payout->amount, 2) }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                @if($payout->status === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($payout->status === 'processing') bg-blue-100 text-blue-800
                                @elseif($payout->status === 'completed') bg-green-100 text-green-800
                                @elseif($payout->status === 'cancelled') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst($payout->status) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                            No payouts found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        @if($payouts->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $payouts->links() }}
            </div>
        @endif
    </div>
</div>
@endsection