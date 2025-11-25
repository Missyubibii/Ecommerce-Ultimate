<?php

namespace App\Services;

use App\Models\Banner;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class BannerService
{
    public function getAll($filters = [])
    {
        $query = Banner::orderBy('sort_order', 'asc')->orderByDesc('created_at');

        if (!empty($filters['position'])) {
            $query->where('position', $filters['position']);
        }

        return $query->paginate(10);
    }

    public function create(array $data)
    {
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $data['image'] = $data['image']->store('banners', 'public');
        }
        $data['is_active'] = isset($data['is_active']) ? (bool)$data['is_active'] : false;

        return Banner::create($data);
    }

    public function update(Banner $banner, array $data)
    {
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            // Xóa ảnh cũ
            if ($banner->image) {
                Storage::disk('public')->delete($banner->image);
            }
            $data['image'] = $data['image']->store('banners', 'public');
        }
        $data['is_active'] = isset($data['is_active']) ? (bool)$data['is_active'] : false;

        $banner->update($data);
        return $banner;
    }

    public function delete(Banner $banner)
    {
        if ($banner->image) {
            Storage::disk('public')->delete($banner->image);
        }
        return $banner->delete();
    }
}
