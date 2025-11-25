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
                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-medium">Chỉnh
                    sửa</a>
                <form action="{{ route('admin.products.destroy', $product) }}" method="POST"
                    onsubmit="return confirm('Xóa sản phẩm này?');">
                    @csrf @method('DELETE')
                    <button type="submit"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm font-medium">Xóa</button>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 divide-y lg:divide-y-0 lg:divide-x divide-gray-200">
            {{-- LEFT: Images & Basic Info --}}
            <div class="lg:col-span-1 p-6 space-y-6">
                <div class="aspect-square rounded-lg overflow-hidden border border-gray-200 bg-gray-100">
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                </div>
                @if($product->images->isNotEmpty())
                    <div class="grid grid-cols-4 gap-2">
                        @foreach($product->images as $img)
                            <div class="aspect-square rounded border border-gray-200 overflow-hidden">
                                <img src="{{ $img->image_url }}" class="w-full h-full object-cover">
                            </div>
                        @endforeach
                    </div>
                @endif
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 space-y-3">
                    <div class="flex justify-between"><span class="text-gray-600">Giá bán</span><span
                            class="font-bold text-indigo-600">{{ number_format($product->price) }} đ</span></div>
                    <div class="flex justify-between"><span class="text-gray-600">Tồn kho</span><span
                            class="font-bold">{{ $product->quantity }}</span></div>
                </div>
            </div>

            {{-- RIGHT: Description --}}
            <div class="lg:col-span-2 p-6 space-y-8">
                @if($product->short_description)
                    <div>
                        <h3 class="text-sm font-bold uppercase mb-2">Mô tả ngắn</h3>
                        <div class="bg-yellow-50 p-4 rounded-lg text-sm">{{ $product->short_description }}</div>
                    </div>
                @endif

                @if(isset($product->metadata['specs']) && count($product->metadata['specs']))
                    <div>
                        <h3 class="text-sm font-bold uppercase mb-3">Thông số</h3>
                        <table class="min-w-full divide-y divide-gray-200 border">
                            <tbody class="divide-y divide-gray-200">
                                @foreach($product->metadata['specs'] as $spec)
                                    <tr>
                                        <td class="px-4 py-2 bg-gray-50 font-medium text-sm w-1/3">{{ $spec['key'] }}</td>
                                        <td class="px-4 py-2 text-sm">{{ $spec['value'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                <div>
                    <h3 class="text-sm font-bold uppercase mb-2">Chi tiết</h3>
                    <div class="prose max-w-none text-sm text-gray-600">{!! nl2br(e($product->description)) !!}</div>
                </div>
            </div>
        </div>
    </div>
@endsection
