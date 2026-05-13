<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PublicProductController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ChatController;

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CartController as AdminCartController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\SearchReportController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\AdminChatController;
use App\Http\Controllers\Admin\BrandController;

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| 1. PUBLIC ROUTES (GUEST + USER) 
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

// Search Routes
Route::get('/search', [SearchController::class, 'index'])->name('search.index');
Route::get('/search/suggestions', [SearchController::class, 'suggestions'])->name('search.suggestions');

// Product & Category Routes
Route::get('/category/{slug}', [PublicProductController::class, 'category'])->name('category.show');
Route::get('/product/{slug}', [PublicProductController::class, 'show'])->name('product.show');

// Cart Routes
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/apply-coupon', [CartController::class, 'applyCoupon'])->name('cart.apply-coupon');
Route::post('/cart/update/{id}', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');

// Checkout Routes
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout/place-order', [CheckoutController::class, 'store'])->name('order.place');
Route::get('/checkout/thankyou/{id}', [CheckoutController::class, 'thankyou'])->name('checkout.thankyou');

// AI Chatbot Routes
Route::post('/chat/send', [ChatController::class, 'sendMessage'])->name('chat.send');
Route::get('/chat/history', [ChatController::class, 'getHistory'])->name('chat.history');
Route::delete('/chat/history', [ChatController::class, 'clearHistory'])->name('chat.clear');

/*
|--------------------------------------------------------------------------
| 2. USER ROUTES (CUSTOMER DASHBOARD) 
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard của khách hàng
    Route::get('/home', function () {
        return view('home');
    })->name('dashboard');

    // Quản lý Profile & Address
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('address')->name('address.')->group(function () {
        Route::post('/', [AddressController::class, 'store'])->name('store');
        Route::put('/{address}', [AddressController::class, 'update'])->name('update');
        Route::delete('/{address}', [AddressController::class, 'destroy'])->name('destroy');
        Route::patch('/{address}/default', [AddressController::class, 'setDefault'])->name('set-default');
    });

    // Quản lý Đơn hàng của khách hàng
    Route::prefix('customer/orders')->name('customer.orders.')->group(function () {
        Route::get('/', [\App\Http\Controllers\CustomerOrderController::class, 'index'])->name('index');
        Route::get('/{id}', [\App\Http\Controllers\CustomerOrderController::class, 'show'])->name('show');
        Route::post('/{id}/cancel', [\App\Http\Controllers\CustomerOrderController::class, 'cancel'])->name('cancel');
    });
});

/*
|--------------------------------------------------------------------------
| 2.1 GOOGLE SOCIAL LOGIN ROUTES
|--------------------------------------------------------------------------
*/
Route::get('/auth/google', [App\Http\Controllers\Auth\GoogleAuthController::class, 'redirectToGoogle'])->name('google.login');
Route::get('/auth/google/callback', [App\Http\Controllers\Auth\GoogleAuthController::class, 'handleGoogleCallback']);

/*
|--------------------------------------------------------------------------
| 3. ADMIN ROUTES (BACKEND PANEL) 
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin|manager'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Admin Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::post('/dashboard/refresh', [AdminDashboardController::class, 'refresh'])->name('dashboard.refresh');

        // Reset User Password
        Route::middleware('role:admin')->group(function() {
            Route::patch('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.resetPassword');
            Route::post('users/bulk-delete', [UserController::class, 'bulkDelete'])->name('users.bulkDelete');
            Route::resource('users', UserController::class);
        });

        // Category Management
        Route::resource('categories', CategoryController::class);

        // Product Management
        Route::post('products/bulk-delete', [ProductController::class, 'bulkDestroy'])->name('products.bulkDelete');
        Route::resource('products', ProductController::class);
        Route::patch('products/{product}/reorder-images', [ProductController::class, 'reorderImages'])->name('products.reorderImages');

        // Brand Management
        Route::resource('brands', BrandController::class);

        // Cart Management
        Route::get('/carts', [AdminCartController::class, 'index'])->name('carts.index');
        Route::get('/carts/detail', [AdminCartController::class, 'show'])->name('carts.show');
        Route::delete('/carts/clear', [AdminCartController::class, 'destroy'])->name('carts.destroy');

        // Order Management
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
        Route::put('/orders/{id}', [OrderController::class, 'update'])->name('orders.update');
        Route::post('/orders/{id}/approve', [OrderController::class, 'approve'])->name('orders.approve');
        Route::post('/orders/{id}/ship', [OrderController::class, 'ship'])->name('orders.ship');
        Route::post('/orders/{id}/complete', [OrderController::class, 'complete'])->name('orders.complete');
        Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');

        // Payment Management
        Route::put('/payments/{id}', [OrderController::class, 'updatePayment'])->name('orders.update_payment');

        // Shipment Management
        Route::put('/shipments/{id}', [OrderController::class, 'updateShipment'])->name('orders.update_shipment');

        // Coupon Management
        Route::resource('coupons', CouponController::class);

        // Settings Management
        Route::middleware('role:admin')->group(function() {
            Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
            Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
        });

        // Activity Logs Management
        Route::get('/activity_logs', [ActivityLogController::class, 'index'])->name('activity_logs.index')->middleware('role:admin');

        // Banner Management
        Route::resource('banners', BannerController::class);

        // Search Reports Management
        // Search Reports Management
        Route::get('/search-reports', [SearchReportController::class, 'index'])->name('search_reports.index')->middleware('role:admin');

        // Chat History
        Route::get('/chat-history', [AdminChatController::class, 'index'])->name('chat.index');
        Route::get('/chat-history/{id}', [AdminChatController::class, 'show'])->name('chat.show');
    });

require __DIR__ . '/auth.php';

// Route test email
Route::get('/test-email', function () {
    try {
        \Illuminate\Support\Facades\Mail::raw('Đây là email test từ Laravel Ecommerce Ultimate! Gửi lúc: ' . now(), function ($message) {
            $message->to('d.khanh9c@gmail.com')
                    ->subject('Test SMTP - Ecommerce Ultimate');
        });
        
        return '✅ Email đã được gửi thành công! Vui lòng kiểm tra hộp thư.';
    } catch (\Exception $e) {
        return '❌ LỖI: ' . $e->getMessage();
    }
});
