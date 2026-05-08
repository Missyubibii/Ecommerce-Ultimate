<?php

namespace App\Services;

use App\Models\Banner;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class BannerService
{
    /**
     * 1. Lấy danh sách banner có phân trang và bộ lọc
     */
    public function getAll($filters = [])
    {
        // Khởi tạo truy vấn, sắp xếp theo thứ tự hiển thị (sort_order) và ngày tạo mới nhất
        $query = Banner::orderBy('sort_order', 'asc')->orderByDesc('created_at');

        // Lọc theo vị trí hiển thị (VD: top, sidebar, popup)
        if (!empty($filters['position'])) {
            $query->where('position', $filters['position']);
        }

        return $query->paginate(10);
    }

    /**
     * 2. Tạo mới banner
     */
    public function create(array $data)
    {
        // Xử lý upload hình ảnh nếu có file được gửi lên
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $data['image'] = $data['image']->store('banners', 'public');
        }

        // Ép kiểu trạng thái kích hoạt về dạng boolean (true/false)
        $data['is_active'] = isset($data['is_active']) ? (bool) $data['is_active'] : false;

        return Banner::create($data);
    }

    /**
     * 3. Cập nhật thông tin banner
     */
    public function update(Banner $banner, array $data)
    {
        // Xử lý thay đổi hình ảnh
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            // Bước A: Xóa file ảnh cũ trong bộ nhớ (Storage) để tránh rác dữ liệu
            if ($banner->image) {
                Storage::disk('public')->delete($banner->image);
            }
            // Bước B: Lưu file ảnh mới
            $data['image'] = $data['image']->store('banners', 'public');
        }

        // Cập nhật trạng thái hiển thị
        $data['is_active'] = isset($data['is_active']) ? (bool) $data['is_active'] : false;

        $banner->update($data);
        return $banner;
    }

    /**
     * 4. Xóa banner và dọn dẹp file liên quan
     */
    public function delete(Banner $banner)
    {
        // Xóa file vật lý khỏi thư mục storage trước khi xóa bản ghi trong database
        if ($banner->image) {
            Storage::disk('public')->delete($banner->image);
        }
        return $banner->delete();
    }
}