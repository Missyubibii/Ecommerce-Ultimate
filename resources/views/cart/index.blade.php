@extends('layouts.app')

@section('title', 'Giỏ hàng của bạn')

@section('content')
    <div class="container mx-auto px-4 py-8" x-data="cartHandler()">

        <div x-show="isLoading" class="fixed inset-0 bg-gray-500 bg-opacity-50 flex items-center justify-center z-50">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-white"></div>
        </div>

        <template x-if="cart.count > 0">
            <div class="flex flex-col lg:flex-row gap-8">
                <div class="w-full lg:w-2/3 bg-white shadow rounded-lg overflow-hidden">
                    <table class="w-full text-left">
                        <thead class="bg-gray-100 uppercase text-sm text-gray-600">
                            <tr>
                                <th class="px-6 py-4">Sản phẩm</th>
                                <th class="px-6 py-4 text-center">Số lượng</th>
                                <th class="px-6 py-4 text-right">Tổng</th>
                                <th class="px-6 py-4"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <template x-for="item in cart.items" :key="item.id">
                                <tr>
                                    <td class="px-6 py-4 flex items-center gap-4">
                                        <img :src="item.product.image_url || 'https://placehold.co/100'" class="w-16 h-16 object-cover rounded">
                                        <div>
                                            <p class="font-bold text-gray-800" x-text="item.product.name"></p>
                                            <p class="text-sm text-gray-500" x-text="formatMoney(item.product.price)"></p>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex items-center justify-center border rounded w-fit mx-auto">
                                            <button @click="updateQty(item.id, item.quantity - 1)" class="px-3 py-1 hover:bg-gray-100">-</button>
                                            <input type="text" readonly :value="item.quantity" class="w-10 text-center border-none focus:ring-0">
                                            <button @click="updateQty(item.id, item.quantity + 1)" class="px-3 py-1 hover:bg-gray-100">+</button>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right font-semibold" x-text="formatMoney(item.total)"></td>
                                    <td class="px-6 py-4 text-center">
                                        <button @click="removeItem(item.id)" class="text-red-500 hover:text-red-700">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <div class="w-full lg:w-1/3">
                    <div class="bg-white shadow rounded-lg p-6">
                        <h2 class="text-xl font-bold mb-4">Tổng đơn hàng</h2>
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-600">Tạm tính</span>
                            <span class="font-bold" x-text="formatMoney(cart.subtotal)"></span>
                        </div>
                        <div class="flex justify-between mb-4 border-b pb-4">
                            <span class="text-gray-600">Phí ship</span>
                            <span class="text-gray-500 italic">Tính khi thanh toán</span>
                        </div>
                        <div class="flex justify-between mb-6 text-xl font-bold text-red-600">
                            <span>Tổng cộng</span>
                            <span x-text="formatMoney(cart.subtotal)"></span>
                        </div>
                        <a href="{{ route('checkout.index') }}" class="block w-full text-center bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
                            Tiến hành thanh toán
                        </a>
                    </div>
                </div>
            </div>
        </template>

        <template x-if="cart.count === 0">
            <div class="text-center py-16 bg-white rounded shadow">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                <h2 class="text-xl font-medium text-gray-900">Giỏ hàng trống</h2>
                <a href="{{ url('/') }}" class="mt-4 inline-block text-blue-600 hover:underline">Tiếp tục mua sắm &rarr;</a>
            </div>
        </template>
    </div>

    <script>
        function cartHandler() {
            return {
                cart: @json($cart),
                isLoading: false,

                formatMoney(amount) {
                    return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount);
                },

                async updateQty(itemId, newQty) {
                    if (newQty < 1) return;
                    this.isLoading = true;
                    try {
                        let res = await axios.post(`/cart/update/${itemId}`, { quantity: newQty });
                        if(res.data.success) {
                            this.cart = res.data.data;
                        }
                    } catch(e) {
                        alert(e.response.data.message || 'Lỗi cập nhật');
                    }
                    this.isLoading = false;
                },

                async removeItem(itemId) {
                    if(!confirm('Bạn chắc chắn muốn xóa?')) return;
                    this.isLoading = true;
                    try {
                        let res = await axios.post(`/cart/remove/${itemId}`);
                        if(res.data.success) {
                            this.cart = res.data.data;
                        }
                    } catch(e) {
                        alert('Lỗi xóa item');
                    }
                    this.isLoading = false;
                }
            }
        }
    </script>
@endsection
