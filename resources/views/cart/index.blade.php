@extends('layouts.app')

@section('title', 'Giỏ hàng của bạn')

@section('content')
    <div class="bg-gray-50 min-h-screen py-10 font-sans" x-data="cartHandler()">
        <div class="container mx-auto px-4">

            {{-- Breadcrumb --}}
            <nav class="flex items-center gap-2 text-sm text-gray-500 mb-8">
                <a href="{{ route('home') }}" class="hover:text-indigo-600 transition-colors">Trang chủ</a>
                <i data-lucide="chevron-right" class="w-4 h-4"></i>
                <span class="font-semibold text-gray-900">Giỏ hàng</span>
            </nav>

            <h1 class="text-3xl font-bold text-gray-900 mb-8 flex items-center gap-3">
                Giỏ hàng của bạn
                <span
                    class="text-base font-medium text-gray-500 bg-white px-3 py-1 rounded-full shadow-sm border border-gray-100"
                    x-text="'(' + cart.count + ' sản phẩm)'" x-show="cart.count > 0"></span>
            </h1>

            {{-- Loading Overlay (Glassmorphism) --}}
            <div x-show="isLoading"
                class="fixed inset-0 bg-white/60 backdrop-blur-[2px] z-50 flex items-center justify-center transition-opacity"
                x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" style="display: none;">
                <div class="flex flex-col items-center">
                    <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-indigo-600"></div>
                    <span class="mt-2 text-sm font-medium text-indigo-600">Đang cập nhật...</span>
                </div>
            </div>

            {{-- MAIN CART CONTENT --}}
            <template x-if="cart.count > 0">
                <div class="flex flex-col lg:flex-row gap-8 items-start">

                    {{-- LEFT COLUMN: Cart Items --}}
                    <div class="w-full lg:w-2/3 space-y-6">

                        {{-- 1. Free Shipping Progress (Gamification UX) --}}
                        {{-- <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-4 flex flex-col gap-2">
                            <div class="flex justify-between text-sm font-medium">
                                <span class="text-indigo-900">
                                    Mua thêm <span class="text-indigo-600 font-bold"
                                        x-text="formatMoney(Math.max(0, 2000000 - cart.subtotal))"></span> để được Freeship
                                </span>
                                <span class="text-indigo-600"
                                    x-text="Math.min(100, Math.round((cart.subtotal / 2000000) * 100)) + '%'"></span>
                            </div>
                            <div class="w-full bg-indigo-200 rounded-full h-2.5">
                                <div class="bg-indigo-600 h-2.5 rounded-full transition-all duration-1000 ease-out"
                                    :style="'width: ' + Math.min(100, (cart.subtotal / 2000000) * 100) + '%'"></div>
                            </div>
                        </div> --}}

                        {{-- 2. Items List --}}
                        <div
                            class="bg-white shadow-sm border border-gray-200 rounded-2xl overflow-hidden divide-y divide-gray-100">
                            {{-- Header (Desktop only) --}}
                            <div
                                class="hidden md:grid grid-cols-12 gap-4 p-5 bg-gray-50 text-xs font-bold text-gray-500 uppercase tracking-wider">
                                <div class="col-span-6">Sản phẩm</div>
                                <div class="col-span-3 text-center">Số lượng</div>
                                <div class="col-span-2 text-right">Thành tiền</div>
                                <div class="col-span-1"></div>
                            </div>

                            {{-- Item Loop --}}
                            <template x-for="item in cart.items" :key="item.id">
                                <div class="p-5 grid grid-cols-1 md:grid-cols-12 gap-6 md:gap-4 items-center group transition-colors border-b border-gray-50 last:border-0"
                                    :class="{'bg-red-50/50': !item.is_available, 'hover:bg-gray-50': item.is_available}">

                                    {{-- Product Info --}}
                                    <div class="col-span-1 md:col-span-6 flex gap-4">
                                        <div
                                            class="w-20 h-20 shrink-0 bg-white rounded-xl border border-gray-100 overflow-hidden relative">
                                            <img :src="item.product.image || 'https://placehold.co/100'"
                                                class="w-full h-full object-contain p-2"
                                                :class="{'grayscale opacity-50': !item.is_available}">

                                            {{-- Badge hết hàng --}}
                                            <div x-show="!item.is_available"
                                                class="absolute inset-0 flex items-center justify-center bg-black/10">
                                                <span
                                                    class="text-[10px] font-bold bg-red-600 text-white px-1.5 py-0.5 rounded">HẾT
                                                    HÀNG</span>
                                            </div>
                                        </div>
                                        <div class="flex flex-col justify-center">
                                            <a :href="'/product/' + item.product.slug"
                                                class="font-bold text-gray-800 hover:text-indigo-600 transition-colors line-clamp-2"
                                                x-text="item.product.name"></a>

                                            {{-- Giá tiền --}}
                                            <span class="text-sm text-gray-500 mt-1"
                                                x-text="formatMoney(item.product.price)"></span>

                                            {{-- ERROR MESSAGE TỪ BACKEND --}}
                                            <div x-show="!item.is_available"
                                                class="mt-1 text-xs font-bold text-red-600 flex items-center gap-1">
                                                <i data-lucide="alert-circle" class="w-3 h-3"></i>
                                                <span x-text="item.message"></span>
                                            </div>

                                            {{-- Mobile Controls (Giữ nguyên hoặc copy từ file cũ) --}}
                                            <div class="md:hidden mt-3 flex items-center justify-between w-full gap-4">
                                                {{-- ... (Code mobile cũ) ... --}}
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Desktop Quantity --}}
                                    <div class="hidden md:flex col-span-3 justify-center">
                                        <div class="flex items-center border border-gray-200 rounded-lg bg-white shadow-sm">
                                            <button @click="updateQty(item.id, item.quantity - 1)"
                                                :disabled="!item.is_available && item.quantity <= item.stock"
                                                class="w-8 h-8 flex items-center justify-center text-gray-500 hover:bg-gray-100 hover:text-indigo-600 rounded-l-lg transition disabled:opacity-50">
                                                <i data-lucide="minus" class="w-3 h-3"></i>
                                            </button>
                                            <input type="text" readonly :value="item.quantity"
                                                class="w-10 text-center border-none p-0 text-sm font-bold text-gray-800 focus:ring-0">
                                            <button @click="updateQty(item.id, item.quantity + 1)"
                                                :disabled="!item.is_available"
                                                class="w-8 h-8 flex items-center justify-center text-gray-500 hover:bg-gray-100 hover:text-indigo-600 rounded-r-lg transition disabled:opacity-50">
                                                <i data-lucide="plus" class="w-3 h-3"></i>
                                            </button>
                                        </div>
                                    </div>

                                    {{-- Desktop Total --}}
                                    <div class="hidden md:block col-span-2 text-right">
                                        <span class="font-bold text-gray-900"
                                            :class="{'text-gray-400 line-through': !item.is_available}"
                                            x-text="formatMoney(item.total)"></span>
                                    </div>

                                    {{-- Remove Action --}}
                                    <div
                                        class="col-span-1 md:col-span-1 text-right md:text-center absolute top-4 right-4 md:static">
                                        <button @click="removeItem(item.id)"
                                            class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-50 text-gray-400 hover:bg-red-50 hover:text-red-500 transition-all">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>

                        {{-- Continue Shopping Link --}}
                        <div class="pt-4">
                            <a href="{{ url('/') }}"
                                class="inline-flex items-center gap-2 text-indigo-600 hover:text-indigo-800 font-medium transition-colors">
                                <i data-lucide="arrow-left" class="w-4 h-4"></i> Tiếp tục mua sắm
                            </a>
                        </div>
                    </div>

                    {{-- RIGHT COLUMN: Summary (Sticky) --}}
                    <div class="w-full lg:w-1/3 shrink-0">
                        <div
                            class="bg-white p-6 rounded-2xl shadow-lg shadow-gray-200/50 border border-gray-100 sticky top-24">
                            <h2 class="text-xl font-bold text-gray-900 mb-6">Tổng đơn hàng</h2>

                            {{-- Summary Lines --}}
                            <div class="space-y-4 mb-6 border-b border-gray-100 pb-6">
                                <div class="flex justify-between text-gray-600">
                                    <span>Tạm tính</span>
                                    <span class="font-bold text-gray-900" x-text="formatMoney(cart.subtotal)"></span>
                                </div>

                                {{-- Hiển thị Coupon --}}
                                <div class="flex justify-between text-green-600" x-show="coupon">
                                    <span>Giảm giá <span class="font-bold text-xs bg-green-100 px-1 py-0.5 rounded"
                                            x-text="coupon?.code"></span></span>
                                    <span class="font-bold" x-text="'-' + formatMoney(coupon?.discount_amount)"></span>
                                </div>
                            </div>

                            {{-- Coupon Input --}}
                            <div class="mb-6">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Mã giảm giá</label>
                                <div class="flex gap-2" x-show="!coupon">
                                    <input type="text" x-model="couponCode" placeholder="Nhập mã..."
                                        class="w-full rounded-lg border-gray-200 bg-gray-50 text-sm focus:border-indigo-500 focus:ring-indigo-500 uppercase">
                                    <button @click="applyCoupon()" :disabled="isLoading"
                                        class="px-4 py-2 bg-gray-800 hover:bg-gray-900 text-white text-sm font-bold rounded-lg transition-colors disabled:opacity-50">
                                        Áp dụng
                                    </button>
                                </div>
                                {{-- Error Message --}}
                                <p x-show="couponError" class="text-xs text-red-500 mt-2" x-text="couponError"></p>

                                {{-- Coupon Applied State --}}
                                <div x-show="coupon"
                                    class="flex justify-between items-center p-3 bg-green-50 border border-green-100 rounded-lg">
                                    <span class="text-sm text-green-700 font-medium flex items-center gap-2">
                                        <i data-lucide="check-circle" class="w-4 h-4"></i> Đã áp dụng mã
                                    </span>
                                    <button @click="removeCoupon()"
                                        class="text-xs text-gray-500 hover:text-red-500 underline">Gỡ bỏ</button>
                                </div>
                            </div>

                            {{-- Total --}}
                            <div class="flex justify-between items-end mb-6">
                                <span class="font-bold text-gray-800">Tổng cộng</span>
                                <div class="text-right">
                                    <span class="block text-2xl font-bold text-indigo-600"
                                        x-text="formatMoney(grandTotal)"></span>
                                    <span class="text-xs text-gray-400">(Đã bao gồm VAT)</span>
                                </div>
                            </div>

                            {{-- Checkout Button --}}
                            <a href="{{ route('checkout.index') }}"
                                class="block w-full text-center bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-xl font-bold text-lg shadow-lg shadow-indigo-200 transition-all transform hover:-translate-y-1"
                                :class="{'opacity-50 cursor-not-allowed pointer-events-none bg-gray-400 shadow-none': isCheckoutDisabled}">
                                Tiến hành thanh toán
                            </a>

                            {{-- Warning Message if disabled --}}
                            <div x-show="isCheckoutDisabled"
                                class="mt-3 p-3 bg-red-50 border border-red-100 rounded-lg flex gap-2">
                                <i data-lucide="alert-triangle" class="w-5 h-5 text-red-500 shrink-0"></i>
                                <p class="text-xs text-red-600 font-medium">
                                    Vui lòng xóa hoặc giảm số lượng các sản phẩm hết hàng (đánh dấu đỏ) để tiếp tục.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            {{-- EMPTY STATE --}}
            <template x-if="cart.count === 0">
                <div
                    class="flex flex-col items-center justify-center py-20 bg-white rounded-3xl shadow-sm border border-gray-100 text-center">
                    <div class="w-32 h-32 bg-indigo-50 rounded-full flex items-center justify-center mb-6">
                        <i data-lucide="shopping-cart" class="w-16 h-16 text-indigo-200"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Giỏ hàng của bạn đang trống</h2>
                    <p class="text-gray-500 max-w-md mb-8">Có vẻ như bạn chưa thêm sản phẩm nào vào giỏ hàng. Hãy khám phá
                        các sản phẩm tuyệt vời của chúng tôi nhé!</p>
                    <a href="{{ url('/') }}"
                        class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-lg shadow-indigo-200 transition-all transform hover:-translate-y-1 flex items-center gap-2">
                        Mua sắm ngay <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                </div>
            </template>

        </div>
    </div>

    <script>
        function cartHandler() {
            return {
                // Dữ liệu ban đầu từ Server
                cart: @json($cart),
                isLoading: false,

                // Coupon Logic
                couponCode: '',
                coupon: null, // Object: { code, discount_amount }
                couponError: '',

                init() {
                    // Nếu cần, check session storage để reload coupon đã áp dụng
                    console.log('Cart init', this.cart);
                },

                // [COMPUTED] Tính tổng tiền cuối cùng (Sau khi trừ KM)
                get grandTotal() {
                    let total = this.cart.subtotal;
                    if (this.coupon && this.coupon.discount_amount) {
                        total -= this.coupon.discount_amount;
                    }
                    return Math.max(0, total); // Không âm
                },

                // [COMPUTED] Kiểm tra xem có được phép Checkout không
                get isCheckoutDisabled() {
                    // Disable nếu giỏ trống HOẶC có bất kỳ item nào bị lỗi (hết hàng/không tồn tại)
                    return this.cart.count === 0 || this.cart.items.some(item => !item.is_available);
                },

                // Format tiền Việt Nam
                formatMoney(amount) {
                    return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount);
                },

                // 1. Cập nhật số lượng
                async updateQty(itemId, newQty) {
                    if (newQty < 1 || this.isLoading) return; // Không cho giảm dưới 1

                    this.isLoading = true;
                    try {
                        // Gọi API Update
                        let res = await axios.post(`/cart/update/${itemId}`, { quantity: newQty });

                        if (res.data.success) {
                            this.cart = res.data.data; // Cập nhật lại toàn bộ giỏ hàng từ server

                            // Dispatch event để Header cập nhật số lượng badge
                            window.dispatchEvent(new CustomEvent('cart-updated', {
                                detail: { count: this.cart.count }
                            }));
                        }
                    } catch (e) {
                        let msg = e.response?.data?.message || 'Lỗi cập nhật giỏ hàng';
                        alert(msg); // Hoặc dùng Toast notify
                    } finally {
                        this.isLoading = false;
                    }
                },

                // 2. Xóa sản phẩm
                async removeItem(itemId) {
                    if (!confirm('Bạn có chắc muốn xóa sản phẩm này?')) return;

                    this.isLoading = true;
                    try {
                        let res = await axios.post(`/cart/remove/${itemId}`);
                        if (res.data.success) {
                            this.cart = res.data.data;

                            window.dispatchEvent(new CustomEvent('cart-updated', {
                                detail: { count: this.cart.count }
                            }));
                        }
                    } catch (e) {
                        alert('Lỗi khi xóa sản phẩm');
                    } finally {
                        this.isLoading = false;
                    }
                },

                // 3. Áp dụng mã giảm giá
                async applyCoupon() {
                    if (!this.couponCode.trim()) return;

                    this.isLoading = true;
                    this.couponError = '';
                    this.coupon = null;

                    try {
                        let res = await axios.post('{{ route("cart.apply-coupon") }}', { code: this.couponCode });
                        if (res.data.success) {
                            this.coupon = res.data.data;
                            // Hiển thị thông báo thành công (nếu có component toast)
                            // alert('Áp dụng mã thành công!');
                        }
                    } catch (e) {
                        this.couponError = e.response?.data?.message || 'Mã giảm giá không hợp lệ.';
                    } finally {
                        this.isLoading = false;
                    }
                },

                removeCoupon() {
                    this.coupon = null;
                    this.couponCode = '';
                    this.couponError = '';
                }
            }
        }
    </script>
@endsection
