<?php
// routes/web.php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SuperAdmin;
use App\Http\Controllers\Vendor;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/category/{slug}', [HomeController::class, 'category'])->name('category.show');
Route::get('/product/{slug}', [HomeController::class, 'product'])->name('product.show');
Route::get('/search', [HomeController::class, 'search'])->name('search');

require __DIR__ . '/auth.php';

// Customer Routes (Authenticated)
Route::middleware(['auth', 'customer'])->group(function () {
    // Cart
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
    Route::put('/cart/{cart}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{cart}', [CartController::class, 'destroy'])->name('cart.destroy');
    Route::get('/cart/count', [CartController::class, 'count'])->name('cart.count');

    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
    Route::delete('/wishlist/{wishlist}', [WishlistController::class, 'destroy'])->name('wishlist.destroy');
    Route::get('/wishlist/count', [WishlistController::class, 'count'])->name('wishlist.count');


    // Checkout & Orders
    Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::put('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');


    // Reviews
    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
});


// Super Admin Routes
Route::prefix('super-admin')->name('super-admin.')->middleware(['auth', 'super_admin'])->group(function () {
    Route::get('/dashboard', [SuperAdmin\DashboardController::class, 'index'])->name('dashboard');

    // Categories
    Route::resource('categories', SuperAdmin\CategoryController::class);

    // Vendors
    Route::get('/vendors', [SuperAdmin\VendorController::class, 'index'])->name('vendors.index');
    Route::get('/vendors/{vendor}', [SuperAdmin\VendorController::class, 'show'])->name('vendors.show');
    Route::post('/vendors/{vendor}/toggle-status', [SuperAdmin\VendorController::class, 'toggleStatus'])->name('vendors.toggle-status');
    Route::delete('/vendors/{vendor}', [SuperAdmin\VendorController::class, 'destroy'])->name('vendors.destroy');

    // Products
    Route::get('/products', [SuperAdmin\ProductController::class, 'index'])->name('products.index');
    Route::get('/products/{product}', [SuperAdmin\ProductController::class, 'show'])->name('products.show');
    Route::post('/products/{product}/toggle-status', [SuperAdmin\ProductController::class, 'toggleStatus'])->name('products.toggle-status');
    Route::post('/products/{product}/toggle-featured', [SuperAdmin\ProductController::class, 'toggleFeatured'])->name('products.toggle-featured');
    Route::delete('/products/{product}', [SuperAdmin\ProductController::class, 'destroy'])->name('products.destroy');

    // Orders
    Route::get('/orders', [SuperAdmin\OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [SuperAdmin\OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/update-status', [SuperAdmin\OrderController::class, 'updateStatus'])->name('orders.update-status');

    // Reviews
    Route::get('/reviews', [SuperAdmin\ReviewController::class, 'index'])->name('reviews.index');
    Route::post('/reviews/{review}/approve', [SuperAdmin\ReviewController::class, 'approve'])->name('reviews.approve');
    Route::delete('/reviews/{review}', [SuperAdmin\ReviewController::class, 'destroy'])->name('reviews.destroy');
});

// Vendor Routes
Route::prefix('vendor')->name('vendor.')->middleware(['auth', 'vendor'])->group(function () {
    Route::get('/dashboard', [Vendor\DashboardController::class, 'index'])->name('dashboard');

    // Products
    Route::resource('products', Vendor\ProductController::class);
    Route::delete('/products/{image}/delete-image', [Vendor\ProductController::class, 'deleteImage'])->name('products.delete-image');

    // Orders
    Route::get('/orders', [Vendor\OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [Vendor\OrderController::class, 'show'])->name('orders.show');
    Route::put('/orders/{orderItem}/update-status', [Vendor\OrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::put('/products/{product}/toggle-status', [Vendor\ProductController::class, 'toggleStatus'])->name('products.toggle-status');
});

// Redirect to appropriate dashboard based on role
Route::get('/dashboard', function () {
    if (auth()->user()->isSuperAdmin()) {
        return redirect()->route('super-admin.dashboard');
    } elseif (auth()->user()->isVendor()) {
        return redirect()->route('vendor.dashboard');
    } else {
        return redirect()->route('home');
    }
})->middleware('auth')->name('dashboard');
