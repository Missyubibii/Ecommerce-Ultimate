<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\ProductService;
use App\Services\CategoryService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

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

        $debug = ['module' => 'Product', 'action' => 'List', 'count' => $products->total()];

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $products, 'debug' => $debug]);
        }

        return view('admin.products.index', compact('products', 'categories', 'lowStockCount', 'allProductIds'))
            ->with('server_debug', $debug);
    }

    public function create()
    {
        $categories = $this->categoryService->getAllFlat();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $this->validateProduct($request);

        $data['is_featured'] = $request->boolean('is_featured');
        $data['special_offer'] = $request->boolean('special_offer');
        $data['online_only'] = $request->boolean('online_only');

        $product = $this->productService->create($data);

        $debug = ['module' => 'Product', 'action' => 'Create', 'id' => $product->id];

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'id' => $product->id, 'debug' => $debug]);
        }

        return redirect()->route('admin.products.index')
            ->with('status', 'Product created!')
            ->with('server_debug', $debug);
    }

    public function edit(Product $product)
    {
        $categories = $this->categoryService->getAllFlat();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        // 1. Validate cơ bản
        $data = $this->validateProduct($request, $product->id);

        // 2. Lấy thêm các trường dữ liệu phục vụ xử lý ảnh phức tạp (JSON từ JS gửi lên)
        $data['images_data'] = $request->input('images_data');
        $data['deleted_image_ids'] = $request->input('deleted_image_ids');

        // 3. Map boolean
        $data['is_featured'] = $request->boolean('is_featured');
        $data['special_offer'] = $request->boolean('special_offer');
        $data['online_only'] = $request->boolean('online_only');

        $updated = $this->productService->update($product, $data);

        $debug = ['module' => 'Product', 'action' => 'Update', 'id' => $product->id];

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $updated, 'debug' => $debug]);
        }

        return redirect()->route('admin.products.index')
            ->with('status', 'Product updated!')
            ->with('server_debug', $debug);
    }

    public function show(Product $product)
    {
        $product->load('category', 'images');
        $debug = ['module' => 'Product', 'action' => 'Show', 'id' => $product->id];

        return view('admin.products.show', compact('product'))->with('server_debug', $debug);
    }

    public function destroy(Request $request, Product $product)
    {
        $this->productService->delete($product);
        $debug = ['module' => 'Product', 'action' => 'Delete', 'id' => $product->id];

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'debug' => $debug]);
        }

        return redirect()->route('admin.products.index')
            ->with('status', 'Product deleted!')
            ->with('server_debug', $debug);
    }

    public function reorderImages(Request $request, Product $product)
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'exists:product_images,id']);
        $this->productService->reorderImages($product, $request->ids);

        return response()->json(['success' => true, 'message' => 'Images reordered successfully.']);
    }

    private function validateProduct(Request $request, $id = null)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'sku' => 'nullable|string|unique:products,sku,' . $id,
            'short_description' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'specifications' => 'nullable|string',
            'stock_locations' => 'nullable|string',
            'market_price' => 'nullable|numeric|min:0',
            'warranty' => 'nullable|string|max:255',
            'colors' => 'nullable|string',
            'images_data' => 'nullable|string',
            'deleted_image_ids' => 'nullable|array',
        ];

        // Logic Gallery:
        // - Create: Bắt buộc upload
        // - Edit: Không bắt buộc upload, nhưng nếu xóa hết ảnh cũ thì phải upload mới (logic này có thể handle ở JS hoặc service).
        if (!$id) {
            $rules['gallery'] = 'required|array|min:1';
            $rules['gallery.*'] = 'image|max:2048';
        } else {
            $rules['gallery'] = 'nullable|array';
            $rules['gallery.*'] = 'image|max:2048';
        }

        return $request->validate($rules);
    }
}
