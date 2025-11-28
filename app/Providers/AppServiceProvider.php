<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\View\Composers\CategoryComposer;
use App\Models\Banner;
use App\Models\Category;
use App\Models\CartItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::defaultView('vendor.pagination.custom-tailwind');
        Paginator::defaultSimpleView('vendor.pagination.custom-tailwind');
        View::composer(['layouts.app', 'products.index'], CategoryComposer::class);

        View::composer('*', function ($view) {
            // 1. Header Banners
            $headerBanners = Banner::where('position', 'header_top')
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get();

            // 2. Categories cho Menu
            $menuCategories = Category::whereNull('parent_id')
                ->with('children')
                ->orderBy('name')
                ->get();

            // 3. CART COUNT LOGIC
            $cartCount = 0;
            if (Auth::check()) {
                $cartCount = CartItem::where('user_id', Auth::id())->sum('quantity');
            } else {
                $sessionId = Session::get('cart_session_id');
                if ($sessionId) {
                    $cartCount = CartItem::where('session_id', $sessionId)->sum('quantity');
                }
            }

            $view->with('headerBanners', $headerBanners)
                ->with('menuCategories', $menuCategories)
                ->with('cartCount', $cartCount);
        });
    }
}
