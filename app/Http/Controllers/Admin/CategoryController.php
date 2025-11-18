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
    protected CategoryService $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public static function middleware(): array
    {
        return [
            'auth',
            new Middleware('role:admin'), 
        ];
    }

    public function index(Request $request)
    {
        // Lấy danh sách dạng cây để hiển thị
        $categories = $this->categoryService->getTree();

        $debug = [
            'module' => 'Category',
            'action' => 'ListTree',
            'count_root' => $categories->count()
        ];

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $categories, 'debug' => $debug]);
        }

        return view('admin.categories.index', [
            'categories' => $categories,
            'server_debug' => $debug
        ]);
    }

    public function create(Request $request)
    {
        $parents = $this->categoryService->getAllFlat();
        return view('admin.categories.create', ['parents' => $parents]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'slug' => 'nullable|string|unique:categories,slug'
        ]);

        $category = $this->categoryService->create($data);

        $debug = ['module' => 'Category', 'action' => 'Create', 'id' => $category->id];

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $category, 'debug' => $debug]);
        }

        return redirect()->route('admin.categories.index')
            ->with('status', 'Category created successfully!')
            ->with('server_debug', $debug);
    }

    public function edit(Category $category)
    {
        $parents = $this->categoryService->getAllFlat();
        return view('admin.categories.edit', ['category' => $category, 'parents' => $parents]);
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'slug' => 'nullable|string|unique:categories,slug,' . $category->id
        ]);

        $updated = $this->categoryService->update($category, $data);

        $debug = ['module' => 'Category', 'action' => 'Update', 'changes' => $updated->getChanges()];

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $updated, 'debug' => $debug]);
        }

        return redirect()->route('admin.categories.index')
            ->with('status', 'Category updated successfully!')
            ->with('server_debug', $debug);
    }

    public function destroy(Request $request, Category $category)
    {
        $this->categoryService->delete($category);

        $debug = ['module' => 'Category', 'action' => 'Delete', 'id' => $category->id];

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'debug' => $debug]);
        }

        return redirect()->route('admin.categories.index')
            ->with('status', 'Category deleted successfully!')
            ->with('server_debug', $debug);
    }
}
