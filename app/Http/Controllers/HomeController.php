<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Models\Banner;

class HomeController extends Controller
{
    public function index()
    {
        // 1. Lấy Banner cho Main Slider từ DB
        $banners = Banner::where('position', 'main_slider')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        // Fallback nếu chưa có banner trong DB để tránh lỗi view
        if ($banners->isEmpty()) {
            $banners = collect([
                (object)[
                    'id' => 1,
                    'title' => 'Chào mừng đến với Ultimate Store',
                    'image_url' => 'https://placehold.co/1200x450/2563eb/ffffff?text=Ultimate+Store',
                    'url' => '#'
                ]
            ]);
        }

        // 2. Lấy Danh mục & Sản phẩm
        $categoriesData = Category::whereNull('parent_id')
            ->with(['products' => function ($q) {
                $q->where('status', 'active')->latest()->take(8);
            }])
            ->get()
            ->map(function ($cat) {
                return [
                    'id' => $cat->id,
                    'name' => $cat->name,
                    // 'url' => route('search.results', ['category_id' => $cat->id]),
                    'is_featured' => 1,
                    'products' => $cat->products
                ];
            });

        $product_category = [$categoriesData];

        return view('home', compact('banners', 'product_category'));
    }

    public function search(Request $request)
    {
        $query = Product::query()->where('status', 'active');

        if ($request->has('q')) {
            $searchTerm = $request->q;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                    ->orWhere('sku', 'like', "%{$searchTerm}%");
            });
        }

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Eager load category để tránh N+1 query
        $products = $query->with('category')->paginate(12)->withQueryString();

        // Lấy danh mục để làm sidebar filter
        $categories = Category::whereNull('parent_id')->with('children')->get();

        return view('welcome', compact('products', 'categories'));
    }
}
