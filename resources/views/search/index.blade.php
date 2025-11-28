@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        {{-- Breadcrumb [cite: 679] --}}
        <x-breadcrumb :items="[
            ['label' => 'Trang chủ', 'url' => route('home')],
            ['label' => 'Tìm kiếm: ' . $keyword, 'url' => '#']
        ]" />

        <div class="mt-4">
            <h1 class="text-2xl font-bold mb-6">
                Kết quả tìm kiếm cho "{{ $keyword }}"
                <span class="text-gray-500 text-base font-normal">({{ $products->total() }} sản phẩm)</span>
            </h1>

            <div class="flex flex-col md:flex-row gap-6">
                {{-- Sidebar Filter (Tái sử dụng component của Category Page) --}}
                <aside class="w-full md:w-1/4">
                    @include('partials.filters')
                </aside>

                {{-- Main Content --}}
                <div class="w-full md:w-3/4">
                    @if($products->count() > 0)
                        {{-- Grid sản phẩm --}}
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            @foreach($products as $product)
                                <x-card-product :product="$product" />
                            @endforeach
                        </div>

                        {{-- Pagination --}}
                        <div class="mt-6">
                            {{ $products->appends(request()->query())->links() }}
                        </div>
                    @else
                        {{-- Empty State --}}
                        <div class="text-center py-10 bg-gray-50 rounded-lg">
                            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <p class="text-lg text-gray-600">Rất tiếc, không tìm thấy sản phẩm nào khớp với
                                "<strong>{{ $keyword }}</strong>".</p>
                            <p class="text-sm text-gray-500 mt-2">Hãy thử từ khóa khác hoặc xem các gợi ý dưới đây.</p>
                        </div>

                        @if(isset($suggestedProducts) && $suggestedProducts->count() > 0)
                            <div class="mt-10">
                                <h3 class="text-xl font-bold mb-4">Sản phẩm bán chạy</h3>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                    @foreach($suggestedProducts as $product)
                                        <x-card-product :product="$product" />
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
