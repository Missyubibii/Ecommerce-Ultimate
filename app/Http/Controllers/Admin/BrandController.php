<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class BrandController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return ['auth', new Middleware('role:admin')];
    }

    public function index()
    {
        $brands = Brand::orderBy('sort_order')->paginate(10);
        return view('admin.brands.index', compact('brands'));
    }

    public function create()
    {
        return view('admin.brands.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:brands,slug',
            'logo' => 'nullable|image|max:2048',
            'description' => 'nullable|string',
            'display_locations' => 'nullable|array',
            'sort_order' => 'nullable|integer',
        ]);

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('brands', 'public');
        }

        $data['is_active'] = $request->boolean('is_active', true);
        $data['display_locations'] = $request->input('display_locations', []);

        Brand::create($data);

        return redirect()->route('admin.brands.index')->with('status', 'Brand created successfully!');
    }

    public function edit(Brand $brand)
    {
        return view('admin.brands.edit', compact('brand'));
    }

    public function update(Request $request, Brand $brand)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:brands,slug,' . $brand->id,
            'logo' => 'nullable|image|max:2048',
            'description' => 'nullable|string',
            'display_locations' => 'nullable|array',
            'sort_order' => 'nullable|integer',
        ]);

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        if ($request->hasFile('logo')) {
            if ($brand->logo) {
                Storage::disk('public')->delete($brand->logo);
            }
            $data['logo'] = $request->file('logo')->store('brands', 'public');
        }

        $data['is_active'] = $request->boolean('is_active');
        $data['display_locations'] = $request->input('display_locations', []);

        $brand->update($data);

        return redirect()->route('admin.brands.index')->with('status', 'Brand updated successfully!');
    }

    public function destroy(Brand $brand)
    {
        if ($brand->logo) {
            Storage::disk('public')->delete($brand->logo);
        }
        $brand->delete();

        return redirect()->route('admin.brands.index')->with('status', 'Brand deleted successfully!');
    }
}
