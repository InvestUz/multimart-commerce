<?php
namespace App\Http\Controllers\SuperAdmin;
use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BannerController extends Controller
{
    public function index() {
        $banners = Banner::orderBy('order')->paginate(20);
        return view('super-admin.banners.index', compact('banners'));
    }

    public function create() {
        return view('super-admin.banners.create');
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'required|image|max:2048',
            'link' => 'nullable|url',
            'order' => 'nullable|integer',
            'is_active' => 'boolean',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('banners', 'public');
        }

        // Set default order if not provided
        if (!isset($validated['order'])) {
            $validated['order'] = Banner::max('order') + 1;
        }

        // Handle checkbox value
        $validated['is_active'] = $request->has('is_active');

        Banner::create($validated);
        return redirect()->route('super-admin.banners.index')->with('success', 'Banner created successfully!');
    }

    public function edit(Banner $banner) {
        return view('super-admin.banners.edit', compact('banner'));
    }

    public function update(Request $request, Banner $banner) {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|max:2048',
            'link' => 'nullable|url',
            'order' => 'nullable|integer',
            'is_active' => 'boolean',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($banner->image_path) {
                Storage::disk('public')->delete($banner->image_path);
            }
            $validated['image_path'] = $request->file('image')->store('banners', 'public');
        } else {
            // Keep the existing image path
            unset($validated['image']);
        }

        // Set default order if not provided
        if (!isset($validated['order'])) {
            $validated['order'] = $banner->order;
        }

        // Handle checkbox value
        $validated['is_active'] = $request->has('is_active');

        $banner->update($validated);
        return redirect()->route('super-admin.banners.index')->with('success', 'Banner updated successfully!');
    }

    public function destroy(Banner $banner) {
        if ($banner->image_path) {
            Storage::disk('public')->delete($banner->image_path);
        }
        $banner->delete();
        return redirect()->route('super-admin.banners.index')->with('success', 'Banner deleted successfully!');
    }
}