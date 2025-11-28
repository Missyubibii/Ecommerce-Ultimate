<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Models\Banner;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        // --- 1. XỬ LÝ BANNER ---
        $allBanners = Banner::where('is_active', true)->orderBy('sort_order')->get();

        $mainBanners = $allBanners->where('position', 'main_slider')->values();
        if ($mainBanners->isEmpty()) {
            $mainBanners = collect([(object)[
                'id' => 1,
                'title' => 'Chào mừng đến với Ultimate Store',
                'image_url' => 'https://placehold.co/1200x450/2563eb/ffffff?text=Ultimate+Store',
                'url' => '#'
            ]]);
        }

        $ads = $allBanners->where('position', '!=', 'main_slider')
            ->groupBy('position')
            ->map(fn($group) => $group->first());

        // --- 2. XỬ LÝ SẢN PHẨM ---
        if ($request->wantsJson()) {
            $rootCategories = Category::whereNull('parent_id')->with('children')->orderBy('name')->get();

            $sections = $rootCategories->map(function ($cat) {
                $categoryIds = $cat->children->pluck('id')->push($cat->id);

                $products = Product::whereIn('category_id', $categoryIds)
                    ->where('status', 'active')
                    ->latest()
                    ->take(8)
                    ->get()
                    ->map(function ($p) {
                        return [
                            'id' => $p->id,
                            'name' => $p->name,
                            'detail_url' => route('product.show', $p->slug),
                            'image_url' => $p->image_url,
                            'price' => $p->price,
                            'market_price' => $p->market_price, // Giá niêm yết
                            'quantity' => $p->quantity,
                            'warranty' => $p->warranty,
                            'special_offer' => $p->special_offer, // Text ưu đãi
                        ];
                    });

                return [
                    'id' => $cat->id,
                    'name' => $cat->name,
                    'url' => route('category.show', $cat->slug),
                    'products' => $products
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'sections' => $sections
                ]
            ]);
        }

        return view('home', compact('mainBanners', 'ads'));
    }
}
