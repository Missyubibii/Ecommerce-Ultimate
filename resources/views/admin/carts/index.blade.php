@extends('layouts.admin')

@section('title', 'Quản lý Giỏ hàng')
@section('header', 'Quản lý Giỏ hàng đang hoạt động')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Flash Message nếu có --}}
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="overflow-x-auto">
                        <table class="min-w-full leading-normal">
                            <thead>
                                <tr>
                                    <th
                                        class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Khách hàng
                                    </th>
                                    <th
                                        class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Loại
                                    </th>
                                    <th
                                        class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Số lượng SP
                                    </th>
                                    <th
                                        class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Tổng giá trị
                                    </th>
                                    <th
                                        class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Cập nhật
                                    </th>
                                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($carts as $cart)
                                    <tr>
                                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                            @if($cart->user_id)
                                                <div class="flex items-center">
                                                    <div class="ml-3">
                                                        <p class="text-gray-900 whitespace-no-wrap font-bold">
                                                            {{ $cart->user->name ?? 'User #' . $cart->user_id }}
                                                        </p>
                                                        <p class="text-gray-600 whitespace-no-wrap text-xs">
                                                            {{ $cart->user->email ?? '' }}
                                                        </p>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-gray-500 italic">Guest
                                                    ({{ \Illuminate\Support\Str::limit($cart->session_id, 8) }})</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center">
                                            <span
                                                class="relative inline-block px-3 py-1 font-semibold leading-tight text-{{ $cart->user_id ? 'green' : 'yellow' }}-900">
                                                <span aria-hidden="true"
                                                    class="absolute inset-0 bg-{{ $cart->user_id ? 'green' : 'yellow' }}-200 opacity-50 rounded-full"></span>
                                                <span class="relative">{{ $cart->user_id ? 'Thành viên' : 'Vãng lai' }}</span>
                                            </span>
                                        </td>
                                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center">
                                            {{ $cart->total_quantity }} <span
                                                class="text-xs text-gray-500">({{ $cart->distinct_items }} loại)</span>
                                        </td>
                                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-right font-bold">
                                            {{ number_format($cart->cart_total, 0, ',', '.') }}đ
                                        </td>
                                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center">
                                            {{ \Carbon\Carbon::parse($cart->last_updated)->diffForHumans() }}
                                        </td>
                                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-right">
                                            <a href="{{ route('admin.carts.show', ['user_id' => $cart->user_id, 'session_id' => $cart->session_id]) }}"
                                                class="text-blue-600 hover:text-blue-900 mr-2">
                                                Chi tiết
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6"
                                            class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center text-gray-500">
                                            Không có giỏ hàng nào đang hoạt động.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="mt-4 px-5 pb-5">
                            {{ $carts->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
