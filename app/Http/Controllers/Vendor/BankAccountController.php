<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\VendorBankAccount;
use Illuminate\Http\Request;

class BankAccountController extends Controller
{
    public function index()
    {
        $bankAccounts = auth()->user()->bankAccounts()->latest()->get();
        return view('vendor.bank-accounts.index', compact('bankAccounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'bank_name' => 'required|string|max:255',
            'account_holder_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:50',
            'routing_number' => 'nullable|string|max:50',
            'swift_code' => 'nullable|string|max:50',
            'is_primary' => 'boolean',
        ]);

        $validated['vendor_id'] = auth()->id();

        if ($request->is_primary) {
            auth()->user()->bankAccounts()->update(['is_primary' => false]);
        }

        VendorBankAccount::create($validated);

        return back()->with('success', 'Bank account added successfully!');
    }

    public function update(Request $request, VendorBankAccount $account)
    {
        $this->authorize('update', $account);

        $validated = $request->validate([
            'bank_name' => 'required|string|max:255',
            'account_holder_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:50',
            'routing_number' => 'nullable|string|max:50',
            'swift_code' => 'nullable|string|max:50',
            'is_primary' => 'boolean',
        ]);

        if ($request->is_primary) {
            auth()->user()->bankAccounts()
                ->where('id', '!=', $account->id)
                ->update(['is_primary' => false]);
        }

        $account->update($validated);

        return back()->with('success', 'Bank account updated successfully!');
    }

    public function destroy(VendorBankAccount $account)
    {
        $this->authorize('delete', $account);

        if ($account->is_primary && auth()->user()->bankAccounts()->count() > 1) {
            return back()->with('error', 'Cannot delete primary account. Set another as primary first.');
        }

        $account->delete();

        return back()->with('success', 'Bank account deleted successfully!');
    }

    public function makePrimary(VendorBankAccount $account)
    {
        $this->authorize('update', $account);

        auth()->user()->bankAccounts()->update(['is_primary' => false]);
        $account->update(['is_primary' => true]);

        return back()->with('success', 'Primary account updated successfully!');
    }
}
