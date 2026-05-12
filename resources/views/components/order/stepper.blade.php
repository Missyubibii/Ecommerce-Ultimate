@props(['status'])

@php
    $steps = [
        ['key' => 'pending', 'label' => 'Đặt hàng', 'icon' => 'shopping-cart'],
        ['key' => 'processing', 'label' => 'Xác nhận', 'icon' => 'check-circle'],
        ['key' => 'shipped', 'label' => 'Đang giao', 'icon' => 'truck'],
        ['key' => 'completed', 'label' => 'Hoàn thành', 'icon' => 'flag'],
    ];

    $currentStepIndex = 0;
    foreach ($steps as $index => $step) {
        if ($status === $step['key']) {
            $currentStepIndex = $index;
            break;
        }
    }
    
    // Nếu trạng thái là cancelled hoặc refunded, stepper sẽ hiển thị khác
    $isSpecialStatus = in_array($status, ['cancelled', 'refunded']);
@endphp

<div class="w-full py-6">
    <div class="flex items-center">
        @foreach ($steps as $index => $step)
            <div class="flex items-center {{ !$loop->last ? 'flex-1' : '' }}">
                {{-- Step Circle --}}
                <div class="relative flex flex-col items-center group">
                    <div class="w-10 h-10 flex items-center justify-center rounded-full border-2 transition-colors duration-500 {{ $index <= $currentStepIndex && !$isSpecialStatus ? 'bg-indigo-600 border-indigo-600 text-white shadow-lg shadow-indigo-200' : 'bg-white border-gray-300 text-gray-400' }}">
                        <i data-lucide="{{ $step['icon'] }}" class="w-5 h-5"></i>
                    </div>
                    <div class="absolute -bottom-8 w-max text-center">
                        <span class="text-[10px] font-bold uppercase tracking-wider {{ $index <= $currentStepIndex && !$isSpecialStatus ? 'text-indigo-600' : 'text-gray-400' }}">
                            {{ $step['label'] }}
                        </span>
                    </div>
                </div>

                {{-- Connector Line --}}
                @if (!$loop->last)
                    <div class="flex-1 h-0.5 mx-4 {{ $index < $currentStepIndex && !$isSpecialStatus ? 'bg-indigo-600' : 'bg-gray-200' }}"></div>
                @endif
            </div>
        @endforeach
    </div>

    @if($isSpecialStatus)
        <div class="mt-12 p-3 bg-red-50 border border-red-100 rounded-lg flex items-center justify-center gap-2">
            <i data-lucide="alert-circle" class="w-5 h-5 text-red-600"></i>
            <span class="text-sm font-bold text-red-700 uppercase">Đơn hàng này đã bị {{ $status === 'cancelled' ? 'Hủy' : 'Hoàn tiền' }}</span>
        </div>
    @endif
</div>
