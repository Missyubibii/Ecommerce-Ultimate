@extends('layouts.admin')

@section('title', 'Báo cáo Tìm kiếm')

@section('content')
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-800">Search Analytics</h1>
            <button onclick="window.location.reload()" class="p-2 bg-gray-100 rounded-full hover:bg-gray-200"
                title="Tải lại trang">
                <i data-lucide="refresh-cw" class="w-5 h-5"></i>
            </button>
        </div>

        {{-- Grid Thống kê --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- Card 1: Top Từ khóa --}}
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex flex-col h-full">
                <h3 class="font-bold text-lg mb-4 flex items-center gap-2 text-indigo-600 flex-shrink-0">
                    <i data-lucide="trending-up" class="w-5 h-5"></i> Top Từ khóa phổ biến
                </h3>
                {{-- Thêm thanh cuộn, giới hạn chiều cao (~5 dòng) --}}
                <div class="overflow-y-auto max-h-[300px] relative custom-scrollbar">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 text-gray-500 sticky top-0 z-10 shadow-sm">
                            <tr>
                                <th class="px-3 py-2 bg-gray-50">Từ khóa</th>
                                <th class="px-3 py-2 text-right bg-gray-50">Lượt tìm</th>
                                {{-- Đổi tiêu đề sang tiếng Việt rõ nghĩa hơn --}}
                                <th class="px-3 py-2 text-right bg-gray-50">Thời gian cập nhật</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($topKeywords as $term)
                                <tr>
                                    <td class="px-3 py-3 font-medium text-gray-700">{{ $term->term }}</td>
                                    <td class="px-3 py-3 text-right">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            {{ number_format($term->hits) }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-3 text-right text-gray-500">
                                        {{ $term->last_searched_at->diffForHumans() }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-gray-400">Chưa có dữ liệu</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Card 2: Không tìm thấy kết quả (Cơ hội kinh doanh) --}}
            <div class="bg-white p-6 rounded-xl shadow-sm border border-red-50 flex flex-col h-full">
                <h3 class="font-bold text-lg mb-4 flex items-center gap-2 text-red-600 flex-shrink-0">
                    <i data-lucide="alert-circle" class="w-5 h-5"></i> Từ khóa KHÔNG ra kết quả
                    <span class="text-xs font-normal text-gray-500 ml-auto">(Cần nhập hàng)</span>
                </h3>
                {{-- Thêm thanh cuộn, giới hạn chiều cao --}}
                <div class="overflow-y-auto max-h-[300px] relative custom-scrollbar">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-red-50 text-red-700 sticky top-0 z-10 shadow-sm">
                            <tr>
                                <th class="px-3 py-2 bg-red-50">Từ khóa thiếu</th>
                                <th class="px-3 py-2 text-right bg-red-50">Số lần khách tìm</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($zeroResultKeywords as $log)
                                <tr>
                                    <td class="px-3 py-3 font-medium text-gray-800">{{ $log->keyword }}</td>
                                    <td class="px-3 py-3 text-right font-bold text-red-600">{{ $log->count }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center py-4 text-gray-400">Tuyệt vời! Hệ thống đáp ứng mọi tìm
                                        kiếm.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Bảng Log chi tiết --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex flex-col">
            <div class="p-6 border-b border-gray-100 flex-shrink-0">
                <h3 class="font-bold text-lg text-gray-800">Lịch sử tìm kiếm chi tiết</h3>
            </div>
            {{-- Thêm thanh cuộn cho bảng log chi tiết --}}
            <div class="overflow-y-auto max-h-[400px] relative custom-scrollbar">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-500 font-medium sticky top-0 z-10 shadow-sm">
                        <tr>
                            <th class="px-6 py-3 bg-gray-50">Thời gian</th>
                            <th class="px-6 py-3 bg-gray-50">Từ khóa</th>
                            <th class="px-6 py-3 bg-gray-50">Kết quả trả về</th>
                            <th class="px-6 py-3 bg-gray-50">Người dùng</th>
                            <th class="px-6 py-3 bg-gray-50">IP</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($recentSearches as $log)
                            <tr class="hover:bg-gray-50">
                                {{-- Sửa format ngày tháng cho gọn hơn nếu cần --}}
                                <td class="px-6 py-4 text-gray-500 whitespace-nowrap">
                                    {{ $log->created_at->format('H:i d/m/Y') }}</td>
                                <td class="px-6 py-4 font-medium text-gray-900">{{ $log->keyword }}</td>
                                <td class="px-6 py-4">
                                    @if($log->results_count > 0)
                                        <span class="text-green-600">{{ $log->results_count }} sản phẩm</span>
                                    @else
                                        <span class="text-red-500 font-bold">0 kết quả</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($log->user)
                                        <div class="flex items-center gap-2">
                                            <div
                                                class="w-6 h-6 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 text-xs flex-shrink-0">
                                                {{ substr($log->user->name, 0, 1) }}
                                            </div>
                                            <span class="truncate max-w-[150px]"
                                                title="{{ $log->user->email }}">{{ $log->user->name }}</span>
                                        </div>
                                    @else
                                        {{-- Việt hóa Guest --}}
                                        <span class="text-gray-400 italic">Khách chưa đăng nhập</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-gray-400 text-xs font-mono">{{ $log->ip_address }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-gray-100 bg-white">
                {{ $recentSearches->links() }}
            </div>
        </div>
    </div>
@endsection
