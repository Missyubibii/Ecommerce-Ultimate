@props(['product'])

<div
    class="group relative bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-lg transition-all duration-300 flex flex-col h-full overflow-hidden">
    {{-- Product Image --}}
    <div class="relative w-full aspect-square overflow-hidden bg-gray-100">
        <a href="{{ route('product.show', $product->slug) }}">
            @if($product->image_url)
                <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                    class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-500">
            @else
                <div class="flex items-center justify-center h-full text-gray-400">
                    <i data-lucide="image" class="w-12 h-12"></i>
                </div>
            @endif
        </a>

        {{-- Badges (Ví dụ: Sale, New) --}}
        @if(isset($product->old_price) && $product->old_price > $product->price)
            <span class="absolute top-2 left-2 bg-red-500 text-white text-[10px] font-bold px-2 py-1 rounded">
                -{{ round((($product->old_price - $product->price) / $product->old_price) * 100) }}%
            </span>
        @endif

        {{-- Quick Action Buttons --}}
        <div
            class="absolute bottom-2 right-2 flex flex-col gap-2 translate-y-full group-hover:translate-y-0 transition-transform duration-300 opacity-0 group-hover:opacity-100">
            <button
                class="w-8 h-8 bg-white rounded-full shadow flex items-center justify-center text-gray-600 hover:bg-indigo-600 hover:text-white transition-colors"
                title="Thêm vào giỏ">
                <i data-lucide="shopping-cart" class="w-4 h-4"></i>
            </button>
            <button
                class="w-8 h-8 bg-white rounded-full shadow flex items-center justify-center text-gray-600 hover:text-red-500 transition-colors"
                title="Yêu thích">
                <i data-lucide="heart" class="w-4 h-4"></i>
            </button>
        </div>
    </div>

    {{-- Product Info --}}
    <div class="p-4 flex-1 flex flex-col">
        {{-- Category --}}
        @if($product->category)
            <p class="text-xs text-gray-500 mb-1">{{ $product->category->name }}</p>
        @endif

        {{-- Title --}}
        <h3 class="text-sm font-medium text-gray-900 mb-2 flex-1 line-clamp-2">
            <a href="{{ route('product.show', $product->slug) }}" class="hover:text-indigo-600 transition-colors">
                {{ $product->name }}
            </a>
        </h3>

        {{-- Rating (Static placeholder) --}}
        <div class="flex items-center mb-2">
            @for($i = 0; $i < 5; $i++)
                <i data-lucide="star"
                    class="w-3 h-3 {{ $i < 4 ? 'text-yellow-400 fill-yellow-400' : 'text-gray-300' }}"></i>
            @endfor
            <span class="text-xs text-gray-400 ml-1">(12)</span>
        </div>

        {{-- Price --}}
        <div class="mt-auto flex items-end gap-2">
            <span class="text-base font-bold text-red-600">
                {{ number_format($product->price, 0, ',', '.') }}₫
            </span>
            @if(isset($product->old_price) && $product->old_price > $product->price)
                <span class="text-xs text-gray-400 line-through mb-0.5">
                    {{ number_format($product->old_price, 0, ',', '.') }}₫
                </span>
            @endif
        </div>
    </div>
</div>
