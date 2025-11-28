@props(['items' => []])

<nav class="flex mb-4" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-3">
        @foreach($items as $item)
            <li class="inline-flex items-center">
                {{-- Nếu không phải item đầu tiên thì thêm dấu mũi tên --}}
                @if(!$loop->first)
                    <div class="flex items-center">
                        <i data-lucide="chevron-right" class="w-4 h-4 text-gray-400 mx-1"></i>
                    </div>
                @endif

                {{-- Item cuối cùng (Active) --}}
                @if($loop->last)
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2 truncate max-w-[200px] md:max-w-xs"
                        aria-current="page">
                        {{ $item['label'] }}
                    </span>
                @else
                    {{-- Các item trước đó (Link) --}}
                    <a href="{{ $item['url'] }}"
                        class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600 transition-colors">
                        {{-- Nếu là trang chủ thì thêm icon Home --}}
                        @if($loop->first)
                            <i data-lucide="home" class="w-4 h-4 mr-2 mb-0.5"></i>
                        @endif
                        {{ $item['label'] }}
                    </a>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
