@props(['topProducts'])

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 h-full">
    <h3 class="text-lg font-bold text-gray-900 mb-6">Sản phẩm bán chạy</h3>
    <div class="space-y-4 max-h-[300px] overflow-y-auto pr-2">
        @foreach($topProducts as $product)
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600 font-bold text-sm">
                        {{ $loop->iteration }}
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-800 line-clamp-1">{{ $product->name }}</p>
                        <p class="text-xs text-gray-500">{{ $product->category_name ?? 'Không phân loại' }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm font-bold text-indigo-600">{{ $product->sold_count }}</p>
                    <p class="text-[10px] text-gray-400 font-medium uppercase">Đã bán</p>
                </div>
            </div>
            @if(!$loop->last)
                <div class="border-b border-gray-50"></div>
            @endif
        @endforeach
    </div>
    
    <div class="mt-6 pt-6 border-t border-gray-50">
        <canvas id="topProductsChart" class="h-32"></canvas>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('topProductsChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($topProducts->pluck('name')->map(fn($n) => strlen($n) > 15 ? substr($n, 0, 15) . '...' : $n)),
                datasets: [{
                    data: @json($topProducts->pluck('sold_count')),
                    backgroundColor: '#6366f1',
                    borderRadius: 4,
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { display: false },
                    y: { grid: { display: false }, ticks: { font: { size: 10 } } }
                }
            }
        });
    });
</script>
