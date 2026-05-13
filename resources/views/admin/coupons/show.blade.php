@extends('layouts.admin')

@section('title', 'Chi tiết Mã giảm giá')

@section('content')
<div class="py-12 bg-gray-50 min-h-screen">
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900">Chi tiết Mã Giảm Giá</h1>
                <p class="mt-2 text-sm text-gray-500">Xem thông tin chi tiết và hiệu suất của mã khuyến mãi.</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.coupons.index') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition shadow-sm flex items-center">
                    <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                    Quay lại
                </a>
                <a href="{{ route('admin.coupons.edit', $coupon->id) }}" class="px-4 py-2 bg-indigo-600 rounded-lg text-sm font-medium text-white hover:bg-indigo-700 transition shadow-md flex items-center">
                    <i data-lucide="edit" class="w-4 h-4 mr-2"></i>
                    Chỉnh sửa
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Cột trái: Thông tin chính --}}
            <div class="lg:col-span-2 space-y-8">
                {{-- Card chính --}}
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
                    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-8 py-10 text-white relative">
                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-4">
                                <span class="px-3 py-1 bg-white/20 backdrop-blur-md rounded-full text-xs font-bold uppercase tracking-wider">
                                    {{ $coupon->discount_type == 'percent' ? 'Phần trăm' : 'Cố định' }}
                                </span>
                                <span class="px-3 py-1 {{ $coupon->is_active ? 'bg-green-400' : 'bg-red-400' }} rounded-full text-xs font-bold uppercase tracking-wider">
                                    {{ $coupon->is_active ? 'Đang hoạt động' : 'Tạm dừng' }}
                                </span>
                            </div>
                            <h2 class="text-5xl font-black tracking-tighter mb-2">{{ $coupon->code }}</h2>
                            <p class="text-indigo-100 text-lg">{{ $coupon->description ?: 'Không có mô tả cho mã này.' }}</p>
                        </div>
                        {{-- Trang trí --}}
                        <div class="absolute top-0 right-0 -mt-10 -mr-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
                        <div class="absolute bottom-0 left-0 -mb-10 -ml-10 w-40 h-40 bg-purple-900/20 rounded-full blur-3xl"></div>
                    </div>

                    <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4">Thông số giảm giá</h3>
                            <div class="space-y-4">
                                <div class="flex justify-between items-center border-b border-gray-50 pb-2">
                                    <span class="text-gray-600 font-medium">Giá trị giảm:</span>
                                    <span class="text-xl font-bold text-gray-900">
                                        {{ $coupon->discount_type == 'percent' ? $coupon->discount_value.'%' : number_format($coupon->discount_value).' VNĐ' }}
                                    </span>
                                </div>
                                <div class="flex justify-between items-center border-b border-gray-50 pb-2">
                                    <span class="text-gray-600 font-medium">Đơn tối thiểu:</span>
                                    <span class="text-gray-900 font-bold">{{ number_format($coupon->min_order_amount) }} VNĐ</span>
                                </div>
                                <div class="flex justify-between items-center border-b border-gray-50 pb-2">
                                    <span class="text-gray-600 font-medium">Số lượng tối đa:</span>
                                    <span class="text-gray-900 font-bold">{{ $coupon->usage_limit ?: 'Không giới hạn' }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600 font-medium">Kênh áp dụng:</span>
                                    <span class="px-2 py-1 bg-indigo-50 text-indigo-700 rounded text-xs font-bold uppercase">
                                        {{ $coupon->channel == 'both' ? 'Toàn hệ thống' : ($coupon->channel == 'email' ? 'Chỉ Email' : 'Chỉ Web') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4">Thời gian & Hiệu suất</h3>
                            <div class="space-y-4">
                                <div class="flex justify-between items-center border-b border-gray-50 pb-2">
                                    <span class="text-gray-600 font-medium">Ngày hết hạn:</span>
                                    <span class="text-gray-900 font-bold {{ $coupon->expiry_date && $coupon->expiry_date->isPast() ? 'text-red-500' : '' }}">
                                        {{ $coupon->expiry_date ? $coupon->expiry_date->format('d/m/Y H:i') : 'Vĩnh viễn' }}
                                    </span>
                                </div>
                                <div class="flex justify-between items-center border-b border-gray-50 pb-2">
                                    <span class="text-gray-600 font-medium">Đã sử dụng:</span>
                                    <div class="text-right">
                                        <span class="text-xl font-bold text-indigo-600">{{ $coupon->orders_count ?? 0 }}</span>
                                        <span class="text-gray-400 text-xs ml-1">lần</span>
                                    </div>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600 font-medium">Ngày tạo:</span>
                                    <span class="text-gray-500">{{ $coupon->created_at->format('d/m/Y') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Giới hạn áp dụng Card --}}
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
                    <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center">
                        <i data-lucide="filter" class="w-5 h-5 mr-2 text-rose-500"></i>
                        Giới hạn áp dụng
                    </h3>
                    
                    <div class="space-y-8">
                        {{-- Danh mục --}}
                        <div>
                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Danh mục được áp dụng</h4>
                            <div class="flex flex-wrap gap-2">
                                @forelse($selectedCategories as $cat)
                                    <span class="px-3 py-1.5 bg-indigo-50 text-indigo-700 rounded-lg text-sm font-medium border border-indigo-100">
                                        {{ $cat->name }}
                                    </span>
                                @empty
                                    <span class="text-sm text-gray-400 italic">Áp dụng cho tất cả danh mục</span>
                                @endforelse
                            </div>
                        </div>

                        {{-- Thương hiệu --}}
                        <div>
                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Thương hiệu được áp dụng</h4>
                            <div class="flex flex-wrap gap-2">
                                @forelse($selectedBrands as $brand)
                                    <span class="px-3 py-1.5 bg-rose-50 text-rose-700 rounded-lg text-sm font-medium border border-rose-100">
                                        {{ $brand->name }}
                                    </span>
                                @empty
                                    <span class="text-sm text-gray-400 italic">Áp dụng cho tất cả thương hiệu</span>
                                @endforelse
                            </div>
                        </div>

                        {{-- Sản phẩm --}}
                        <div>
                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Sản phẩm cụ thể</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                @forelse($selectedProducts as $prod)
                                    <div class="flex items-center p-3 bg-emerald-50 rounded-xl border border-emerald-100 group hover:bg-emerald-100 transition">
                                        <div class="w-8 h-8 rounded-lg bg-emerald-200 flex items-center justify-center mr-3">
                                            <i data-lucide="package" class="w-4 h-4 text-emerald-700"></i>
                                        </div>
                                        <span class="text-sm font-medium text-emerald-800 truncate">{{ $prod->name }}</span>
                                    </div>
                                @empty
                                    <span class="text-sm text-gray-400 italic">Áp dụng cho tất cả sản phẩm</span>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Cột phải: Thống kê nhanh --}}
            <div class="lg:col-span-1 space-y-6">
                {{-- Status card --}}
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                    <h3 class="text-sm font-bold text-gray-900 mb-4">Trạng thái hệ thống</h3>
                    <div class="flex items-center mb-6">
                        @if($coupon->is_active && (!$coupon->expiry_date || $coupon->expiry_date->isFuture()))
                            <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse mr-3"></div>
                            <span class="text-green-700 font-bold">Đang hiệu lực</span>
                        @else
                            <div class="w-3 h-3 bg-red-500 rounded-full mr-3"></div>
                            <span class="text-red-700 font-bold">Không khả dụng</span>
                        @endif
                    </div>
                    
                    <div class="space-y-4">
                        <div class="p-4 bg-gray-50 rounded-xl">
                            <p class="text-xs text-gray-500 mb-1">Dự kiến kết thúc trong</p>
                            <p class="text-lg font-bold text-gray-900">
                                @if($coupon->expiry_date)
                                    @if($coupon->expiry_date->isPast())
                                        Đã hết hạn
                                    @else
                                        {{ $coupon->expiry_date->diffForHumans() }}
                                    @endif
                                @else
                                    Không bao giờ
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Tips/Help --}}
                <div class="bg-indigo-900 rounded-2xl shadow-lg p-6 text-white relative overflow-hidden">
                    <div class="relative z-10">
                        <h3 class="text-lg font-bold mb-2">Mẹo Quản lý</h3>
                        <p class="text-indigo-200 text-sm leading-relaxed">
                            Mã này được gửi qua kênh <span class="font-bold text-white">{{ $coupon->channel }}</span>. Bạn có thể tạm dừng mã bất cứ lúc nào bằng cách chuyển trạng thái sang Vô hiệu hóa.
                        </p>
                    </div>
                    <i data-lucide="lightbulb" class="absolute -bottom-4 -right-4 w-24 h-24 text-white/10 rotate-12"></i>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
