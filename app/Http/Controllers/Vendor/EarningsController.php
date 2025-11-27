<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\VendorPayout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EarningsController extends Controller
{
    public function index()
    {
        $vendor = auth()->user();

        $totalEarnings = $vendor->vendorOrderItems()
            ->whereHas('order', function ($q) {
                $q->where('payment_status', 'paid');
            })
            ->sum('total');

        $pendingEarnings = $vendor->vendorOrderItems()
            ->where('vendor_status', 'delivered')
            ->whereNull('payout_id')
            ->sum('total');

        $completedPayouts = $vendor->payouts()
            ->where('status', 'completed')
            ->sum('amount');

        // Monthly earnings
        $monthlyEarnings = $vendor->vendorOrderItems()
            ->whereHas('order', function ($q) {
                $q->where('payment_status', 'paid');
            })
            ->where('created_at', '>=', now()->subMonths(12))
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('SUM(total) as earnings')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Recent transactions
        $recentTransactions = $vendor->vendorOrderItems()
            ->with(['order.user', 'product.images'])
            ->latest()
            ->take(20)
            ->get();

        return view('vendor.earnings.index', compact(
            'totalEarnings',
            'pendingEarnings',
            'completedPayouts',
            'monthlyEarnings',
            'recentTransactions'
        ));
    }

    public function payouts()
    {
        $payouts = auth()->user()->payouts()
            ->latest()
            ->paginate(20);

        return view('vendor.earnings.payouts', compact('payouts'));
    }

    public function requestPayout(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:10',
            'bank_account_id' => 'required|exists:vendor_bank_accounts,id',
        ]);

        $vendor = auth()->user();

        $pendingEarnings = $vendor->vendorOrderItems()
            ->where('vendor_status', 'delivered')
            ->whereNull('payout_id')
            ->sum('total');

        if ($validated['amount'] > $pendingEarnings) {
            return back()->with('error', 'Insufficient pending earnings.');
        }

        // Check if vendor has a primary bank account
        $bankAccount = $vendor->bankAccounts()->find($validated['bank_account_id']);
        if (!$bankAccount) {
            return back()->with('error', 'Invalid bank account.');
        }

        VendorPayout::create([
            'vendor_id' => $vendor->id,
            'payout_number' => 'PAY-' . strtoupper(Str::random(10)),
            'amount' => $validated['amount'],
            'bank_account_id' => $validated['bank_account_id'],
            'status' => 'pending',
        ]);

        return back()->with('success', 'Payout request submitted successfully!');
    }
}