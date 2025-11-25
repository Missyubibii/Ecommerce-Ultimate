<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\BannerService;
use App\Models\Banner;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    protected $bannerService;

    public function __construct(BannerService $bannerService)
    {
        $this->bannerService = $bannerService;
    }

    public function index(Request $request)
    {
        $banners = $this->bannerService->getAll($request->all());
        return view('admin.banners.index', compact('banners'));
    }

    public function create()
    {
        return view('admin.banners.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:2048', // 2MB
            'url' => 'nullable|url',
            'position' => 'required|in:main_slider,header_top',
            'sort_order' => 'integer',
        ]);

        $this->bannerService->create($request->all());

        return redirect()->route('admin.banners.index')->with('success', 'Thêm banner thành công');
    }

    public function edit(Banner $banner)
    {
        return view('admin.banners.edit', compact('banner'));
    }

    public function update(Request $request, Banner $banner)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:2048',
            'url' => 'nullable|url',
            'position' => 'required|in:main_slider,header_top',
            'sort_order' => 'integer',
        ]);

        $this->bannerService->update($banner, $request->all());

        return redirect()->route('admin.banners.index')->with('success', 'Cập nhật banner thành công');
    }

    public function destroy(Banner $banner)
    {
        $this->bannerService->delete($banner);
        return back()->with('success', 'Đã xóa banner');
    }
}
