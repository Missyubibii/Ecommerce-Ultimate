@extends('layouts.admin')

@section('title', 'Thêm sản phẩm mới')
@section('header', 'Tạo sản phẩm mới')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <form id="createProductForm" enctype="multipart/form-data">
                @csrf

                {{-- LƯU Ý: Phải có meta tag
                <meta name="csrf-token" content="{{ csrf_token() }}"> trong layout/app.blade.php để AJAX POST hoạt động --}}

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                    {{-- CỘT TRÁI: THÔNG TIN CHÍNH --}}
                    <div class="lg:col-span-2 space-y-6">
                        {{-- Box: Thông tin cơ bản --}}
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                            <h3 class="text-lg font-semibold mb-4 text-gray-800">Thông tin cơ bản</h3>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1 required">Tên sản phẩm <span
                                        class="text-red-500">*</span></label>
                                <input type="text" name="name" value="{{ old('name') }}" required
                                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Slug (URL)</label>
                                    <input type="text" name="slug" value="{{ old('slug') }}"
                                        placeholder="Tu dong tao neu de trong"
                                        class="w-full px-4 py-2 border rounded-lg bg-white focus:ring-2 focus:ring-blue-500 outline-none">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Mã SKU</label>
                                    <input type="text" name="sku" value="{{ old('sku') }}"
                                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tóm tắt (Summary)</label>
                                <textarea name="summary" rows="3"
                                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">{{ old('summary') }}</textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả chi tiết</label>
                                <textarea name="description" rows="6"
                                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">{{ old('description') }}</textarea>
                            </div>
                        </div>

                        {{-- Box: Thông số kỹ thuật (JSON) --}}
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200" x-data="{ specs: [] }">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-semibold text-gray-800">Thông số kỹ thuật</h3>
                                <button type="button" @click="addSpecRow($el)"
                                    class="text-sm bg-blue-100 text-blue-700 px-3 py-1 rounded hover:bg-blue-200 transition">+
                                    Thêm dòng</button>
                            </div>
                            <div id="spec-container" class="space-y-2">
                                <p class="text-gray-500 text-sm">Nhập Tên thông số (VD: Màu sắc) và Giá trị (VD: Đỏ, Xanh).
                                </p>
                            </div>
                        </div>

                        {{-- Box: Hình ảnh --}}
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Hình ảnh</h3>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ảnh đại diện</label>
                            <input type="file" name="image" accept="image/*"
                                class="w-full text-sm text-gray-500 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">

                            <label class="block text-sm font-medium text-gray-700 mt-4 mb-1">Thư viện ảnh (Gallery)</label>
                            <input type="file" name="gallery[]" multiple accept="image/*"
                                class="w-full text-sm text-gray-500 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        </div>
                    </div>

                    {{-- CỘT PHẢI: CÀI ĐẶT & GIÁ --}}
                    <div class="space-y-6">
                        {{-- Box: Giá & Kho --}}
                        <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-200">
                            <h3 class="font-bold text-gray-800 mb-4 border-b pb-2">Giá & Kho hàng</h3>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Giá bán <span
                                        class="text-red-500">*</span></label>
                                <input type="number" name="price" value="{{ old('price') }}" required
                                    class="w-full px-3 py-2 border rounded-lg font-bold">
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Giá cũ (Khuyến mãi)</label>
                                <input type="number" name="old_price" value="{{ old('old_price') }}"
                                    class="w-full px-3 py-2 border rounded-lg">
                            </div>

                            <div class="grid grid-cols-2 gap-3 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tồn kho</label>
                                    <input type="number" name="quantity" value="{{ old('quantity', 0) }}"
                                        class="w-full px-3 py-2 border rounded-lg">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Bảo hành</label>
                                    <input type="text" name="warranty" value="{{ old('warranty') }}"
                                        placeholder="VD: 12 tháng" class="w-full px-3 py-2 border rounded-lg">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Vị trí kho</label>
                                <input type="text" name="stock_locations_input" placeholder="VD: Kệ A1, Kho tổng"
                                    class="w-full px-3 py-2 border rounded-lg"
                                    data-stock-locations-input="{{ old('stock_locations_input') }}">
                            </div>
                        </div>

                        {{-- Box: Phân loại --}}
                        <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-200">
                            <h3 class="font-bold text-gray-800 mb-4 border-b pb-2">Phân loại</h3>

                            {{-- <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Thương hiệu</label>
                                <select name="brand_id" class="w-full px-3 py-2 border rounded-lg bg-white">
                                    <option value="">-- Chọn thương hiệu --</option>
                                    @foreach($brands ?? [] as $brand)
                                        <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
                                            {{ $brand->name }}</option>
                                    @endforeach
                                </select>
                            </div> --}}

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Danh mục <span
                                        class="text-red-500">*</span></label>
                                <select name="category_ids[]" multiple required
                                    class="w-full px-3 py-2 border rounded-lg h-32 bg-white">
                                    @foreach($categories ?? [] as $cat)
                                        {{-- Giả định old('category_ids') là array --}}
                                        <option value="{{ data_get($cat, 'id') }}" {{ in_array(data_get($cat, 'id'), old('category_ids', [])) ? 'selected' : '' }}>{{ data_get($cat, 'name') }}</option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-gray-500 mt-1">Giữ Ctrl/Cmd để chọn nhiều</p>
                            </div>
                        </div>

                        {{-- Box: Trạng thái (Switches) --}}
                        <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-200">
                            <h3 class="font-bold text-gray-800 mb-4 border-b pb-2">Thiết lập hiển thị</h3>

                            <div class="space-y-3">
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" name="is_active" value="1" checked
                                        class="w-5 h-5 rounded text-blue-600">
                                    <span class="ml-2 text-gray-700">Đang hoạt động (Active)</span>
                                </label>

                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" name="is_featured" value="1"
                                        class="w-5 h-5 rounded text-blue-600">
                                    <span class="ml-2 text-gray-700">Sản phẩm nổi bật</span>
                                </label>

                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" name="special_offer" value="1"
                                        class="w-5 h-5 rounded text-blue-600">
                                    <span class="ml-2 text-gray-700">Ưu đãi đặc biệt</span>
                                </label>

                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" name="online_only" value="1"
                                        class="w-5 h-5 rounded text-blue-600">
                                    <span class="ml-2 text-gray-700">Chỉ bán Online</span>
                                </label>
                            </div>
                        </div>

                        {{-- Box: SEO --}}
                        <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-200">
                            <h3 class="font-bold text-gray-800 mb-4 border-b pb-2">SEO</h3>
                            <div class="space-y-3">
                                <div>
                                    <label class="text-xs font-semibold text-gray-500">Meta Title</label>
                                    <input type="text" name="meta_title" value="{{ old('meta_title') }}"
                                        class="w-full px-3 py-1.5 text-sm border rounded">
                                </div>
                                <div>
                                    <label class="text-xs font-semibold text-gray-500">Meta Keywords</label>
                                    <input type="text" name="meta_keywords" value="{{ old('meta_keywords') }}"
                                        class="w-full px-3 py-1.5 text-sm border rounded">
                                </div>
                                <div>
                                    <label class="text-xs font-semibold text-gray-500">Meta Description</label>
                                    <textarea name="meta_description" rows="2"
                                        class="w-full px-3 py-1.5 text-sm border rounded">{{ old('meta_description') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Sticky Footer Actions --}}
                <div class="mt-8 p-4 bg-gray-50 border-t flex justify-end gap-4 sticky bottom-0 z-10">
                    <a href="{{ route('admin.products.index') }}"
                        class="px-6 py-2 border border-gray-300 rounded-lg bg-white hover:bg-gray-100 transition">Hủy bỏ</a>
                    <button type="submit" id="btnSubmit"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center gap-2 transition shadow-lg">
                        <i data-lucide="save" class="h-4 w-4"></i> Lưu sản phẩm
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Hàm thêm dòng Specs
        function addSpecRow() {
            const container = document.getElementById('spec-container');
            const div = document.createElement('div');
            div.className = 'flex gap-2 items-center spec-row';
            div.innerHTML = `
                <input type="text" placeholder="Tên thông số (VD: RAM)" class="spec-key w-1/3 px-3 py-2 border rounded focus:ring-1 outline-none text-sm">
                <input type="text" placeholder="Giá trị (VD: 8GB)" class="spec-value w-2/3 px-3 py-2 border rounded focus:ring-1 outline-none text-sm">
                <button type="button" onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700 p-2"><i data-lucide="trash" class="h-4 w-4"></i></button>
            `;
            container.appendChild(div);
            lucide.createIcons();
        }

        // Khởi tạo form Specs
        document.addEventListener('DOMContentLoaded', () => {
            addSpecRow(); // Thêm sẵn 1 dòng khi tải trang

            // Thêm logic khởi tạo nếu có old input (từ server validation)
            // Đây là cách đơn giản nhất để khởi tạo AlpineJS specs khi không dùng Livewire/Vue
            // Hiện tại không dùng Alpine data x-model nên không cần init complex.
        });

        // Hàm xử lý SUBMIT (Gửi AJAX Form Data)
        document.getElementById('createProductForm').addEventListener('submit', async function (e) {
            e.preventDefault();
            const btn = document.getElementById('btnSubmit');
            btn.disabled = true;
            btn.innerHTML = '<i data-lucide="loader-2" class="animate-spin h-4 w-4"></i> Đang lưu...';

            const formData = new FormData(this);

            // 1. Gom dữ liệu Specifications thành JSON string
            const specsArray = [];
            document.querySelectorAll('.spec-row').forEach(row => {
                const key = row.querySelector('.spec-key').value.trim();
                const val = row.querySelector('.spec-value').value.trim();
                if (key && val) specsArray.push({ key, value: val });
            });
            // Append JSON string vào FormData để Controller decode
            formData.append('specifications', JSON.stringify(specsArray));

            // 2. Gom Stock Locations thành JSON array string
            const locInput = document.querySelector('[data-stock-locations-input]').value;
            const locArray = locInput.split(',').map(item => item.trim()).filter(item => item !== '');
            formData.append('stock_locations', JSON.stringify(locArray));

            // 3. Xử lý Image & Gallery files (FormData tự động gửi file)

            try {
                const response = await fetch("{{ route('admin.products.store') }}", {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest', // Hybrid check
                        // FormData tự động set Content-Type: multipart/form-data
                    },
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    // Sử dụng hàm Toast/Alert toàn cục nếu có
                    window.location.href = "{{ route('admin.products.index') }}?success=created";
                } else {
                    // Xử lý lỗi validation từ server
                    alert('Lỗi: ' + (result.message || 'Kiểm tra lại dữ liệu'));
                    if (result.errors) console.table(result.errors);
                }
            } catch (error) {
                alert('Lỗi kết nối server');
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i data-lucide="save" class="h-4 w-4"></i> Lưu sản phẩm';
                lucide.createIcons();
            }
        });

        // Final Init for Lucide
        document.addEventListener('DOMContentLoaded', () => lucide.createIcons());
    </script>
@endpush
