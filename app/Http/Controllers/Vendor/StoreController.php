<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StoreController extends Controller
{
    public function settings()
    {
        $vendor = auth()->user();
        return view('vendor.store.settings', compact('vendor'));
    }

    public function updateSettings(Request $request)
    {
        $vendor = auth()->user();

        $validated = $request->validate([
            'shop_name' => 'required|string|max:255',
            'shop_description' => 'nullable|string|max:1000',
            'shop_logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'shop_banner' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
        ]);

        if ($request->hasFile('shop_logo')) {
            if ($vendor->shop_logo) {
                Storage::disk('public')->delete($vendor->shop_logo);
            }
            $validated['shop_logo'] = $request->file('shop_logo')->store('vendors', 'public');
        }

        if ($request->hasFile('shop_banner')) {
            if ($vendor->shop_banner) {
                Storage::disk('public')->delete($vendor->shop_banner);
            }
            $validated['shop_banner'] = $request->file('shop_banner')->store('vendors', 'public');
        }

        $vendor->update($validated);

        return back()->with('success', 'Store settings updated successfully!');
    }
}
