<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\ProductService;
use App\Services\CategoryService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller implements HasMiddleware
{
    protected $productService;
    protected $categoryService;

    public function __construct(ProductService $productService, CategoryService $categoryService)
    {
        $this->productService = $productService;
        $this->categoryService = $categoryService;
    }

    public static function middleware(): array
    {
        return ['auth', new Middleware('role:admin')];
    }

    public function index(Request $request)
    {
        $filters = $request->all();
        $products = $this->productService->listing($filters);
        $categories = $this->categoryService->getAllFlat();
        $lowStockCount = $this->productService->countLowStock();
        $allProductIds = $this->productService->getAllIds($filters);

        $debug = [
            'module' => 'Product',
            'action' => 'List',
            'filters' => $filters,
            'count' => $products->total()
        ];

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $products, 'debug' => $debug]);
        }

        return view('admin.products.index', [
            'products' => $products,
            'categories' => $categories,
            'server_debug' => $debug,
            'lowStockCount' => $lowStockCount,
            'allProductIds' => $allProductIds,
        ]);
    }

    public function create()
    {
        $categories = $this->categoryService->getAllFlat();
        // $brands = DB::table('brands')->get();
        return view('admin.products.create', ['categories' => $categories]);
    }

    public function store(Request $request)
    {
        // 1. Validation (Bao gồm các trường JSON string)
        $request->validate([
            'name' => 'required|string|max:255',
            'category_ids' => 'required|array',
            'category_ids.*' => 'exists:categories,id', // Multi-select
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'specifications' => 'nullable|string',
            'stock_locations' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'gallery.*' => 'image|max:2048',
            'brand_id' => 'nullable|exists:brands,id',
        ]);

        $data = $request->all();
        $categoryIds = $request->category_ids ?? [];

        // 2. Xử lý JSON Strings (Specifications)
        if ($request->has('specifications')) {
            $specs = json_decode($request->specifications, true);
            $data['metadata'] = ['specifications' => $specs];
            unset($data['specifications']);
        }

        // 3. Xử lý JSON Strings (Stock Locations)
        if ($request->has('stock_locations')) {
            $data['stock_locations'] = json_decode($request->stock_locations, true);
        }

        // 4. Xử lý các trường boolean (Gán giá trị boolean)
        $data['is_active'] = $request->boolean('is_active');
        $data['is_featured'] = $request->boolean('is_featured');
        $data['special_offer'] = $request->boolean('special_offer');
        $data['online_only'] = $request->boolean('online_only');


        // 5. Gọi Service
        $product = $this->productService->create($data, $categoryIds);

        $debug = ['module' => 'Product', 'action' => 'Create', 'id' => $product->id];

        // Trả JSON vì Frontend dùng AJAX
        return response()->json(['success' => true, 'id' => $product->id, 'debug' => $debug]);
    }

    public function edit(Product $product)
    {
        $categories = $this->categoryService->getAllFlat();
        // $brands = DB::table('brands')->get();
        $product->load('categories');

        return view('admin.products.edit', [
            'product' => $product,
            'categories' => $categories,
            // 'brands' => $brands
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'category_ids.*' => 'exists:categories,id',
            'price' => 'required|numeric|min:0',
            'sku' => 'required|string|unique:products,sku,' . $product->id,
            'image' => 'nullable|image|max:2048',
            'short_description' => 'nullable|string|max:500',
            'specs' => 'nullable|array',
            'specs.*.key' => 'required_with:specs.*.value|string',
            'specs.*.value' => 'required_with:specs.*.key|string',
        ]);

        // 1. Lấy dữ liệu cơ bản
        $data = $request->all();
        $categoryIds = $request->category_ids ?? [];

        // 1. Xử lý JSON Strings (Specifications)
        if ($request->has('specifications')) {
            $specs = json_decode($request->specifications, true);
            $data['metadata'] = ['specifications' => $specs];
            unset($data['specifications']);
        }

        // 2. Xử lý Stock Locations
        if ($request->has('stock_locations')) {
            $data['stock_locations'] = json_decode($request->stock_locations, true);
        }

        // 3. Xử lý booleans
        $data['is_active'] = $request->boolean('is_active');
        $data['is_featured'] = $request->boolean('is_featured');
        $data['special_offer'] = $request->boolean('special_offer');
        $data['online_only'] = $request->boolean('online_only');

        // 4. Xử lý Metadata (Tương tự store)
        if ($request->has('specs')) {
            $validSpecs = collect($request->specs)
                ->filter(fn($item) => !empty($item['key']) && !empty($item['value']))
                ->values()
                ->toArray();

            // Merge với metadata cũ nếu cần, hoặc ghi đè
            $currentMeta = $product->metadata ?? [];
            $currentMeta['specs'] = $validSpecs;
            $data['metadata'] = $currentMeta;
        }

        // 3. Gọi Service với $data (FIXED HERE)
        $updated = $this->productService->update($product, $data, $categoryIds);

        $debug = ['module' => 'Product', 'action' => 'Update', 'id' => $product->id];

        if ($request->wantsJson()) return response()->json(['success' => true, 'data' => $updated, 'debug' => $debug]);

        return redirect()->route('admin.products.index')->with('status', 'Product updated!')->with('server_debug', $debug);
    }

    public function show(Product $product)
    {
        $product->load('category', 'images');
        $debug = ['module' => 'Product', 'action' => 'Show', 'id' => $product->id];

        return view('admin.products.show', ['product' => $product, 'server_debug' => $debug]);
    }

    public function destroy(Request $request, Product $product)
    {
        $this->productService->delete($product);
        $debug = ['module' => 'Product', 'action' => 'Delete', 'id' => $product->id];

        if ($request->wantsJson()) return response()->json(['success' => true, 'debug' => $debug]);

        return redirect()->route('admin.products.index')->with('status', 'Product deleted!')->with('server_debug', $debug);
    }

    public function reorderImages(Request $request, Product $product)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:product_images,id'
        ]);

        $this->productService->reorderImages($product, $request->ids);

        return response()->json([
            'success' => true,
            'message' => 'Đã cập nhật thứ tự ảnh và ảnh đại diện mới.'
        ]);
    }
}
