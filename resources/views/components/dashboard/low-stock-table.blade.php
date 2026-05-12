@props(['lowStock'])

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 h-full">
    <div class="p-6 border-b border-gray-50">
        <h3 class="text-lg font-bold text-gray-900">Cảnh báo tồn kho thấp</h3>
    </div>
    <div class="p-6 space-y-4 max-h-[400px] overflow-y-auto">
        @forelse($lowStock as $product)
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></div>
                    <p class="text-sm font-medium text-gray-800 line-clamp-1">{{ $product->name }}</p>
                </div>
                <div class="px-2 py-1 rounded bg-red-50 text-red-600 text-xs font-bold">
                    Còn {{ $product->quantity }}
                </div>
            </div>
            @if(!$loop->last)
                <div class="border-b border-gray-50"></div>
            @endif
        @empty
            <p class="text-sm text-gray-500 text-center py-4">Không có sản phẩm nào sắp hết hàng.</p>
        @endforelse
    </div>
</div>
