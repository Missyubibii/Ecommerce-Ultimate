@extends('layouts.app')

@section('title', $category->name)

@section('content')
    <div class="bg-gray-50 py-8" x-data="categoryPage()">
        <div class="container mx-auto px-4">

            {{-- Breadcrumb --}}
            <div class="flex items-center gap-2 text-sm text-gray-500 mb-6">
                <a href="{{ route('home') }}" class="hover:text-indigo-600">Trang chủ</a>
                <i data-lucide="chevron-right" class="w-4 h-4"></i>
                <span class="font-semibold text-gray-800">{{ $category->name }}</span>
            </div>

            <div class="flex flex-col lg:flex-row gap-8">

                {{-- Sidebar Filters (Demo tĩnh) --}}
                <div class="w-full lg:w-1/4 shrink-0">
                    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-200 sticky top-24">
                        <h3 class="font-bold text-lg mb-4">Bộ lọc</h3>

                        {{-- Sort --}}
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Sắp xếp theo</label>
                            <select x-model="sort" @change="applyFilters()"
                                class="w-full border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="newest">Mới nhất</option>
                                <option value="price_asc">Giá tăng dần</option>
                                <option value="price_desc">Giá giảm dần</option>
                                <option value="name_asc">Tên A-Z</option>
                            </select>
                        </div>

                        {{-- Price Range (Demo) --}}
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Khoảng giá</label>
                            <div class="flex items-center gap-2">
                                <input type="number" x-model="priceMin" placeholder="Min"
                                    class="w-full border-gray-300 rounded-lg text-sm p-2">
                                <span>-</span>
                                <input type="number" x-model="priceMax" placeholder="Max"
                                    class="w-full border-gray-300 rounded-lg text-sm p-2">
                            </div>
                            <button @click="applyFilters()"
                                class="w-full mt-3 bg-indigo-600 text-white py-2 rounded-lg text-sm hover:bg-indigo-700 transition">Áp
                                dụng</button>
                        </div>
                    </div>
                </div>

                {{-- Product Grid --}}
                <div class="flex-1">
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-2xl font-bold text-gray-900">{{ $category->name }}</h1>
                        <span class="text-sm text-gray-500">{{ $products->total() }} sản phẩm</span>
                    </div>

                    @if($products->count() > 0)
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
                            @foreach($products as $product)
                                <div
                                    class="bg-white border border-gray-100 rounded-xl overflow-hidden hover:shadow-lg transition-all duration-300 group flex flex-col">
                                    {{-- Image --}}
                                    <a href="{{ route('product.show', $product->slug) }}"
                                        class="block relative pt-[100%] bg-white overflow-hidden">
                                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                                            class="absolute top-0 left-0 w-full h-full object-contain p-4 transition-transform duration-500 group-hover:scale-110">
                                    </a>

                                    {{-- Info --}}
                                    <div class="p-4 flex flex-col flex-grow">
                                        <h3 class="text-sm font-medium text-gray-800 line-clamp-2 mb-2 flex-grow">
                                            <a href="{{ route('product.show', $product->slug) }}"
                                                class="hover:text-indigo-600 transition-colors">
                                                {{ $product->name }}
                                            </a>
                                        </h3>

                                        <div class="mt-auto">
                                            <div class="flex items-baseline gap-2 mb-3">
                                                <span
                                                    class="text-lg font-bold text-indigo-600">{{ number_format($product->price, 0, ',', '.') }}đ</span>
                                                @if($product->cost_price > $product->price)
                                                    <span
                                                        class="text-xs text-gray-400 line-through">{{ number_format($product->cost_price, 0, ',', '.') }}đ</span>
                                                @endif
                                            </div>

                                            <button @click="addToCart({{ $product->id }})"
                                                class="w-full py-2 bg-gray-100 hover:bg-indigo-600 text-gray-700 hover:text-white font-medium text-sm rounded-lg transition-colors flex items-center justify-center gap-2">
                                                <i data-lucide="shopping-bag" class="w-4 h-4"></i>
                                                Thêm vào giỏ
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Pagination --}}
                        <div class="mt-8">
                            {{ $products->withQueryString()->links() }}
                        </div>
                    @else
                        <div class="text-center py-12 bg-white rounded-xl border border-gray-200">
                            <p class="text-gray-500">Không tìm thấy sản phẩm nào trong danh mục này.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        function categoryPage() {
            return {
                sort: '{{ request('sort', 'newest') }}',
                priceMin: '{{ request('price_min') }}',
                priceMax: '{{ request('price_max') }}',

                applyFilters() {
                    let url = new URL(window.location.href);
                    if (this.sort) url.searchParams.set('sort', this.sort);
                    if (this.priceMin) url.searchParams.set('price_min', this.priceMin);
                    if (this.priceMax) url.searchParams.set('price_max', this.priceMax);
                    window.location.href = url.toString();
                },

                async addToCart(productId) {
                    try {
                        const res = await axios.post('{{ route("cart.add") }}', {
                            product_id: productId,
                            quantity: 1
                        });
                        if (res.data.success) {
                            alert('Đã thêm vào giỏ hàng!');
                            window.location.reload();
                        }
                    } catch (e) {
                        alert('Lỗi: ' + (e.response?.data?.message || 'Có lỗi xảy ra'));
                    }
                }
            }
        }
    </script>
@endsection
