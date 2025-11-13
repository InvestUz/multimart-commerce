<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SettingController extends Controller
{
    public function index()
    {
        // Get all settings as key-value pairs
        $settings = Setting::pluck('value', 'key')->toArray();

        return view('super-admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            // General Settings
            'site_name' => 'nullable|string|max:255',
            'site_description' => 'nullable|string|max:500',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'currency' => 'nullable|string|max:10',
            'timezone' => 'nullable|string|max:50',

            // Email Settings
            'smtp_host' => 'nullable|string|max:255',
            'smtp_port' => 'nullable|string|max:10',
            'smtp_encryption' => 'nullable|string|max:10',
            'smtp_username' => 'nullable|string|max:255',
            'smtp_password' => 'nullable|string|max:255',

            // Payment Settings
            'cash_on_delivery' => 'nullable|boolean',
            'credit_card' => 'nullable|boolean',
            'tax_rate' => 'nullable|numeric|min:0|max:100',

            // Shipping Settings
            'default_shipping_cost' => 'nullable|numeric|min:0',
            'free_shipping_threshold' => 'nullable|numeric|min:0',
        ]);

        // Convert boolean checkboxes
        $validated['cash_on_delivery'] = $request->has('cash_on_delivery') ? 1 : 0;
        $validated['credit_card'] = $request->has('credit_card') ? 1 : 0;

        // Update or create each setting
        foreach ($validated as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value ?? '']
            );
        }

        // Clear settings cache
        Cache::forget('settings');

        return back()->with('success', 'Settings updated successfully!');
    }
}
