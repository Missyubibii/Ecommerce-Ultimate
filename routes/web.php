<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CartController as AdminCartController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| 1. PUBLIC ROUTES (GUEST + USER) - MODULE L
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

// Cart Routes
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update/{id}', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');

/*
|--------------------------------------------------------------------------
| 2. USER ROUTES (CUSTOMER DASHBOARD) - MODULE A
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard của khách hàng
    Route::get('/home', function () {
        return view('home');
    })->name('home');

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

    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/place-order', [CheckoutController::class, 'store'])->name('order.place');
    Route::get('/checkout/thankyou/{id}', [CheckoutController::class, 'thankyou'])->name('checkout.thankyou');
});

/*
|--------------------------------------------------------------------------
| 3. ADMIN ROUTES (BACKEND PANEL) - MODULE J
|--------------------------------------------------------------------------
*/
// Prefix URL là /admin, Name là admin., Middleware check role admin
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Admin Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Reset User Password
        Route::patch('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.resetPassword');
        // Bulk Delete Users
        Route::post('users/bulk-delete', [UserController::class, 'bulkDelete'])->name('users.bulkDelete');
        // User Management
        Route::resource('users', UserController::class);

        // Category Management
        Route::resource('categories', CategoryController::class);

        // Product Management
        Route::resource('products', ProductController::class);
        Route::patch('products/{product}/reorder-images', [ProductController::class, 'reorderImages'])->name('products.reorderImages');

        // Cart Management
        Route::get('/carts', [AdminCartController::class, 'index'])->name('carts.index');
        Route::get('/carts/detail', [AdminCartController::class, 'show'])->name('carts.show');
        Route::delete('/carts/clear', [AdminCartController::class, 'destroy'])->name('carts.destroy');

        // Order Management
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
        Route::put('/orders/{id}', [OrderController::class, 'update'])->name('orders.update');

        // Payment Management
        Route::put('/payments/{id}', [OrderController::class, 'updatePayment'])->name('orders.update_payment');

        // Shipment Management
        Route::put('/shipments/{id}', [OrderController::class, 'updateShipment'])->name('orders.update_shipment');

        // Coupon Management
        Route::resource('coupons', CouponController::class);

        // Settings Management
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');

        // Activity Logs Management
        Route::get('/activity_logs', [ActivityLogController::class, 'index'])->name('activity_logs.index');

        // Banner Management
        Route::resource('banners', BannerController::class);
    });

require __DIR__ . '/auth.php';
