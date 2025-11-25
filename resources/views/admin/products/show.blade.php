@extends('layouts.admin')

@section('title', 'Chi tiết: ' . $product->name)
@section('header', 'Chi tiết sản phẩm')

@section('content')
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        {{-- Header Actions --}}
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50">
            <div>
                <h1 class="text-xl font-bold text-gray-900">{{ $product->name }}</h1>
                <span class="text-sm text-gray-500">SKU: {{ $product->sku }}</span>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.products.index') }}"
                    class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 text-sm font-medium">Quay
                    lại</a>
                <a href="{{ route('admin.products.edit', $product) }}"
                    class="px-4 py-2 bg-indigo-600 border-gray-300 text-white rounded-lg hover:bg-indigo-700 text-sm font-medium">Chỉnh
                    sửa</a>

                <button onclick="confirmDelete('{{ $product->id }}')"
                        class="px-4 py-2 bg-red-600 border border-gray-300 text-white rounded-lg hover:bg-red-700 text-sm font-medium">
                    <i data-lucide="trash" class="h-4 w-4 mr-2"></i> Xóa sản phẩm
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 divide-y lg:divide-y-0 lg:divide-x divide-gray-200">

            {{-- LEFT: Images & Basic Info --}}
            <div class="lg:col-span-1 p-6 space-y-6">
                {{-- Main Image --}}
                <div class="aspect-square rounded-lg overflow-hidden border border-gray-200 bg-gray-100">
                    {{-- DÙNG ACCESSOR image_url --}}
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                </div>

                {{-- Gallery --}}
                @if($product->images->isNotEmpty())
                    <div class="grid grid-cols-4 gap-2">
                        @foreach($product->images->sortBy('sort_order') as $img)
                            <div class="aspect-square rounded border border-gray-200 overflow-hidden relative">
                                {{-- DÙNG ACCESSOR image_url của ProductImage --}}
                                <img src="{{ $img->image_url }}" alt="Gallery Image" class="w-full h-full object-cover">
                                @if($img->path === $product->image)
                                    <div
                                        class="absolute inset-0 bg-black/30 flex items-center justify-center text-xs text-white font-bold">
                                        Main</div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Status & Price Box --}}
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Trạng thái</span>
                        <span
                            class="px-2 py-1 text-xs font-bold rounded-full {{ $product->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-800' }}">
                            {{ ucfirst($product->status) }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Giá bán</span>
                        <span class="text-xl font-bold text-indigo-600">{{ number_format($product->price, 0, ',', '.') }}
                            đ</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500">Giá nhập</span>
                        <span class="text-gray-900">{{ number_format($product->cost_price, 0, ',', '.') }} đ</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500">Tồn kho</span>
                        <span
                            class="font-medium {{ $product->quantity <= $product->min_stock ? 'text-red-600' : 'text-gray-900' }}">
                            {{ $product->quantity }} {{ $product->unit }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- RIGHT: Description & Specs --}}
            <div class="lg:col-span-2 p-6 space-y-8">

                {{-- 1. Short Description --}}
                @if($product->short_description)
                    <div>
                        <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-2">Mô tả ngắn</h3>
                        <div class="bg-yellow-50 p-4 rounded-lg text-gray-700 text-sm border border-yellow-100">
                            {{ $product->short_description }}
                        </div>
                    </div>
                @endif

                {{-- 2. Specifications (Metadata) --}}
                @if(isset($product->metadata['specs']) && count($product->metadata['specs']) > 0)
                    <div>
                        <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-3">Thông số kỹ thuật</h3>
                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <table class="min-w-full divide-y divide-gray-200">
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    @foreach($product->metadata['specs'] as $spec)
                                        <tr>
                                            <td class="px-4 py-3 text-sm font-medium text-gray-500 w-1/3 bg-gray-50">
                                                {{ $spec['key'] }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-900">{{ $spec['value'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                {{-- 3. Detailed Description --}}
                <div>
                    <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-2">Chi tiết sản phẩm</h3>
                    <div class="prose max-w-none text-gray-600 text-sm">
                        {!! nl2br(e($product->description)) !!}
                    </div>
                </div>

            </div>
        </div>
    </div>
    @push('scripts')
    <script>
        function confirmDelete(productId) {
            showConfirmModal(
                'Xác nhận Xóa Sản phẩm',
                'Bạn có chắc chắn muốn xóa sản phẩm này? Hành động này không thể hoàn tác.',
                async () => {
                    // Logic gọi API xóa sản phẩm
                    const url = `{{ route('admin.products.destroy', ['product' => '__ID__']) }}`.replace('__ID__', productId);
                    const response = await fetch(url, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });

                    if (response.ok) {
                        showToast('Đã xóa thành công!', 'success');
                        window.location.href = "{{ route('admin.products.index') }}";
                    } else {
                        showToast('Xóa thất bại.', 'error');
                    }
                }
            );
        }
    </script>
    @endpush
@endsection
