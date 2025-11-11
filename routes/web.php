<?php
// routes/web.php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SuperAdmin;
use App\Http\Controllers\Vendor;
use App\Models\SubCategory;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/category/{slug}', [HomeController::class, 'category'])->name('category.show');
Route::get('/product/{slug}', [HomeController::class, 'product'])->name('product.show');
Route::get('/search', [HomeController::class, 'search'])->name('search');
Route::get('/brands', [HomeController::class, 'brands'])->name('brands.index');
Route::get('/brand/{slug}', [HomeController::class, 'brand'])->name('brand.show');

// About pages
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::post('/contact', [HomeController::class, 'contactSubmit'])->name('contact.submit');

// API endpoint to get sub-categories by category (for frontend AJAX)
Route::get('/api/sub-categories/by-category/{categoryId}', function ($categoryId) {
    $subCategories = SubCategory::where('category_id', $categoryId)
        ->where('is_active', true)
        ->orderBy('order')
        ->get(['id', 'name', 'slug']);

    return response()->json($subCategories);
})->name('api.sub-categories.by-category');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

require __DIR__ . '/auth.php';

/*
|--------------------------------------------------------------------------
| Customer Routes (Authenticated)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {

    // Cart Routes
    Route::prefix('cart')->name('cart.')->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('index');
        Route::post('/add', [CartController::class, 'store'])->name('store');
        Route::post('/update/{cart}', [CartController::class, 'update'])->name('update');
        Route::delete('/{cart}', [CartController::class, 'destroy'])->name('destroy');
        Route::get('/count', [CartController::class, 'count'])->name('count');
        Route::post('/clear', [CartController::class, 'clear'])->name('clear');
    });

    // Wishlist Routes
    Route::prefix('wishlist')->name('wishlist.')->group(function () {
        Route::get('/', [WishlistController::class, 'index'])->name('index');
        Route::post('/toggle', [WishlistController::class, 'toggle'])->name('toggle');
        Route::delete('/{wishlist}', [WishlistController::class, 'destroy'])->name('destroy');
        Route::get('/count', [WishlistController::class, 'count'])->name('count');
        Route::post('/clear', [WishlistController::class, 'clear'])->name('clear');
    });

    // Checkout & Orders Routes
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout');
        Route::post('/store', [OrderController::class, 'store'])->name('store');
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/{order}', [OrderController::class, 'show'])->name('show');
        Route::post('/{order}/cancel', [OrderController::class, 'cancel'])->name('cancel');
        Route::get('/{order}/invoice', [OrderController::class, 'invoice'])->name('invoice');
    });

    // Reviews Routes
    Route::prefix('reviews')->name('reviews.')->group(function () {
        Route::post('/', [ReviewController::class, 'store'])->name('store');
        Route::put('/{review}', [ReviewController::class, 'update'])->name('update');
        Route::delete('/{review}', [ReviewController::class, 'destroy'])->name('destroy');
    });

    // User Profile & Account
    Route::prefix('account')->name('account.')->group(function () {
        Route::get('/profile', [HomeController::class, 'profile'])->name('profile');
        Route::put('/profile', [HomeController::class, 'updateProfile'])->name('profile.update');
        Route::get('/addresses', [HomeController::class, 'addresses'])->name('addresses');
        Route::post('/addresses', [HomeController::class, 'storeAddress'])->name('addresses.store');
        Route::put('/addresses/{address}', [HomeController::class, 'updateAddress'])->name('addresses.update');
        Route::delete('/addresses/{address}', [HomeController::class, 'deleteAddress'])->name('addresses.destroy');
    });
});

/*
|--------------------------------------------------------------------------
| Super Admin Routes
|--------------------------------------------------------------------------
*/

Route::prefix('super-admin')->name('super-admin.')->middleware(['auth', 'super_admin'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [SuperAdmin\DashboardController::class, 'index'])->name('dashboard');

    // Categories Management
    Route::resource('categories', SuperAdmin\CategoryController::class);

    // Sub-Categories Management
    Route::resource('sub-categories', SuperAdmin\SubCategoryController::class);
    Route::get('/sub-categories/by-category/{categoryId}', [SuperAdmin\SubCategoryController::class, 'getByCategory'])
        ->name('sub-categories.by-category');

    // Brands Management
    Route::resource('brands', SuperAdmin\BrandController::class);

    // Vendors Management
    Route::prefix('vendors')->name('vendors.')->group(function () {
        Route::get('/', [SuperAdmin\VendorController::class, 'index'])->name('index');
        Route::get('/{vendor}', [SuperAdmin\VendorController::class, 'show'])->name('show');
        Route::post('/{vendor}/toggle-status', [SuperAdmin\VendorController::class, 'toggleStatus'])->name('toggle-status');
        Route::post('/{vendor}/approve', [SuperAdmin\VendorController::class, 'approve'])->name('approve');
        Route::delete('/{vendor}', [SuperAdmin\VendorController::class, 'destroy'])->name('destroy');
    });

    // Products Management
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/', [SuperAdmin\ProductController::class, 'index'])->name('index');
        Route::get('/{product}', [SuperAdmin\ProductController::class, 'show'])->name('show');
        Route::post('/{product}/toggle-status', [SuperAdmin\ProductController::class, 'toggleStatus'])->name('toggle-status');
        Route::post('/{product}/toggle-featured', [SuperAdmin\ProductController::class, 'toggleFeatured'])->name('toggle-featured');
        Route::delete('/{product}', [SuperAdmin\ProductController::class, 'destroy'])->name('destroy');
    });

    // Orders Management
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [SuperAdmin\OrderController::class, 'index'])->name('index');
        Route::get('/{order}', [SuperAdmin\OrderController::class, 'show'])->name('show');
        Route::post('/{order}/update-status', [SuperAdmin\OrderController::class, 'updateStatus'])->name('update-status');
        Route::post('/{order}/update-payment', [SuperAdmin\OrderController::class, 'updatePaymentStatus'])->name('update-payment');
    });

    // Reviews Management
    Route::prefix('reviews')->name('reviews.')->group(function () {
        Route::get('/', [SuperAdmin\ReviewController::class, 'index'])->name('index');
        Route::post('/{review}/approve', [SuperAdmin\ReviewController::class, 'approve'])->name('approve');
        Route::post('/{review}/reject', [SuperAdmin\ReviewController::class, 'reject'])->name('reject');
        Route::delete('/{review}', [SuperAdmin\ReviewController::class, 'destroy'])->name('destroy');
    });

    // Coupons Management
    Route::resource('coupons', SuperAdmin\CouponController::class);

    // Banners Management
    Route::resource('banners', SuperAdmin\BannerController::class);

    // Flash Sales Management
    Route::resource('flash-sales', SuperAdmin\FlashSaleController::class);
    Route::post('/flash-sales/{flashSale}/add-product', [SuperAdmin\FlashSaleController::class, 'addProduct'])->name('flash-sales.add-product');
    Route::delete('/flash-sales/{flashSale}/remove-product/{product}', [SuperAdmin\FlashSaleController::class, 'removeProduct'])->name('flash-sales.remove-product');

    // Support Tickets Management
    Route::prefix('tickets')->name('tickets.')->group(function () {
        Route::get('/', [SuperAdmin\SupportTicketController::class, 'index'])->name('index');
        Route::get('/{ticket}', [SuperAdmin\SupportTicketController::class, 'show'])->name('show');
        Route::post('/{ticket}/reply', [SuperAdmin\SupportTicketController::class, 'reply'])->name('reply');
        Route::post('/{ticket}/assign', [SuperAdmin\SupportTicketController::class, 'assign'])->name('assign');
        Route::post('/{ticket}/resolve', [SuperAdmin\SupportTicketController::class, 'resolve'])->name('resolve');
        Route::post('/{ticket}/close', [SuperAdmin\SupportTicketController::class, 'close'])->name('close');
    });

    // Refunds Management
    Route::prefix('refunds')->name('refunds.')->group(function () {
        Route::get('/', [SuperAdmin\RefundController::class, 'index'])->name('index');
        Route::get('/{refund}', [SuperAdmin\RefundController::class, 'show'])->name('show');
        Route::post('/{refund}/approve', [SuperAdmin\RefundController::class, 'approve'])->name('approve');
        Route::post('/{refund}/reject', [SuperAdmin\RefundController::class, 'reject'])->name('reject');
        Route::post('/{refund}/complete', [SuperAdmin\RefundController::class, 'complete'])->name('complete');
    });

    // Vendor Payouts Management
    Route::prefix('payouts')->name('payouts.')->group(function () {
        Route::get('/', [SuperAdmin\PayoutController::class, 'index'])->name('index');
        Route::get('/{payout}', [SuperAdmin\PayoutController::class, 'show'])->name('show');
        Route::post('/generate', [SuperAdmin\PayoutController::class, 'generate'])->name('generate');
        Route::post('/{payout}/approve', [SuperAdmin\PayoutController::class, 'approve'])->name('approve');
        Route::post('/{payout}/complete', [SuperAdmin\PayoutController::class, 'complete'])->name('complete');
    });

    // Settings Management
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SuperAdmin\SettingController::class, 'index'])->name('index');
        Route::post('/', [SuperAdmin\SettingController::class, 'update'])->name('update');
    });

    // Newsletter Subscribers
    Route::prefix('newsletter')->name('newsletter.')->group(function () {
        Route::get('/', [SuperAdmin\NewsletterController::class, 'index'])->name('index');
        Route::post('/send', [SuperAdmin\NewsletterController::class, 'send'])->name('send');
        Route::delete('/{subscriber}', [SuperAdmin\NewsletterController::class, 'destroy'])->name('destroy');
    });

    // Pages Management (CMS)
    Route::resource('pages', SuperAdmin\PageController::class);

    // Shipping Methods
    Route::resource('shipping-methods', SuperAdmin\ShippingMethodController::class);

    // Taxes
    Route::resource('taxes', SuperAdmin\TaxController::class);

    // Users Management
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [SuperAdmin\UserController::class, 'index'])->name('index');
        Route::get('/{user}', [SuperAdmin\UserController::class, 'show'])->name('show');
        Route::post('/{user}/toggle-status', [SuperAdmin\UserController::class, 'toggleStatus'])->name('toggle-status');
        Route::delete('/{user}', [SuperAdmin\UserController::class, 'destroy'])->name('destroy');
    });

    // Activity Logs
    Route::get('/activity-logs', [SuperAdmin\ActivityLogController::class, 'index'])->name('activity-logs.index');

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/sales', [SuperAdmin\ReportController::class, 'sales'])->name('sales');
        Route::get('/products', [SuperAdmin\ReportController::class, 'products'])->name('products');
        Route::get('/vendors', [SuperAdmin\ReportController::class, 'vendors'])->name('vendors');
        Route::get('/customers', [SuperAdmin\ReportController::class, 'customers'])->name('customers');
    });
});

/*
|--------------------------------------------------------------------------
| Vendor Routes
|--------------------------------------------------------------------------
*/

Route::prefix('vendor')->name('vendor.')->middleware(['auth', 'vendor'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [Vendor\DashboardController::class, 'index'])->name('dashboard');

    // Products Management
    Route::resource('products', Vendor\ProductController::class);
    Route::post('/products/{product}/toggle-status', [Vendor\ProductController::class, 'toggleStatus'])->name('products.toggle-status');
    Route::delete('/products/{product}/images/{image}', [Vendor\ProductController::class, 'deleteImage'])->name('products.delete-image');
    Route::post('/products/{product}/images/reorder', [Vendor\ProductController::class, 'reorderImages'])->name('products.reorder-images');

    // Product Variants
    Route::prefix('products/{product}/variants')->name('products.variants.')->group(function () {
        Route::post('/', [Vendor\ProductVariantController::class, 'store'])->name('store');
        Route::put('/{variant}', [Vendor\ProductVariantController::class, 'update'])->name('update');
        Route::delete('/{variant}', [Vendor\ProductVariantController::class, 'destroy'])->name('destroy');
    });

    // Orders Management
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [Vendor\OrderController::class, 'index'])->name('index');
        Route::get('/{order}', [Vendor\OrderController::class, 'show'])->name('show');
        Route::post('/{orderItem}/update-status', [Vendor\OrderController::class, 'updateStatus'])->name('update-status');
        Route::post('/{order}/ship', [Vendor\OrderController::class, 'ship'])->name('ship');
    });

    // Earnings & Payouts
    Route::prefix('earnings')->name('earnings.')->group(function () {
        Route::get('/', [Vendor\EarningsController::class, 'index'])->name('index');
        Route::get('/payouts', [Vendor\EarningsController::class, 'payouts'])->name('payouts');
        Route::post('/payouts/request', [Vendor\EarningsController::class, 'requestPayout'])->name('payouts.request');
    });

    // Bank Accounts
    Route::prefix('bank-accounts')->name('bank-accounts.')->group(function () {
        Route::get('/', [Vendor\BankAccountController::class, 'index'])->name('index');
        Route::post('/', [Vendor\BankAccountController::class, 'store'])->name('store');
        Route::put('/{account}', [Vendor\BankAccountController::class, 'update'])->name('update');
        Route::delete('/{account}', [Vendor\BankAccountController::class, 'destroy'])->name('destroy');
        Route::post('/{account}/make-primary', [Vendor\BankAccountController::class, 'makePrimary'])->name('make-primary');
    });

    // Store Settings
    Route::prefix('store')->name('store.')->group(function () {
        Route::get('/settings', [Vendor\StoreController::class, 'settings'])->name('settings');
        Route::put('/settings', [Vendor\StoreController::class, 'updateSettings'])->name('settings.update');
    });

    // Messages/Conversations
    Route::prefix('messages')->name('messages.')->group(function () {
        Route::get('/', [Vendor\MessageController::class, 'index'])->name('index');
        Route::get('/{conversation}', [Vendor\MessageController::class, 'show'])->name('show');
        Route::post('/{conversation}', [Vendor\MessageController::class, 'reply'])->name('reply');
    });

    // Reviews (Vendor can respond)
    Route::prefix('reviews')->name('reviews.')->group(function () {
        Route::get('/', [Vendor\ReviewController::class, 'index'])->name('index');
        Route::post('/{review}/respond', [Vendor\ReviewController::class, 'respond'])->name('respond');
    });

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/sales', [Vendor\ReportController::class, 'sales'])->name('sales');
        Route::get('/products', [Vendor\ReportController::class, 'products'])->name('products');
    });
});

/*
|--------------------------------------------------------------------------
| Common Authenticated Routes (All Roles)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [HomeController::class, 'notifications'])->name('index');
        Route::post('/{notification}/read', [HomeController::class, 'markAsRead'])->name('read');
        Route::post('/read-all', [HomeController::class, 'markAllAsRead'])->name('read-all');
    });

    // Support Tickets (Customer)
    Route::prefix('tickets')->name('tickets.')->group(function () {
        Route::get('/', [HomeController::class, 'tickets'])->name('index');
        Route::get('/create', [HomeController::class, 'createTicket'])->name('create');
        Route::post('/', [HomeController::class, 'storeTicket'])->name('store');
        Route::get('/{ticket}', [HomeController::class, 'showTicket'])->name('show');
        Route::post('/{ticket}/reply', [HomeController::class, 'replyTicket'])->name('reply');
    });

    // Refund Requests (Customer)
    Route::prefix('refunds')->name('refunds.')->group(function () {
        Route::get('/', [HomeController::class, 'refunds'])->name('index');
        Route::get('/create/{order}', [HomeController::class, 'createRefund'])->name('create');
        Route::post('/', [HomeController::class, 'storeRefund'])->name('store');
        Route::get('/{refund}', [HomeController::class, 'showRefund'])->name('show');
    });
});

/*
|--------------------------------------------------------------------------
| Dashboard Redirect Route
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', function () {
    if (auth()->user()->isSuperAdmin()) {
        return redirect()->route('super-admin.dashboard');
    } elseif (auth()->user()->isVendor()) {
        return redirect()->route('vendor.dashboard');
    } else {
        return redirect()->route('home');
    }
})->middleware('auth')->name('dashboard');

/*
|--------------------------------------------------------------------------
| Newsletter Subscription (Public)
|--------------------------------------------------------------------------
*/

Route::post('/newsletter/subscribe', [HomeController::class, 'newsletterSubscribe'])->name('newsletter.subscribe');
Route::get('/newsletter/unsubscribe/{token}', [HomeController::class, 'newsletterUnsubscribe'])->name('newsletter.unsubscribe');

/*
|--------------------------------------------------------------------------
| CMS Pages (Public)
|--------------------------------------------------------------------------
*/

Route::get('/page/{slug}', [HomeController::class, 'page'])->name('page.show');
