<?php
namespace App\Http\Controllers\SuperAdmin;
use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
            $validated['image'] = $request->file('image')->store('banners', 'public');
        }

        Banner::create($validated);
        return redirect()->route('super-admin.banners.index')->with('success', 'Banner created!');
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
            if ($banner->image) Storage::disk('public')->delete($banner->image);
            $validated['image'] = $request->file('image')->store('banners', 'public');
        }

        $banner->update($validated);
        return redirect()->route('super-admin.banners.index')->with('success', 'Banner updated!');
    }

    public function destroy(Banner $banner) {
        if ($banner->image) Storage::disk('public')->delete($banner->image);
        $banner->delete();
        return redirect()->route('super-admin.banners.index')->with('success', 'Banner deleted!');
    }
}
