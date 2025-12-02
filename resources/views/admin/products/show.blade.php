@extends('layouts.admin')

@section('title', 'Chi tiết: ' . $product->name)
@section('header', 'Chi tiết sản phẩm')

@section('content')
    <div class=" bg-white rounded-xl shadow-sm p-6 max-w-7xl mx-auto space-y-6">

        {{-- Header Actions --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $product->name }}</h1>
                <div class="flex items-center gap-2 mt-1 text-sm text-gray-500">
                    <span class="px-2 py-0.5 rounded bg-gray-100 border border-gray-200 font-mono text-xs">{{ $product->sku }}</span>
                    <span>•</span>
                    <span>Đã tạo: {{ $product->created_at->format('d/m/Y H:i') }}</span>
                </div>
            </div>
            <div class="flex gap-3 w-full sm:w-auto">
                <a href="{{ route('admin.products.index') }}"
                    class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 text-sm font-medium shadow-sm transition">
                    ← Quay lại
                </a>
                <a href="{{ route('admin.products.edit', $product) }}"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-medium shadow-sm transition flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    Chỉnh sửa
                </a>
                <form action="{{ route('admin.products.destroy', $product) }}" method="POST"
                    onsubmit="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này? Hành động này không thể hoàn tác.');">
                    @csrf @method('DELETE')
                    <button type="submit"
                        class="px-4 py-2 bg-red-50 text-red-700 border border-red-200 rounded-lg hover:bg-red-100 text-sm font-medium transition flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        Xóa
                    </button>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- LEFT COLUMN: GALLERY --}}
            {{-- Khởi tạo currentImage bằng ảnh đại diện của sản phẩm --}}
            <div class="lg:col-span-1" x-data="{ currentImage: '{{ $product->image_url }}' }">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden sticky top-6">

                    {{-- Main Image Display --}}
                    <div class="aspect-square bg-gray-50 border-b border-gray-100 relative group flex items-center justify-center p-4">
                        <img :src="currentImage" alt="{{ $product->name }}" class="max-w-full max-h-full object-contain transition-transform duration-300 group-hover:scale-105">

                        {{-- Badge Trạng thái nổi --}}
                        <div class="absolute top-4 left-4 flex flex-col gap-2">
                            @if($product->status === 'active')
                                <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full border border-green-200 shadow-sm">Active</span>
                            @else
                                <span class="px-3 py-1 bg-gray-100 text-gray-700 text-xs font-bold rounded-full border border-gray-200 shadow-sm">Draft</span>
                            @endif

                            @if($product->is_featured)
                                <span class="px-3 py-1 bg-yellow-100 text-yellow-700 text-xs font-bold rounded-full border border-yellow-200 shadow-sm">Nổi bật</span>
                            @endif
                        </div>
                    </div>

                    {{-- Thumbnails --}}
                    @if($product->images->isNotEmpty())
                        <div class="p-4 bg-white">
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Thư viện ảnh ({{ $product->images->count() }})</p>
                            <div class="grid grid-cols-5 gap-2">
                                {{-- Loop qua gallery --}}
                                @foreach($product->images as $img)
                                    <button
                                        @click="currentImage = '{{ $img->image_url }}'"
                                        class="aspect-square rounded-lg border-2 overflow-hidden hover:opacity-100 transition focus:outline-none bg-gray-50 relative"
                                        :class="currentImage === '{{ $img->image_url }}' ? 'border-indigo-600 ring-2 ring-indigo-100 opacity-100' : 'border-transparent opacity-70 hover:border-gray-300'"
                                    >
                                        <img src="{{ $img->image_url }}" class="w-full h-full object-cover">
                                        @if($img->color)
                                            <div class="absolute bottom-0 inset-x-0 bg-black/50 text-white text-[9px] truncate px-1 py-0.5 text-center">{{ $img->color }}</div>
                                        @endif
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="p-4 text-center text-sm text-gray-400 italic bg-gray-50 border-t">Không có ảnh thư viện bổ sung</div>
                    @endif
                </div>
            </div>

            {{-- RIGHT COLUMN: INFO --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- 1. General Info Card --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Thông tin chung
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div>
                                <label class="text-xs text-gray-500 font-semibold uppercase">Danh mục</label>
                                <div class="text-gray-900 font-medium mt-1">{{ $product->category->name ?? 'Chưa phân loại' }}</div>
                            </div>

                            <div>
                                <label class="text-xs text-gray-500 font-semibold uppercase">Giá bán</label>
                                <div class="flex items-baseline gap-2 mt-1">
                                    <span class="text-2xl font-bold text-indigo-600">{{ number_format($product->price, 0, ',', '.') }} ₫</span>
                                    @if($product->market_price > $product->price)
                                        <span class="text-sm text-gray-400 line-through">{{ number_format($product->market_price, 0, ',', '.') }} ₫</span>
                                    @endif
                                </div>
                            </div>

                            @if($product->cost_price)
                            <div>
                                <label class="text-xs text-gray-500 font-semibold uppercase">Giá vốn (Admin only)</label>
                                <div class="text-gray-600 font-medium mt-1">{{ number_format($product->cost_price, 0, ',', '.') }} ₫</div>
                            </div>
                            @endif
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="text-xs text-gray-500 font-semibold uppercase">Tồn kho</label>
                                <div class="mt-1 flex items-center gap-2">
                                    <span class="text-xl font-bold {{ $product->quantity > 10 ? 'text-gray-900' : 'text-red-600' }}">{{ $product->quantity }}</span>
                                    <span class="text-sm text-gray-500">{{ $product->unit ?? 'sản phẩm' }}</span>
                                    @if($product->quantity <= $product->min_stock)
                                        <span class="px-2 py-0.5 bg-red-100 text-red-700 text-xs rounded-full font-bold">Low Stock</span>
                                    @endif
                                </div>
                            </div>

                            @if($product->colors)
                            <div>
                                <label class="text-xs text-gray-500 font-semibold uppercase">Màu sắc</label>
                                <div class="flex flex-wrap gap-2 mt-1">
                                    @foreach($product->colors as $color)
                                        <span class="px-2 py-1 bg-gray-100 border border-gray-200 rounded text-sm text-gray-700">{{ $color }}</span>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            @if($product->warranty)
                            <div>
                                <label class="text-xs text-gray-500 font-semibold uppercase">Bảo hành</label>
                                <div class="text-gray-900 mt-1">{{ $product->warranty }}</div>
                            </div>
                            @endif
                        </div>
                    </div>

                    @if($product->short_description)
                        <div class="mt-6 pt-6 border-t border-gray-100">
                            <label class="text-xs text-gray-500 font-semibold uppercase mb-2 block">Mô tả ngắn</label>
                            <div class="bg-yellow-50 text-gray-700 p-4 rounded-lg text-sm leading-relaxed border border-yellow-100">
                                {{ $product->short_description }}
                            </div>
                        </div>
                    @endif
                </div>

                {{-- 2. Specifications --}}
                @if(isset($product->metadata['specs']) && count($product->metadata['specs']))
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path></svg>
                        Thông số kỹ thuật
                    </h2>
                    <div class="overflow-hidden rounded-lg border border-gray-200">
                        <table class="min-w-full divide-y divide-gray-200">
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @foreach($product->metadata['specs'] as $index => $spec)
                                    <tr class="{{ $index % 2 === 0 ? 'bg-gray-50' : 'bg-white' }}">
                                        <td class="px-4 py-3 text-sm font-medium text-gray-600 w-1/3">{{ $spec['key'] ?? 'N/A' }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ $spec['value'] ?? 'N/A' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                {{-- 3. Detailed Description --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path></svg>
                        Mô tả chi tiết
                    </h2>
                    @if($product->description)
                        <div class="prose prose-sm max-w-none text-gray-600">
                            {!! nl2br(e($product->description)) !!}
                        </div>
                    @else
                        <p class="text-gray-400 italic text-sm">Chưa có mô tả chi tiết.</p>
                    @endif
                </div>

                {{-- 4. Meta Flags --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div class="bg-white p-3 rounded-xl border border-gray-200 flex flex-col items-center justify-center text-center shadow-sm">
                        <span class="text-xs text-gray-500 uppercase font-semibold mb-1">Hiển thị</span>
                        @if($product->status === 'active')
                            <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        @else
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        @endif
                    </div>
                    <div class="bg-white p-3 rounded-xl border border-gray-200 flex flex-col items-center justify-center text-center shadow-sm">
                        <span class="text-xs text-gray-500 uppercase font-semibold mb-1">Nổi bật</span>
                        @if($product->is_featured)
                            <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        @else
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        @endif
                    </div>
                    <div class="bg-white p-3 rounded-xl border border-gray-200 flex flex-col items-center justify-center text-center shadow-sm">
                        <span class="text-xs text-gray-500 uppercase font-semibold mb-1">Ưu đãi</span>
                        @if($product->special_offer)
                            <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        @else
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
