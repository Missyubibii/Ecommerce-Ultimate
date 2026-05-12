@props(['activities'])

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 h-full">
    <div class="p-6 border-b border-gray-50 flex justify-between items-center">
        <h3 class="text-lg font-bold text-gray-900">Hoạt động Admin</h3>
        <a href="{{ route('admin.activity_logs.index') }}" class="text-xs font-bold text-gray-400 hover:text-indigo-600">Xem tất cả</a>
    </div>
    <div class="p-6 max-h-[300px] overflow-y-auto">
        <div class="flow-root">
            <ul role="list" class="-mb-8">
                @foreach($activities as $activity)
                    <li>
                        <div class="relative pb-8">
                            @if(!$loop->last)
                                <span class="absolute left-4 top-4 -ml-px h-full w-0.5 bg-gray-100" aria-hidden="true"></span>
                            @endif
                            <div class="relative flex space-x-3">
                                <div>
                                    <span class="h-8 w-8 rounded-full bg-gray-100 flex items-center justify-center ring-8 ring-white">
                                        <svg class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </span>
                                </div>
                                <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                    <div>
                                        <p class="text-sm text-gray-500">
                                            <span class="font-bold text-gray-900">{{ $activity->causer->name ?? 'Hệ thống' }}</span>
                                            {{ $activity->description }}
                                        </p>
                                    </div>
                                    <div class="text-right text-xs whitespace-nowrap text-gray-400">
                                        <time datetime="{{ $activity->created_at }}">{{ $activity->created_at->diffForHumans() }}</time>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
