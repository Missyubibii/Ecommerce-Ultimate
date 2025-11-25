<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\View\Composers\CategoryComposer;
use App\Models\Banner;
use App\Models\Category;    

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
        View::composer(['layouts.app', 'products.index'], CategoryComposer::class);

        View::composer('*', function ($view) {
            // 1. Header Banners
            $headerBanners = Banner::where('position', 'header_top')
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get();

            // 2. Categories cho Menu (Lấy danh mục gốc và con của nó)
            $menuCategories = Category::whereNull('parent_id')
                ->with('children')
                ->orderBy('name')
                ->get();

            $view->with('headerBanners', $headerBanners)
                ->with('menuCategories', $menuCategories);
        });
    }
}
