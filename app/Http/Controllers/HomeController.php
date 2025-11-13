<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Banner;
use App\Models\FlashSale;
use App\Models\Order;
use App\Models\Address;
use App\Models\Notification;
use App\Models\SupportTicket;
use App\Models\TicketReply;
use App\Models\Refund;
use App\Models\Page;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class HomeController extends Controller
{
    public function index()
    {
        $banners = Banner::where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->orderBy('order')
            ->get();

        $categories = Category::where('is_active', true)
            ->withCount('products')
            ->orderBy('order')
            ->take(8)
            ->get();

        $featuredProducts = Product::where('is_active', true)
            ->where('is_featured', true)
            ->with(['vendor', 'category', 'images'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->take(8)
            ->get();

        $newArrivals = Product::where('is_active', true)
            ->with(['vendor', 'category', 'images'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->latest()
            ->take(8)
            ->get();

        $flashSale = FlashSale::where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->with(['products.images', 'products.vendor'])
            ->first();

        $brands = Brand::where('is_active', true)
            ->orderBy('name')
            ->take(12)
            ->get();

        return view('home', compact(
            'banners',
            'categories',
            'featuredProducts',
            'newArrivals',
            'flashSale',
            'brands'
        ));
    }

    public function category($slug)
    {
        $category = Category::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $products = Product::where('category_id', $category->id)
            ->where('is_active', true)
            ->with(['vendor', 'images'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->paginate(24);

        $subCategories = $category->subCategories()
            ->where('is_active', true)
            ->withCount('products')
            ->get();

        return view('category', compact('category', 'products', 'subCategories'));
    }

    public function product($slug)
    {
        $product = Product::where('slug', $slug)
            ->where('is_active', true)
            ->with(['vendor', 'category', 'subCategory', 'brand', 'images', 'variants'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->firstOrFail();

        $reviews = $product->reviews()
            ->where('is_approved', true)
            ->with('user')
            ->latest()
            ->paginate(10);

        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->with(['vendor', 'images'])
            ->withAvg('reviews', 'rating')
            ->take(8)
            ->get();

        return view('product', compact('product', 'reviews', 'relatedProducts'));
    }

    public function search(Request $request)
    {
        $query = $request->input('q');
        $categoryId = $request->input('category');
        $minPrice = $request->input('min_price');
        $maxPrice = $request->input('max_price');
        $sortBy = $request->input('sort', 'relevance');

        $products = Product::where('is_active', true)
            ->when($query, function ($q) use ($query) {
                $q->where(function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                      ->orWhere('description', 'like', "%{$query}%")
                      ->orWhere('sku', 'like', "%{$query}%");
                });
            })
            ->when($categoryId, function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            })
            ->when($minPrice, function ($q) use ($minPrice) {
                $q->where('price', '>=', $minPrice);
            })
            ->when($maxPrice, function ($q) use ($maxPrice) {
                $q->where('price', '<=', $maxPrice);
            })
            ->with(['vendor', 'images'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews');

        switch ($sortBy) {
            case 'price_low':
                $products->orderBy('price', 'asc');
                break;
            case 'price_high':
                $products->orderBy('price', 'desc');
                break;
            case 'newest':
                $products->latest();
                break;
            case 'rating':
                $products->orderBy('reviews_avg_rating', 'desc');
                break;
            default:
                $products->orderByRaw("CASE WHEN name LIKE ? THEN 0 ELSE 1 END", ["%{$query}%"]);
        }

        $products = $products->paginate(24);

        $categories = Category::where('is_active', true)->get();

        return view('search', compact('products', 'categories', 'query'));
    }

    public function brands()
    {
        $brands = Brand::where('is_active', true)
            ->withCount('products')
            ->orderBy('name')
            ->paginate(24);

        return view('brands', compact('brands'));
    }

    public function brand($slug)
    {
        $brand = Brand::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $products = Product::where('brand_id', $brand->id)
            ->where('is_active', true)
            ->with(['vendor', 'images'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->paginate(24);

        return view('brand', compact('brand', 'products'));
    }

    public function about()
    {
        return view('about');
    }

    public function contact()
    {
        return view('contact');
    }

    public function contactSubmit(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
        ]);

        // Here you would typically send an email or save to database
        // Mail::to(config('mail.from.address'))->send(new ContactMessage($validated));

        return back()->with('success', 'Your message has been sent successfully!');
    }

    public function profile()
    {
        $user = auth()->user();
        return view('account.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'current_password' => 'nullable|required_with:password',
            'password' => 'nullable|min:8|confirmed',
        ]);

        if ($request->filled('current_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect']);
            }
            $validated['password'] = Hash::make($request->password);
        } else {
            unset($validated['password']);
        }

        unset($validated['current_password']);

        $user->update($validated);

        return back()->with('success', 'Profile updated successfully!');
    }

    public function addresses()
    {
        $addresses = auth()->user()->addresses()->latest()->get();
        return view('account.addresses', compact('addresses'));
    }

    public function storeAddress(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:billing,shipping',
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'is_default' => 'boolean',
        ]);

        $validated['user_id'] = auth()->id();

        if ($request->is_default) {
            auth()->user()->addresses()
                ->where('type', $validated['type'])
                ->update(['is_default' => false]);
        }

        Address::create($validated);

        return back()->with('success', 'Address added successfully!');
    }

    public function updateAddress(Request $request, Address $address)
    {
        $this->authorize('update', $address);

        $validated = $request->validate([
            'type' => 'required|in:billing,shipping',
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'is_default' => 'boolean',
        ]);

        if ($request->is_default) {
            auth()->user()->addresses()
                ->where('type', $validated['type'])
                ->where('id', '!=', $address->id)
                ->update(['is_default' => false]);
        }

        $address->update($validated);

        return back()->with('success', 'Address updated successfully!');
    }

    public function deleteAddress(Address $address)
    {
        $this->authorize('delete', $address);
        $address->delete();

        return back()->with('success', 'Address deleted successfully!');
    }

    public function notifications()
    {
        $notifications = auth()->user()->notifications()->paginate(20);
        return view('notifications', compact('notifications'));
    }

    public function markAsRead(Notification $notification)
    {
        $this->authorize('update', $notification);
        $notification->update(['read_at' => now()]);

        return back()->with('success', 'Notification marked as read');
    }

    public function markAllAsRead()
    {
        auth()->user()->notifications()
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return back()->with('success', 'All notifications marked as read');
    }

    public function tickets()
    {
        $tickets = auth()->user()->supportTickets()->latest()->paginate(15);
        return view('tickets.index', compact('tickets'));
    }

    public function createTicket()
    {
        return view('tickets.create');
    }

    public function storeTicket(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
            'priority' => 'required|in:low,medium,high',
        ]);

        $ticket = auth()->user()->supportTickets()->create([
            'ticket_number' => 'TKT-' . strtoupper(Str::random(10)),
            'subject' => $validated['subject'],
            'priority' => $validated['priority'],
            'status' => 'open',
        ]);

        $ticket->replies()->create([
            'user_id' => auth()->id(),
            'message' => $validated['message'],
            'is_staff' => false,
        ]);

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Support ticket created successfully!');
    }

    public function showTicket(SupportTicket $ticket)
    {
        $this->authorize('view', $ticket);
        $ticket->load(['replies.user']);

        return view('tickets.show', compact('ticket'));
    }

    public function replyTicket(Request $request, SupportTicket $ticket)
    {
        $this->authorize('update', $ticket);

        $validated = $request->validate([
            'message' => 'required|string|max:5000',
        ]);

        $ticket->replies()->create([
            'user_id' => auth()->id(),
            'message' => $validated['message'],
            'is_staff' => false,
        ]);

        $ticket->update(['status' => 'open']);

        return back()->with('success', 'Reply added successfully!');
    }

    public function refunds()
    {
        $refunds = auth()->user()->refunds()->with('order')->latest()->paginate(15);
        return view('refunds.index', compact('refunds'));
    }

    public function createRefund(Order $order)
    {
        $this->authorize('view', $order);

        if ($order->refund) {
            return redirect()->route('refunds.show', $order->refund)
                ->with('info', 'A refund request already exists for this order.');
        }

        return view('refunds.create', compact('order'));
    }

    public function storeRefund(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'reason' => 'required|string|max:1000',
            'amount' => 'required|numeric|min:0',
        ]);

        $order = Order::findOrFail($validated['order_id']);
        $this->authorize('view', $order);

        if ($order->refund) {
            return back()->with('error', 'A refund request already exists for this order.');
        }

        $refund = Refund::create([
            'order_id' => $order->id,
            'user_id' => auth()->id(),
            'refund_number' => 'REF-' . strtoupper(Str::random(10)),
            'amount' => $validated['amount'],
            'reason' => $validated['reason'],
            'status' => 'pending',
        ]);

        return redirect()->route('refunds.show', $refund)
            ->with('success', 'Refund request submitted successfully!');
    }

    public function showRefund(Refund $refund)
    {
        $this->authorize('view', $refund);
        $refund->load('order');

        return view('refunds.show', compact('refund'));
    }

    public function newsletterSubscribe(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|max:255',
        ]);

        $subscriber = NewsletterSubscriber::firstOrCreate(
            ['email' => $validated['email']],
            [
                'token' => Str::random(32),
                'subscribed_at' => now(),
            ]
        );

        if ($subscriber->wasRecentlyCreated) {
            return back()->with('success', 'Thank you for subscribing to our newsletter!');
        }

        return back()->with('info', 'You are already subscribed to our newsletter.');
    }

    public function newsletterUnsubscribe($token)
    {
        $subscriber = NewsletterSubscriber::where('token', $token)->firstOrFail();
        $subscriber->update(['unsubscribed_at' => now()]);

        return view('newsletter.unsubscribed');
    }

    public function page($slug)
    {
        $page = Page::where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        return view('page', compact('page'));
    }
}
