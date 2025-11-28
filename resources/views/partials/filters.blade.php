<div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
    <div class="flex items-center justify-between mb-4 border-b border-gray-100 pb-2">
        <h3 class="font-bold text-gray-800">Bộ lọc tìm kiếm</h3>
        <a href="{{ route('search.index', ['q' => request('q')]) }}" class="text-xs text-red-500 hover:underline">
            Xóa bộ lọc
        </a>
    </div>

    <form action="{{ route('search.index') }}" method="GET" id="filter-form">
        {{-- Giữ lại từ khóa tìm kiếm hiện tại --}}
        <input type="hidden" name="q" value="{{ request('q') }}">

        {{-- 1. Sắp xếp --}}
        <div class="mb-6">
            <h4 class="text-sm font-semibold text-gray-700 mb-2">Sắp xếp theo</h4>
            <select name="sort" onchange="this.form.submit()" class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Mới nhất</option>
                <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Giá: Thấp đến Cao</option>
                <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Giá: Cao đến Thấp</option>
                <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Tên: A-Z</option>
            </select>
        </div>

        {{-- 2. Khoảng giá --}}
        <div class="mb-6" x-data="{ min: {{ request('price_from', 0) }}, max: {{ request('price_to', 50000000) }} }">
            <h4 class="text-sm font-semibold text-gray-700 mb-2">Khoảng giá</h4>

            <div class="flex items-center gap-2 mb-2">
                <input type="number" name="price_from" x-model="min" placeholder="Từ"
                    class="w-1/2 text-sm border-gray-300 rounded-md focus:border-indigo-500 focus:ring-indigo-500">
                <span class="text-gray-400">-</span>
                <input type="number" name="price_to" x-model="max" placeholder="Đến"
                    class="w-1/2 text-sm border-gray-300 rounded-md focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <button type="submit" class="w-full mt-2 bg-indigo-600 text-white text-sm font-medium py-2 rounded hover:bg-indigo-700 transition-colors">
                Áp dụng
            </button>
        </div>

        {{-- 3. Danh mục (Nếu có biến $menuCategories được share global) --}}
        @if(isset($menuCategories) && count($menuCategories) > 0)
            <div class="mb-6">
                <h4 class="text-sm font-semibold text-gray-700 mb-2">Danh mục</h4>
                <div class="space-y-2 max-h-60 overflow-y-auto pr-2 custom-scrollbar">
                    @foreach($menuCategories as $cat)
                        <label class="flex items-start gap-2 cursor-pointer group">
                            <input type="radio" name="category_id" value="{{ $cat->id }}"
                                {{ request('category_id') == $cat->id ? 'checked' : '' }}
                                onchange="this.form.submit()"
                                class="mt-1 w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                            <span class="text-sm text-gray-600 group-hover:text-indigo-600 {{ request('category_id') == $cat->id ? 'font-bold text-indigo-700' : '' }}">
                                {{ $cat->name }}
                            </span>
                        </label>
                        {{-- Children --}}
                        @if($cat->children->count() > 0)
                            <div class="ml-6 space-y-2 mt-1 border-l-2 border-gray-100 pl-2">
                                @foreach($cat->children as $child)
                                    <label class="flex items-start gap-2 cursor-pointer group">
                                        <input type="radio" name="category_id" value="{{ $child->id }}"
                                            {{ request('category_id') == $child->id ? 'checked' : '' }}
                                            onchange="this.form.submit()"
                                            class="mt-1 w-3 h-3 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                                        <span class="text-xs text-gray-500 group-hover:text-indigo-600 {{ request('category_id') == $child->id ? 'font-bold text-indigo-700' : '' }}">
                                            {{ $child->name }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif
    </form>
</div>
