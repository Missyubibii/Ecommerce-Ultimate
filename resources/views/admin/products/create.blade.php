@extends('layouts.admin')
@section('title', 'Thêm sản phẩm mới')
@section('header', 'Tạo sản phẩm mới')

@section('content')
    <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-sm p-6">
        <form id="productForm" action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data"
            class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tên sản phẩm <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả chi tiết</label>
                            <textarea name="description" rows="6"
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">{{ old('description') }}</textarea>
                        </div>
                    </div>

                    {{-- Specs Alpine --}}
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200" x-data="{ specs: [] }">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-sm font-bold text-gray-800 uppercase">Thông số kỹ thuật</h3>
                            <button type="button" @click="specs.push({key: '', value: ''})"
                                class="text-xs bg-indigo-100 text-indigo-700 px-2 py-1 rounded hover:bg-indigo-200">+
                                Thêm</button>
                        </div>
                        <template x-for="(spec, index) in specs" :key="index">
                            <div class="flex gap-2 mb-2">
                                <input type="text" placeholder="Tên (VD: RAM)" x-model="spec.key"
                                    class="w-1/3 px-3 py-2 border rounded text-sm">
                                <input type="text" placeholder="Giá trị (VD: 8GB)" x-model="spec.value"
                                    class="w-2/3 px-3 py-2 border rounded text-sm">
                                <button type="button" @click="specs = specs.filter((_, i) => i !== index)"
                                    class="text-red-500 px-2">X</button>
                            </div>
                        </template>
                        {{-- Input hidden để gửi JSON specs --}}
                        <input type="hidden" name="specifications" :value="JSON.stringify(specs)">
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Giá bán <span
                                class="text-red-500">*</span></label>
                        <input type="number" name="price" value="{{ old('price') }}" required
                            class="w-full px-3 py-2 border rounded-lg font-bold">

                        <label class="block text-sm font-medium text-gray-700 mt-3 mb-1">Tồn kho</label>
                        <input type="number" name="quantity" value="{{ old('quantity', 0) }}"
                            class="w-full px-3 py-2 border rounded-lg">
                    </div>

                    <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Danh mục <span
                                class="text-red-500">*</span></label>
                        <select name="category_id" required class="w-full px-3 py-2 border rounded-lg">
                            <option value="">-- Chọn --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ảnh đại diện</label>
                        <input type="file" name="image" accept="image/*"
                            class="w-full text-sm text-gray-500 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-6 border-t">
                <a href="{{ route('admin.products.index') }}"
                    class="px-6 py-2 bg-gray-100 rounded hover:bg-gray-200 text-gray-700">Hủy</a>
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Lưu sản
                    phẩm</button>
            </div>
        </form>
    </div>
@endsection
