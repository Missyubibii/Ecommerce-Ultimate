<?php
namespace App\View\Composers;
use Illuminate\View\View;
use App\Services\CategoryService;

class CategoryComposer
{
    protected $categoryService;
    public function __construct(CategoryService $categoryService) {
        $this->categoryService = $categoryService;
    }
    public function compose(View $view) {
        // Share biến $globalCategories cho tất cả views
        $view->with('globalCategories', $this->categoryService->getTree());
    }
}
