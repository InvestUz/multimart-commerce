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
use App\Models\SupportTicketReply;
use App\Models\Refund;
use App\Models\Page;
use App\Models\NewsletterSubscriber;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class HomeController extends Controller
{
    public function index()
    {
        // Check if banners table has date columns
        $hasDateColumns = Schema::hasColumns('banners', ['start_date', 'end_date']);

        $banners = Banner::where('is_active', true)
            ->when($hasDateColumns, function ($query) {
                $query->where('start_date', '<=', now())
                      ->where('end_date', '>=', now());
            })
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

        // Check if flash_sales table has date columns
        $hasFlashSaleDateColumns = Schema::hasColumns('flash_sales', ['start_date', 'end_date']);

        $flashSale = FlashSale::where('is_active', true)
            ->when($hasFlashSaleDateColumns, function ($query) {
                $query->where('start_date', '<=', now())
                      ->where('end_date', '>=', now());
            })
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
            ->with(['vendor', 'category', 'images'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->paginate(24);

        return view('brand', compact('brand', 'products'));
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
            'message' => 'required|string',
        ]);

        // In a real application, you would send an email or save to database
        // For now, we'll just return a success response
        return back()->with('success', 'Thank you for contacting us. We will get back to you soon.');
    }

    public function about()
    {
        return view('about');
    }

    public function addresses()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $addresses = $user->addresses()->get();
        return view('account.addresses', compact('addresses'));
    }

    public function storeAddress(Request $request)
    {
        try {
            $validated = $request->validate([
                'label' => 'required|string|max:50',
                'full_name' => 'required|string|max:255',
                'phone' => 'required|string|max:20',
                'address_line1' => 'required|string|max:255',
                'address_line2' => 'nullable|string|max:255',
                'city' => 'required|string|max:100',
                'state' => 'nullable|string|max:100',
                'postal_code' => 'required|string|max:20',
                'country' => 'required|string|max:100',
                'is_default' => 'boolean'
            ]);

            $validated['user_id'] = auth()->id();
            // Properly handle the checkbox value - if not present, set to false
            $validated['is_default'] = $request->input('is_default', false);
            
            // If this is set as default, unset other defaults
            if ($validated['is_default']) {
                UserAddress::where('user_id', auth()->id())->update(['is_default' => false]);
            }

            UserAddress::create($validated);

            // Check if this is a JSON request (for AJAX)
            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Address added successfully!']);
            }

            return back()->with('success', 'Address added successfully!');
        } catch (\Exception $e) {
            // Check if this is a JSON request (for AJAX)
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Failed to add address: ' . $e->getMessage()]);
            }
            return back()->with('error', 'Failed to add address: ' . $e->getMessage())->withInput();
        }
    }

    public function updateAddress(Request $request, UserAddress $address)
    {
        // Ensure the address belongs to the authenticated user
        if ($address->user_id !== auth()->id()) {
            abort(403);
        }

        try {
            $validated = $request->validate([
                'label' => 'required|string|max:50',
                'full_name' => 'required|string|max:255',
                'phone' => 'required|string|max:20',
                'address_line1' => 'required|string|max:255',
                'address_line2' => 'nullable|string|max:255',
                'city' => 'required|string|max:100',
                'state' => 'nullable|string|max:100',
                'postal_code' => 'required|string|max:20',
                'country' => 'required|string|max:100',
                'is_default' => 'boolean'
            ]);

            // Properly handle the checkbox value - if not present, set to false
            $validated['is_default'] = $request->input('is_default', false);

            // If this is set as default, unset other defaults
            if ($validated['is_default']) {
                UserAddress::where('user_id', auth()->id())
                    ->where('id', '!=', $address->id)
                    ->update(['is_default' => false]);
            }

            $address->update($validated);

            // Check if this is a JSON request (for AJAX)
            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Address updated successfully!']);
            }

            return back()->with('success', 'Address updated successfully!');
        } catch (\Exception $e) {
            // Check if this is a JSON request (for AJAX)
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Failed to update address: ' . $e->getMessage()]);
            }
            return back()->with('error', 'Failed to update address: ' . $e->getMessage())->withInput();
        }
    }

    public function deleteAddress(UserAddress $address)
    {
        // Ensure the address belongs to the authenticated user
        if ($address->user_id !== auth()->id()) {
            abort(403);
        }

        try {
            $address->delete();
            return back()->with('success', 'Address deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete address: ' . $e->getMessage());
        }
    }

    public function editAddress(UserAddress $address)
    {
        // Ensure the address belongs to the authenticated user
        if ($address->user_id !== auth()->id()) {
            abort(403);
        }

        return response()->json($address);
    }

    public function setDefaultAddress(UserAddress $address)
    {
        // Ensure the address belongs to the authenticated user
        if ($address->user_id !== auth()->id()) {
            abort(403);
        }

        try {
            // Use the makeDefault method from the UserAddress model
            $address->makeDefault();

            // Check if this is a JSON request (for AJAX)
            if (request()->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Address set as default successfully!']);
            }

            return back()->with('success', 'Address set as default successfully!');
        } catch (\Exception $e) {
            // Check if this is a JSON request (for AJAX)
            if (request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Failed to set address as default: ' . $e->getMessage()]);
            }
            return back()->with('error', 'Failed to set address as default: ' . $e->getMessage());
        }
    }

    public function profile()    {
        return view('account.profile');
    }

    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . auth()->id(),
            'phone' => 'nullable|string|max:20',
        ]);
        
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        if (isset($validated['phone'])) {
            $user->phone = $validated['phone'];
        }
        $user->save();

        return back()->with('success', 'Profile updated successfully!');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Check if current password is correct
        if (!Hash::check($validated['current_password'], auth()->user()->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        // Update password
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $user->password = Hash::make($validated['password']);
        $user->save();

        return back()->with('success', 'Password updated successfully!');
    }

    // Language switcher method
    public function switchLanguage($locale)
    {
        // Validate locale
        if (in_array($locale, ['en', 'ru', 'uz'])) {
            Session::put('locale', $locale);
            App::setLocale($locale);
        }

        // Redirect back to the previous page
        return redirect()->back();
    }

    // Notifications methods
    public function notifications()
    {
        $notifications = auth()->user()->notifications()->latest()->paginate(10);
        return view('account.notifications', compact('notifications'));
    }

    public function markAsRead($notificationId)
    {
        $notification = auth()->user()->notifications()->find($notificationId);
        if ($notification) {
            $notification->markAsRead();
        }
        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications()->update(['read_at' => now()]);
        return back()->with('success', 'All notifications marked as read.');
    }

    // Support Ticket methods
    public function tickets()
    {
        $tickets = auth()->user()->supportTickets()->latest()->paginate(10);
        return view('account.tickets.index', compact('tickets'));
    }

    public function createTicket()
    {
        return view('account.tickets.create');
    }

    public function storeTicket(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'priority' => 'required|in:low,medium,high',
        ]);

        $ticket = new SupportTicket();
        $ticket->user_id = auth()->id();
        $ticket->subject = $validated['subject'];
        $ticket->message = $validated['message'];
        $ticket->priority = $validated['priority'];
        $ticket->status = 'open';
        $ticket->save();

        return redirect()->route('account.tickets.index')->with('success', 'Ticket submitted successfully.');
    }

    public function showTicket(SupportTicket $ticket)
    {
        // Ensure the ticket belongs to the authenticated user
        if ($ticket->user_id !== auth()->id()) {
            abort(403);
        }

        $replies = $ticket->replies()->with('user')->latest()->get();
        return view('account.tickets.show', compact('ticket', 'replies'));
    }

    public function replyTicket(Request $request, SupportTicket $ticket)
    {
        // Ensure the ticket belongs to the authenticated user
        if ($ticket->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'message' => 'required|string',
        ]);

        $reply = new SupportTicketReply();
        $reply->ticket_id = $ticket->id;
        $reply->user_id = auth()->id();
        $reply->message = $validated['message'];
        $reply->is_staff_reply = false;
        $reply->save();

        // Update ticket status to 'customer_reply'
        $ticket->status = 'customer_reply';
        $ticket->save();

        return back()->with('success', 'Reply submitted successfully.');
    }

    // Refund methods
    public function refunds()
    {
        $refunds = auth()->user()->refunds()->with('order')->latest()->paginate(10);
        return view('account.refunds.index', compact('refunds'));
    }

    public function createRefund(Order $order)
    {
        // Ensure the order belongs to the authenticated user
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        // Check if refund already exists for this order
        if ($order->refund) {
            return redirect()->route('account.refunds.index')->with('error', 'A refund request already exists for this order.');
        }

        return view('account.refunds.create', compact('order'));
    }

    public function storeRefund(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'reason' => 'required|string|max:500',
            'amount' => 'required|numeric|min:0',
        ]);

        $order = Order::findOrFail($validated['order_id']);

        // Ensure the order belongs to the authenticated user
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        // Check if refund already exists for this order
        if ($order->refund) {
            return redirect()->route('account.refunds.index')->with('error', 'A refund request already exists for this order.');
        }

        $refund = new Refund();
        $refund->user_id = auth()->id();
        $refund->order_id = $order->id;
        $refund->reason = $validated['reason'];
        $refund->amount = $validated['amount'];
        $refund->status = 'pending';
        $refund->save();

        return redirect()->route('account.refunds.index')->with('success', 'Refund request submitted successfully.');
    }

    public function showRefund(Refund $refund)
    {
        // Ensure the refund belongs to the authenticated user
        if ($refund->user_id !== auth()->id()) {
            abort(403);
        }

        return view('account.refunds.show', compact('refund'));
    }

    // Newsletter subscription methods
    public function newsletterSubscribe(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|unique:newsletter_subscribers,email',
        ]);

        $subscriber = new NewsletterSubscriber();
        $subscriber->email = $validated['email'];
        $subscriber->token = Str::random(32);
        $subscriber->is_active = true;
        $subscriber->save();

        return back()->with('success', 'You have been subscribed to our newsletter successfully.');
    }

    public function newsletterUnsubscribe($token)
    {
        $subscriber = NewsletterSubscriber::where('token', $token)->first();

        if (!$subscriber) {
            return redirect()->route('home')->with('error', 'Invalid unsubscribe link.');
        }

        $subscriber->delete();

        return redirect()->route('home')->with('success', 'You have been unsubscribed from our newsletter.');
    }

    // CMS Page method
    public function page($slug)
    {
        $page = Page::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        return view('page', compact('page'));
    }
}