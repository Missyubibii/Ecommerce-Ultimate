<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| 1. PUBLIC ROUTES (GUEST + USER) - MODULE L
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('welcome'); // Trang chủ Frontend (Homepage)
})->name('home');

/*
|--------------------------------------------------------------------------
| 2. USER ROUTES (CUSTOMER DASHBOARD) - MODULE A
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard của khách hàng
    Route::get('/dashboard', function () {
        return view('dashboard');
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
    });

require __DIR__.'/auth.php';
