@extends('layouts.vendor')

@section('title', 'Earnings')

@section('content')
<div class="p-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Earnings</h1>
            <p class="text-gray-600 mt-1">Track your sales and earnings</p>
        </div>
        <a href="{{ route('vendor.earnings.payouts') }}" 
           class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
            View Payouts
        </a>
    </div>

    <!-- Earnings Summary -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <!-- Total Earnings -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-lg">
                    <i class="fas fa-dollar-sign text-green-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Earnings</p>
                    <p class="text-2xl font-semibold text-gray-900">${{ number_format($totalEarnings, 2) }}</p>
                </div>
            </div>
        </div>

        <!-- Pending Earnings -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-100 rounded-lg">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Pending Earnings</p>
                    <p class="text-2xl font-semibold text-gray-900">${{ number_format($pendingEarnings, 2) }}</p>
                </div>
            </div>
        </div>

        <!-- Completed Payouts -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-lg">
                    <i class="fas fa-check-circle text-blue-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Completed Payouts</p>
                    <p class="text-2xl font-semibold text-gray-900">${{ number_format($completedPayouts, 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Earnings Chart -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Earnings Trend (Last 12 Months)</h3>
        <div class="h-80">
            <canvas id="earningsChart"></canvas>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Recent Transactions</h3>
        </div>
        <div class="divide-y divide-gray-200">
            @forelse($recentTransactions as $transaction)
                <div class="p-6 flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 w-12 h-12 bg-gray-200 rounded-lg overflow-hidden">
                            @if($transaction->product && $transaction->product->images && $transaction->product->images->first())
                                <img src="{{ asset('storage/' . $transaction->product->images->first()->image_path) }}" 
                                     alt="{{ $transaction->product->name ?? 'Product' }}" 
                                     class="w-full h-full object-cover">
                            @endif
                        </div>
                        <div class="ml-4">
                            <h4 class="text-sm font-medium text-gray-900">{{ $transaction->product->name ?? 'Product' }}</h4>
                            <p class="text-sm text-gray-500">
                                Order #{{ $transaction->order->order_number ?? 'N/A' }} • 
                                {{ $transaction->order->user->name ?? 'N/A' }}
                            </p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium text-gray-900">${{ number_format($transaction->total, 2) }}</p>
                        <p class="text-sm text-gray-500">{{ $transaction->created_at->format('M d, Y') }}</p>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center text-gray-500">
                    No recent transactions
                </div>
            @endforelse
        </div>
    </div>

    <!-- Payout Request Form -->
    @if($pendingEarnings > 0)
        <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Request Payout</h3>
            <form method="POST" action="{{ route('vendor.earnings.payouts.request') }}">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Amount <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-gray-500">$</span>
                            <input type="number"
                                   name="amount"
                                   step="0.01"
                                   min="10"
                                   max="{{ $pendingEarnings }}"
                                   required
                                   placeholder="0.00"
                                   class="w-full pl-8 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('amount') border-red-500 @enderror">
                        </div>
                        <p class="mt-1 text-sm text-gray-500">Pending earnings: ${{ number_format($pendingEarnings, 2) }}</p>
                        @error('amount')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex items-end">
                        <button type="submit"
                                class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                            Request Payout
                        </button>
                    </div>
                </div>
            </form>
        </div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Prepare chart data
    const months = @json($monthlyEarnings->pluck('month'));
    const earnings = @json($monthlyEarnings->pluck('earnings'));
    
    // Create chart
    const ctx = document.getElementById('earningsChart').getContext('2d');
    const earningsChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: months,
            datasets: [{
                label: 'Earnings',
                data: earnings,
                backgroundColor: '#3b82f6',
                borderColor: '#2563eb',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value;
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Earnings: $' + context.parsed.y.toFixed(2);
                        }
                    }
                }
            }
        }
    });
</script>
@endsection