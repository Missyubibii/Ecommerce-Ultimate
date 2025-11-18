<aside
    class="fixed inset-y-0 left-0 z-50 flex flex-col bg-white border-r border-gray-200 shadow-lg transition-all duration-300 ease-in-out"
    :class="expanded ? 'w-64' : 'w-20'">

    <div class="flex items-center justify-center h-16 shrink-0 border-b border-gray-200 bg-indigo-600">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center text-white">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
            </svg>
            <span class="ml-3 text-xl font-bold whitespace-nowrap" x-show="expanded"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-x-2" x-transition:enter-end="opacity-100 translate-x-0">
                Ultimate Admin
            </span>
        </a>
    </div>

    @php
        $menu = [
            [
                'name' => 'Dashboard',
                'icon' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>',
                'route' => 'admin.dashboard'
            ],
            [
                'name' => 'Người dùng (Users)',
                'icon' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>',
                'route' => 'admin.users.index'
            ],
            [
                'name' => 'Danh mục (Category)',
                'icon' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>',
                'route' => 'admin.categories.index'
            ],
            [
                'name' => 'Sản phẩm (Product)',
                'icon' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>',
                'route' => 'admin.products.index'
            ],
        ];
    @endphp

    <nav class="flex-1 overflow-y-auto mt-4 px-2 space-y-1">
        @foreach ($menu as $item)
            <a href="{{ route($item['route']) }}" class="group flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-md
                        {{ request()->routeIs($item['route']) || request()->routeIs($item['route'] . '.*')
            ? 'bg-indigo-50 text-indigo-700'
            : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}
                        transition-colors duration-150" :class="expanded ? '' : 'justify-center'"
                :title="expanded ? '' : '{{ $item['name'] }}'">

                <div class="flex-shrink-0">{!! $item['icon'] !!}</div>

                <span x-show="expanded" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 -translate-x-2" x-transition:enter-end="opacity-100 translate-x-0"
                    class="whitespace-nowrap">
                    {{ $item['name'] }}
                </span>
            </a>
        @endforeach
    </nav>

    <div class="border-t border-gray-200 p-4">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="flex items-center gap-3 text-gray-600 hover:text-red-600 w-full transition-colors"
                :class="expanded ? '' : 'justify-center'">
                <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                <span x-show="expanded" class="text-sm font-medium">Đăng xuất</span>
            </button>
        </form>
    </div>
</aside>
