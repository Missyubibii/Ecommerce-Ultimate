<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class CategoryController extends Controller implements HasMiddleware
{
    /**
     * Tạo một instance của CategoryService.
     */
    protected CategoryService $categoryService;

    /**
     * Tạo một instance của CategoryService.
     */
    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * Xác định middleware cần thiết.
     */
    public static function middleware(): array
    {
        return [
            'auth',
            new Middleware('role:admin'),
        ];
    }

    /**
     * Hiển thị danh sách các danh mục.
     */
    public function index(Request $request)
    {
        // Lấy danh sách dạng cây để hiển thị
        $categories = $this->categoryService->getTree();

        // Debug
        $debug = [
            'module' => 'Category',
            'action' => 'ListTree',
            'count_root' => $categories->count()
        ];

        // Hiển thị json
        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $categories, 'debug' => $debug]);
        }

        // Hiển thị view
        return view('admin.categories.index', [
            'categories' => $categories,
            'server_debug' => $debug
        ]);
    }

    /**
     * Hiển thị form tạo danh mục.
     */
    public function create(Request $request)
    {
        // Lấy danh sách tất cả các danh mục để chọn cha
        $parents = $this->categoryService->getAllFlat();

        // Hiển thị view
        return view('admin.categories.create', ['parents' => $parents]);
    }

    /**
     * Lưu danh mục vào database.
     */
    public function store(Request $request)
    {
        // Validate
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'slug' => 'nullable|string|unique:categories,slug'
        ]);

        // Tạo danh mục
        $category = $this->categoryService->create($data);

        // Debug
        $debug = ['module' => 'Category', 'action' => 'Create', 'id' => $category->id];

        // Hiển thị json
        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $category, 'debug' => $debug]);
        }

        return redirect()->route('admin.categories.index')
            ->with('status', 'Category created successfully!')
            ->with('server_debug', $debug);
    }

    /**
     * Hiển thị form chỉnh sửa danh mục.
     */
    public function edit(Category $category)
    {
        // Lấy danh sách tất cả các danh mục để chọn cha
        $parents = $this->categoryService->getAllFlat();

        // Hiển thị view
        return view('admin.categories.edit', ['category' => $category, 'parents' => $parents]);
    }

    /**
     * Cập nhật danh mục trong database.
     */
    public function update(Request $request, Category $category)
    {
        // Validate
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'slug' => 'nullable|string|unique:categories,slug,' . $category->id
        ]);

        // Cập nhật danh mục
        $updated = $this->categoryService->update($category, $data);

        // Debug
        $debug = ['module' => 'Category', 'action' => 'Update', 'changes' => $updated->getChanges()];

        // Hiển thị json
        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $updated, 'debug' => $debug]);
        }

        return redirect()->route('admin.categories.index')
            ->with('status', 'Category updated successfully!')
            ->with('server_debug', $debug);
    }

    /**
     * Xóa danh mục.
     */
    public function destroy(Request $request, Category $category)
    {
        // Xóa danh mục
        $this->categoryService->delete($category);

        // Debug
        $debug = ['module' => 'Category', 'action' => 'Delete', 'id' => $category->id];

        // Hiển thị json
        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'debug' => $debug]);
        }

        return redirect()->route('admin.categories.index')
            ->with('status', 'Category deleted successfully!')
            ->with('server_debug', $debug);
    }
}
