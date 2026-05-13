@props(['topKeywords', 'searchDays'])

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 h-full" 
    x-data="{ 
        days: @js($searchDays),
        keywords: @js($topKeywords),
        loading: false,
        async fetchKeywords(val) {
            this.loading = true;
            this.days = val;
            try {
                const res = await fetch(`{{ route('admin.dashboard') }}?search_days=${val}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();
                this.keywords = data.topKeywords;
            } catch (e) {
                console.error(e);
            } finally {
                this.loading = false;
            }
        }
    }">
    <div class="p-6 border-b border-gray-50 flex items-center justify-between">
        <h3 class="text-lg font-bold text-gray-900">Từ khóa tìm kiếm</h3>
        <div class="relative">
            <select x-model="days" @change="fetchKeywords($event.target.value)" 
                class="text-xs border-gray-200 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 py-1 pl-2 pr-8">
                <option value="1">Hôm nay</option>
                <option value="7">7 ngày</option>
                <option value="30">30 ngày</option>
            </select>
            <div x-show="loading" class="absolute -left-6 top-1.5">
                <svg class="animate-spin h-4 w-4 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
        </div>
    </div>
    <div class="p-6 max-h-[300px] overflow-y-auto">
        <div class="flex flex-wrap gap-2" x-show="keywords.length > 0">
            <template x-for="kw in keywords" :key="kw.keyword">
                <div class="px-3 py-1.5 rounded-xl bg-gray-50 border border-gray-100 flex items-center gap-2">
                    <span class="text-sm font-medium text-gray-700" x-text="`&quot;${kw.keyword}&quot;`"></span>
                    <span class="text-[10px] font-bold text-indigo-600 bg-indigo-50 px-1.5 rounded-full" x-text="kw.count"></span>
                </div>
            </template>
        </div>
        <div class="text-sm text-gray-500 text-center w-full py-4" x-show="keywords.length === 0">
            Chưa có dữ liệu tìm kiếm.
        </div>
    </div>
</div>
