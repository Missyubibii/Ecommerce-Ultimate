<div x-data="chatWidget()" x-init="initChat()" class="fixed bottom-5 right-5 z-50 font-sans" style="display: none;"
    x-show="true" x-transition.opacity.duration.500ms>
    <button @click="isOpen = !isOpen"
        class="bg-blue-600 hover:bg-blue-700 text-white rounded-full p-4 shadow-lg transition-transform transform hover:scale-110 flex items-center justify-center relative group">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
        </svg>
        <span
            class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold w-5 h-5 flex items-center justify-center rounded-full animate-bounce"
            x-show="!isOpen && unreadCount > 0" x-text="unreadCount"></span>
    </button>

    <div x-show="isOpen" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-10 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-10 scale-95"
        class="absolute bottom-20 right-0 w-80 md:w-96 bg-white rounded-xl shadow-2xl overflow-hidden border border-gray-200 flex flex-col"
        style="height: 500px; max-height: 80vh; display: none;">
        <div
            class="bg-blue-600 text-white p-4 flex justify-between items-center bg-gradient-to-r from-blue-600 to-blue-500">
            <div class="flex items-center space-x-2">
                <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-md leading-tight">Trợ lý ảo AI</h3>
                    <p class="text-xs text-blue-100">Hỗ trợ 24/7</p>
                </div>
            </div>

            <div class="flex items-center gap-1">
                <button @click="clearHistory"
                    class="text-blue-100 hover:text-white hover:bg-blue-700/50 p-1.5 rounded-full transition focus:outline-none"
                    title="Xóa cuộc trò chuyện">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>

                <button @click="isOpen = false"
                    class="text-blue-100 hover:text-white hover:bg-blue-700/50 p-1 rounded-full transition focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <div class="flex-1 p-4 overflow-y-auto bg-gray-50 space-y-4 scroll-smooth" id="chat-messages">
            <template x-for="msg in messages" :key="msg.id">
                <div :class="msg.sender === 'user' ? 'flex justify-end' : 'flex justify-start'">
                    <div :class="msg.sender === 'user' ? 'bg-blue-600 text-white rounded-t-xl rounded-bl-xl' : 'bg-white text-gray-800 border border-gray-200 rounded-t-xl rounded-br-xl shadow-sm'"
                        class="max-w-[85%] p-3 text-sm leading-relaxed">
                        <p x-html="parseMessage(msg.message)"></p>

                        <template x-if="msg.products && msg.products.length > 0">
                            <div class="mt-3 space-y-2 border-t pt-2 border-dashed border-gray-300">
                                <p class="text-xs font-semibold text-gray-500 mb-1">Sản phẩm gợi ý:</p>
                                <template x-for="product in msg.products" :key="product.id">
                                    <div class="flex items-start bg-gray-50 rounded p-2 border border-gray-100 hover:bg-white hover:border-blue-300 hover:shadow transition cursor-pointer group"
                                        @click="window.location.href = '/product/' + product.slug">
                                        <div
                                            class="w-12 h-12 flex-shrink-0 bg-white rounded overflow-hidden border border-gray-200">
                                            <img :src="product.image" alt=""
                                                class="w-full h-full object-cover group-hover:scale-105 transition">
                                        </div>
                                        <div class="ml-2">
                                            <p class="text-xs font-bold text-gray-900 group-hover:text-blue-600 transition"
                                                x-text="product.name"></p>
                                            <p class="text-xs text-red-600 font-semibold mt-0.5" x-text="product.price">
                                            </p>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>
            </template>

            <div x-show="isLoading" class="flex justify-start">
                <div class="flex space-x-1 bg-gray-200 rounded-full py-2 px-3 items-center">
                    <div class="w-2 h-2 bg-gray-500 rounded-full animate-bounce"></div>
                    <div class="w-2 h-2 bg-gray-500 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                    <div class="w-2 h-2 bg-gray-500 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                </div>
            </div>
        </div>

        <div class="p-3 bg-white border-t border-gray-100">
            <form @submit.prevent="sendMessage" class="flex gap-2">
                <input type="text" x-model="newMessage" placeholder="Nhập tin nhắn..."
                    class="flex-1 rounded-full border-gray-300 bg-gray-50 shadow-inner focus:bg-white focus:border-blue-500 focus:ring focus:ring-blue-100 focus:ring-opacity-50 text-sm px-4 py-2 transition">
                <button type="submit" :disabled="isLoading || !newMessage.trim()"
                    class="bg-blue-600 text-white rounded-full p-2 w-10 h-10 flex items-center justify-center hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition shadow-md transform active:scale-95">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                        class="w-5 h-5 ml-0.5">
                        <path
                            d="M3.478 2.405a.75.75 0 00-.926.94l2.432 7.905H13.5a.75.75 0 010 1.5H4.984l-2.432 7.905a.75.75 0 00.926.94 60.519 60.519 0 0018.445-8.986.75.75 0 000-1.218A60.517 60.517 0 003.478 2.405z" />
                    </svg>
                </button>
            </form>
        </div>
    </div>

    <script>
        function chatWidget() {
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
                        // Bỏ qua dòng phân cách |---|---|
                        if (row.includes('---')) {
                            isHeader = false;
                            return;
                        }

                        // Tách các ô (cells) bằng dấu | và lọc bỏ phần rỗng ở đầu/cuối
                        const cells = row.split('|').filter(cell => cell.trim() !== '');

                        if (index === 0) {
                            // Header Row
                            html += '<thead class="bg-indigo-50 text-indigo-700 font-bold uppercase text-xs"><tr>';
                            cells.forEach(cell => {
                                html += `<th class="px-3 py-2 border-b border-indigo-100">${cell.trim()}</th>`;
                            });
                            html += '</tr></thead><tbody class="divide-y divide-gray-100 bg-white">';
                        } else {
                            // Body Row
                            html += '<tr class="hover:bg-gray-50 transition">';
                            cells.forEach((cell, i) => {
                                // Cột đầu tiên in đậm nhẹ để làm tiêu đề hàng
                                const cellClass = i === 0 ? 'font-medium text-gray-900' : 'text-gray-600';
                                html += `<td class="px-3 py-2 ${cellClass}">${cell.trim()}</td>`;
                            });
                            html += '</tr>';
                        }
                    });

                    html += '</tbody></table></div>';
                    return html;
                },

                initChat() {
                    // Load lịch sử từ server
                    this.loadHistory();

                    if (window.innerWidth < 640) {
                        this.isOpen = false;
                    }
                },

                // Hàm tải lịch sử chat
                loadHistory() {
                    fetch("{{ route('chat.history') }}")
                        .then(res => res.json())
                        .then(data => {
                            if (data.messages && data.messages.length > 0) {
                                this.messages = data.messages;
                            } else {
                                this.resetMessages();
                            }
                            this.scrollToBottom();
                        })
                        .catch(err => {
                            console.error("Lỗi tải lịch sử:", err);
                            this.resetMessages();
                        });
                },

                // Hàm xóa lịch sử
                clearHistory() {
                    if (!confirm('Bạn có chắc muốn xóa toàn bộ cuộc trò chuyện này không?')) return;

                    this.isLoading = true;
                    fetch("{{ route('chat.clear') }}", {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    })
                        .then(res => res.json())
                        .then(() => {
                            this.resetMessages();
                            this.isLoading = false;
                        })
                        .catch(err => {
                            console.error("Lỗi xóa lịch sử:", err);
                            this.isLoading = false;
                        });
                },

                resetMessages() {
                    this.messages = [
                        { id: 0, sender: 'bot', message: 'Chào bạn! Mình có thể giúp gì cho bạn hôm nay?' }
                    ];
                },

                sendMessage() {
                    const msg = this.newMessage.trim();
                    if (!msg) return;

                    this.messages.push({
                        id: Date.now(),
                        sender: 'user',
                        message: msg
                    });

                    this.newMessage = '';
                    this.isLoading = true;
                    this.scrollToBottom();

                    fetch("{{ route('chat.send') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ message: msg })
                    })
                        .then(res => res.json())
                        .then(data => {
                            let botMsg = {
                                id: Date.now() + 1,
                                sender: 'bot',
                                message: data.response,
                                products: data.products
                            };

                            // Nếu server trả về link thanh toán (logic mới thêm)
                            if (data.order_link) {
                                botMsg.message += `<br><br><a href="${data.order_link}" class="inline-block bg-red-600 text-white text-xs px-3 py-2 rounded font-bold hover:bg-red-700 transition shadow-sm">THANH TOÁN NGAY &rarr;</a>`;
                            }

                            this.messages.push(botMsg);
                            this.isLoading = false;
                            this.scrollToBottom();

                            if (!this.isOpen) this.unreadCount++;
                        })
                        .catch(err => {
                            console.error(err);
                            this.messages.push({
                                id: Date.now() + 1,
                                sender: 'bot',
                                message: 'Mất kết nối với máy chủ. Vui lòng kiểm tra mạng.'
                            });
                            this.isLoading = false;
                            this.scrollToBottom();
                        });
                },

                scrollToBottom() {
                    this.$nextTick(() => {
                        const container = document.getElementById('chat-messages');
                        container.scrollTop = container.scrollHeight;
                    });
                }
            }
        }
    </script>
</div>