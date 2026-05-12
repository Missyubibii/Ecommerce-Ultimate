@props(['brands'])

@if($brands && $brands->count() >= 8)
    <div class="py-12 bg-white overflow-hidden">
        <!-- <div class="container mx-auto px-4 mb-8">
            <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                <span class="w-2 h-8 bg-indigo-600 rounded-full"></span>
                Đối tác thương hiệu
            </h2>
        </div> -->

        <div class="container mx-auto px-4">
            <div class="relative group bg-gray-50/50 rounded-2xl py-8 px-4 overflow-hidden border border-gray-100">
                {{-- Infinite Scroll Container --}}
                <div class="flex overflow-hidden space-x-12 select-none group">
                    {{-- Double the brands to create seamless loop --}}
                    <div class="flex space-x-12 animate-infinite-scroll group-hover:pause">
                        @foreach($brands as $brand)
                            <div
                                class="flex-shrink-0 w-32 h-16 flex items-center justify-center transition-all duration-300 hover:scale-110">
                                <img src="{{ $brand->logo_url }}" alt="{{ $brand->name }}" title="{{ $brand->name }}"
                                    class="max-w-full max-h-full object-contain filter drop-shadow-sm">
                            </div>
                        @endforeach
                    </div>

                    {{-- Second copy for seamless transition --}}
                    <div class="flex space-x-12 animate-infinite-scroll group-hover:pause" aria-hidden="true">
                        @foreach($brands as $brand)
                            <div
                                class="flex-shrink-0 w-32 h-16 flex items-center justify-center transition-all duration-300 hover:scale-110">
                                <img src="{{ $brand->logo_url }}" alt="{{ $brand->name }}" title="{{ $brand->name }}"
                                    class="max-w-full max-h-full object-contain filter drop-shadow-sm">
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Fade gradients for edges --}}
                <div
                    class="absolute inset-y-0 left-0 w-20 bg-gradient-to-r from-gray-50/80 to-transparent z-10 pointer-events-none">
                </div>
                <div
                    class="absolute inset-y-0 right-0 w-20 bg-gradient-to-l from-gray-50/80 to-transparent z-10 pointer-events-none">
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes infinite-scroll {
            from {
                transform: translateX(0);
            }

            to {
                transform: translateX(-100%);
            }
        }

        .animate-infinite-scroll {
            animation: infinite-scroll 30s linear infinite;
        }

        .pause {
            animation-play-state: paused;
        }
    </style>
@endif