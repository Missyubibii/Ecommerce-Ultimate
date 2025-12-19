@extends('layouts.admin')

@section('title', 'Chi tiết hội thoại #' . $session->id)
@section('header')
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.chat.index') }}" class="text-gray-500 hover:text-gray-700 transition">
            <i data-lucide="arrow-left" class="w-6 h-6"></i>
        </a>
        <span class="flex items-center gap-2">
            Chi tiết hội thoại <span
                class="bg-gray-200 text-gray-700 px-2 py-0.5 rounded text-sm">#{{ $session->id }}</span>
        </span>
    </div>
@endsection

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 h-[calc(100vh-180px)]">
        <div
            class="lg:col-span-3 bg-white rounded-lg shadow-sm border border-gray-200 flex flex-col h-full overflow-hidden">
            <div class="p-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center flex-shrink-0">
                <div class="flex items-center gap-2">
                    <div class="w-2.5 h-2.5 rounded-full bg-green-500 animate-pulse"></div>
                    <h3 class="font-bold text-gray-700">Nội dung cuộc trò chuyện</h3>
                </div>
                <span class="text-xs text-gray-500 flex items-center gap-1">
                    <i data-lucide="clock" class="w-3 h-3"></i>
                    {{ $session->updated_at->format('H:i d/m/Y') }}
                </span>
            </div>

            <div class="flex-1 p-6 overflow-y-auto space-y-6 bg-gray-50/50" id="chat-container">
                @forelse($session->messages as $msg)
                    <div class="flex {{ $msg->sender === 'user' ? 'justify-end' : 'justify-start' }}">
                        <div class="flex max-w-[85%] gap-3 {{ $msg->sender === 'user' ? 'flex-row-reverse' : '' }}">
                            <div
                                class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center {{ $msg->sender === 'user' ? 'bg-indigo-100 text-indigo-600' : 'bg-rose-100 text-rose-600' }} shadow-sm border border-white">
                                <i data-lucide="{{ $msg->sender === 'user' ? 'user' : 'bot' }}" class="w-4 h-4"></i>
                            </div>

                            <div class="flex flex-col {{ $msg->sender === 'user' ? 'items-end' : 'items-start' }}">
                                <div
                                    class="p-3.5 rounded-2xl text-sm leading-relaxed shadow-sm break-words {{ $msg->sender === 'user' ? 'bg-indigo-600 text-white rounded-tr-none' : 'bg-white border border-gray-200 text-gray-800 rounded-tl-none' }}">
                                    <p>{!! nl2br(e($msg->message)) !!}</p>
                                </div>
                                <span class="text-[10px] text-gray-400 mt-1 px-1">
                                    {{ $msg->created_at->format('H:i') }}
                                </span>

                                @if($msg->product_context && is_iterable($msg->product_context))
                                    <div
                                        class="mt-2 p-3 bg-white border border-gray-200 rounded-xl flex flex-col gap-2 w-full max-w-xs shadow-sm {{ $msg->sender === 'user' ? 'mr-0' : 'ml-0' }}">
                                        <p
                                            class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1 flex items-center gap-1">
                                            <i data-lucide="shopping-bag" class="w-3 h-3"></i> Sản phẩm đề xuất
                                        </p>
                                        @foreach($msg->product_context as $product)
                                            <a href="/product/{{ $product['slug'] ?? '#' }}" target="_blank"
                                                class="flex gap-3 items-center group hover:bg-gray-50 p-1 rounded transition border border-transparent hover:border-gray-200">
                                                <div
                                                    class="w-10 h-10 bg-gray-100 rounded-lg overflow-hidden border border-gray-200 flex-shrink-0">
                                                    <img src="{{ $product['image'] ?? asset('images/no-image.png') }}"
                                                        class="w-full h-full object-cover">
                                                </div>
                                                <div class="overflow-hidden min-w-0">
                                                    <div class="text-xs font-medium truncate text-gray-900 group-hover:text-indigo-600 transition"
                                                        title="{{ $product['name'] ?? '' }}">
                                                        {{ $product['name'] ?? 'Sản phẩm không tên' }}
                                                    </div>
                                                    <div class="text-xs font-bold text-rose-500">
                                                        {{ $product['price'] ?? 'Liên hệ' }}
                                                    </div>
                                                </div>
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center h-full text-gray-400">
                        <i data-lucide="message-square-dashed" class="w-16 h-16 mb-4 opacity-50"></i>
                        <p>Cuộc hội thoại trống.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="lg:col-span-1 space-y-6 h-full overflow-y-auto">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                <h4 class="font-bold text-gray-800 mb-4 flex items-center gap-2 border-b pb-2">
                    <i data-lucide="user-circle" class="w-5 h-5 text-indigo-500"></i> Thông tin khách
                </h4>
                @if($session->user)
                    <div class="space-y-3 text-sm">
                        <div>
                            <p class="text-xs text-gray-500 mb-0.5">Họ tên</p>
                            <p class="font-medium text-gray-900">{{ $session->user->name }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-0.5">Email</p>
                            <p class="font-medium text-gray-900 break-all">{{ $session->user->email }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-0.5">Ngày tham gia</p>
                            <p class="font-medium text-gray-900">{{ $session->user->created_at->format('d/m/Y') }}</p>
                        </div>
                    </div>
                @else
                    <div class="text-center py-2">
                        <div class="bg-gray-100 text-gray-600 rounded-full px-3 py-1 text-xs inline-block font-medium mb-3">
                            Khách vãng lai</div>
                        <p class="text-sm text-gray-500 italic mb-2">Chưa đăng nhập</p>
                        <div
                            class="text-[10px] text-gray-400 break-all bg-gray-50 p-2 rounded border border-gray-100 font-mono">
                            {{ $session->session_token }}
                        </div>
                    </div>
                @endif
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                <h4 class="font-bold text-gray-800 mb-4 flex items-center gap-2 border-b pb-2">
                    <i data-lucide="bar-chart-2" class="w-5 h-5 text-indigo-500"></i> Thống kê
                </h4>
                <div class="space-y-4 text-sm">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500">Bắt đầu</span>
                        <span class="font-medium text-gray-900">{{ $session->created_at->format('H:i d/m/Y') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500">Cập nhật cuối</span>
                        <span class="font-medium text-gray-900">{{ $session->updated_at->format('H:i d/m/Y') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500">Tổng tin nhắn</span>
                        <span
                            class="font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded">{{ $session->messages->count() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://unpkg.com/lucide@latest"></script>
        <script>
            lucide.createIcons();
            const chatContainer = document.getElementById('chat-container');
            if (chatContainer) {
                chatContainer.scrollTop = chatContainer.scrollHeight;
            }

            return {
                isOpen: false,
                isLoading: false,
                newMessage: '',
                unreadCount: 0,
                messages: [],

                parseMessage(text) {
                    if (!text) return '';

                    // 1. Xử lý Bảng (Table) trước
                    let tableRegex = /((?:\|.*\|\r?\n)+)/g;

                    let formatted = text.replace(tableRegex, (match) => {
                        return this.renderTable(match);
                    });

                    // 2. Xử lý In đậm: **text** -> <strong>text</strong>
                    formatted = formatted.replace(/\*\*(.*?)\*\*/g, '<strong class="font-bold text-indigo-900">$1</strong>');

                    // 3. Xử lý List gạch đầu dòng: * text -> <li>text</li>
                    formatted = formatted.replace(/^\* (.*$)/gim, '<li class="ml-4 list-disc">$1</li>');

                    // 4. Xuống dòng
                    formatted = formatted.replace(/\n/g, '<br>');

                    return formatted;
                },

                renderTable(markdownTable) {
                    const rows = markdownTable.trim().split('\n');
                    if (rows.length < 2) return markdownTable; // Không phải bảng hợp lệ

                    let html = '<div class="overflow-x-auto my-3 border border-gray-200 rounded-lg shadow-sm"><table class="min-w-full text-sm text-left">';
                    let isHeader = true;

                    rows.forEach((row, index) => {
                        if (row.includes('---')) {
                            isHeader = false;
                            return;
                        }

                        const cells = row.split('|').filter(cell => cell.trim() !== '');

                        if (index === 0) {
                            // Cột tiêu đề
                            html += '<thead class="bg-indigo-50 text-indigo-700 font-bold uppercase text-xs"><tr>';
                            cells.forEach(cell => {
                                html += `<th class="px-3 py-2 border-b border-indigo-100">${cell.trim()}</th>`;
                            });
                            html += '</tr></thead><tbody class="divide-y divide-gray-100 bg-white">';
                        } else {
                            // Cột thông tin
                            html += '<tr class="hover:bg-gray-50 transition">';
                            cells.forEach((cell, i) => {
                                const cellClass = i === 0 ? 'font-medium text-gray-900' : 'text-gray-600';
                                html += `<td class="px-3 py-2 ${cellClass}">${cell.trim()}</td>`;
                            });
                            html += '</tr>';
                        }
                    });

                    html += '</tbody></table></div>';
                    return html;
                },
            }
        </script>
    @endpush
@endsection