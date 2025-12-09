@extends('layouts.admin')

@section('title', 'Chi tiết Giỏ hàng')
@section('header', 'Chi tiết Giỏ hàng')

@section('content')
    <div class="p-12 bg-white rounded-xl shadow-lg">

        {{-- Breadcrumb & Header --}}
        <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <nav class="flex items-center gap-2 text-sm text-gray-500">
                <a href="{{ route('admin.carts.index') }}"
                    class="hover:text-indigo-600 flex items-center gap-1 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Quay lại
                </a>
                <span class="text-gray-300">/</span>
                <span class="font-medium text-gray-700">Chi tiết giỏ hàng</span>
            </nav>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- LEFT COLUMN --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50">
                        <h2 class="font-bold text-gray-800 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                            Danh sách sản phẩm ({{ $items->count() }})
                        </h2>
                    </div>

                    <div class="divide-y divide-gray-100">
                        @foreach($items as $item)
                            <div class="p-6 flex items-start gap-5 hover:bg-gray-50 transition-colors group">
                                {{-- Image --}}
                                <div
                                    class="w-24 h-24 bg-white rounded-lg border border-gray-200 overflow-hidden flex-shrink-0 p-2">
                                    @php
                                        $imgUrl = 'https://placehold.co/100?text=No+Image';
                                        if ($item->product && $item->product->image) {
                                            $imgUrl = filter_var($item->product->image, FILTER_VALIDATE_URL)
                                                ? $item->product->image
                                                : asset('storage/' . $item->product->image);
                                        }
                                    @endphp
                                    <img src="{{ $imgUrl }}" class="w-full h-full object-contain">
                                </div>

                                {{-- Info --}}
                                <div class="flex-1 min-w-0">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h3 class="font-bold text-gray-800 text-lg truncate pr-4">
                                                {{ $item->product->name ?? 'Sản phẩm đã bị xóa' }}
                                            </h3>
                                            <p class="text-sm text-gray-500 font-mono mt-1">SKU:
                                                {{ $item->product->sku ?? 'N/A' }}
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <div class="font-bold text-indigo-600 text-lg">
                                                {{ number_format($item->total, 0, ',', '.') }}đ
                                            </div>
                                            <div class="text-xs text-gray-400">
                                                {{ number_format($item->price, 0, ',', '.') }}đ / sp
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Metadata / Stock --}}
                                    <div class="mt-4 flex items-center gap-4">
                                        <div
                                            class="flex items-center border border-gray-200 rounded-lg bg-white overflow-hidden">
                                            <span
                                                class="px-3 py-1 bg-gray-50 text-xs text-gray-500 font-medium border-r border-gray-200">SL
                                                trong giỏ</span>
                                            <span class="px-3 py-1 text-sm font-bold text-gray-800">{{ $item->quantity }}</span>
                                        </div>

                                        <div class="flex items-center gap-1.5 text-xs text-gray-500">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4">
                                                </path>
                                            </svg>
                                            Tồn kho thực tế: <span
                                                class="font-bold {{ ($item->product->quantity ?? 0) < 10 ? 'text-red-500' : 'text-gray-700' }}">{{ $item->product->quantity ?? 0 }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- RIGHT COLUMN --}}
            <div class="space-y-6">
                {{-- Customer Info Card --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                        Thông tin chủ sở hữu
                    </h3>

                    <div class="flex items-center gap-4 mb-4">
                        @if(str_contains($owner, 'Khách vãng lai'))
                            <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center text-gray-400">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="font-bold text-gray-800">Khách vãng lai</div>
                                <div
                                    class="text-xs font-mono text-gray-400 bg-gray-100 px-2 py-0.5 rounded mt-1 border border-gray-200 inline-block">
                                    {{ $sessionId }}
                                </div>
                            </div>
                        @else
                            <div
                                class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-lg">
                                {{ substr($owner, 0, 1) }}
                            </div>
                            <div>
                                <div class="font-bold text-gray-800">{{ explode(' (', $owner)[0] }}</div>
                                <div class="text-xs text-gray-500">{{ Str::between($owner, '(', ')') }}</div>
                            </div>
                        @endif
                    </div>

                    <div class="border-t border-gray-100 pt-4 mt-4">
                        <div class="flex justify-between items-center text-sm mb-2">
                            <span class="text-gray-500">Ngày tạo giỏ:</span>
                            <span
                                class="text-gray-700 font-medium">{{ $items->first()->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-500">Cập nhật cuối:</span>
                            <span
                                class="text-gray-700 font-medium">{{ $items->first()->updated_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>

                {{-- Summary & Actions Card --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="font-bold text-gray-800 mb-4">Tổng quan</h3>

                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-500">Số lượng sản phẩm:</span>
                            <span class="font-medium text-gray-800">{{ $items->sum('quantity') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500 font-medium">Tổng tạm tính:</span>
                            <span
                                class="text-2xl font-bold text-indigo-600">{{ number_format($cartTotal, 0, ',', '.') }}đ</span>
                        </div>
                    </div>

                    <div class="space-y-3">
                        {{-- Nút Xóa --}}
                        <form action="{{ route('admin.carts.destroy') }}" method="POST"
                            onsubmit="return confirm('Hành động này sẽ xóa vĩnh viễn toàn bộ sản phẩm trong giỏ hàng này. Bạn có chắc chắn không?')">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="user_id" value="{{ $userId }}">
                            <input type="hidden" name="session_id" value="{{ $sessionId }}">
                            <button type="submit"
                                class="w-full py-2.5 bg-white border border-red-200 text-red-600 rounded-lg hover:bg-red-50 font-bold text-sm transition-colors flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                    </path>
                                </svg>
                                Xóa giỏ hàng này
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
