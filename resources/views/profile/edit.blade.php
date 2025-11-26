@extends('layouts.app')

@section('title', 'Hồ sơ cá nhân')

@section('content')
<div class="bg-gray-50 py-10 min-h-screen" x-data="{
    showAddressModal: false,
    isEdit: false,
    modalTitle: 'Thêm địa chỉ mới',
    addressForm: {
        id: '',
        full_name: '',
        phone: '',
        address_line1: '',
        city: '',
        state: '',
        country: 'Vietnam',
        is_default: false
    },
    openAdd() {
        this.isEdit = false;
        this.modalTitle = 'Thêm địa chỉ mới';
        this.addressForm = { id: '', full_name: '{{ Auth::user()->name }}', phone: '{{ Auth::user()->phone }}', address_line1: '', city: '', state: '', country: 'Vietnam', is_default: false };
        this.showAddressModal = true;
    },
    openEdit(address) {
        this.isEdit = true;
        this.modalTitle = 'Cập nhật địa chỉ';
        // Copy object để tránh bind trực tiếp vào view khi chưa save
        this.addressForm = JSON.parse(JSON.stringify(address));
        this.showAddressModal = true;
    }
}">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row gap-6">

            {{-- SIDEBAR MENU (Đồng bộ với trang Orders) --}}
            <div class="w-full md:w-1/4">
                <div class="bg-white rounded-xl shadow-sm p-4 sticky top-24">
                    <div class="flex items-center gap-3 mb-6 p-2">
                        {{-- Avatar hiển thị ở đây --}}
                        @if(Auth::user()->avatar)
                            <img src="{{ asset('storage/' . Auth::user()->avatar) }}" class="w-12 h-12 rounded-full object-cover border border-gray-200">
                        @else
                            <div class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-lg">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                        @endif
                        <div class="overflow-hidden">
                            <p class="text-xs text-gray-500 truncate">Tài khoản của</p>
                            <p class="font-bold text-gray-900 truncate" title="{{ Auth::user()->name }}">{{ Auth::user()->name }}</p>
                        </div>
                    </div>
                    <nav class="space-y-1">
                        {{-- Route 'customer.orders.index' cần được định nghĩa --}}
                        <a href="{{ route('customer.orders.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-indigo-600 rounded-lg transition-colors">
                            <i data-lucide="package" class="w-4 h-4"></i>
                            Đơn mua
                        </a>
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium bg-indigo-50 text-indigo-700 rounded-lg transition-colors">
                            <i data-lucide="user" class="w-4 h-4"></i>
                            Tài khoản
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="w-full flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                <i data-lucide="log-out" class="w-4 h-4"></i>
                                Đăng xuất
                            </button>
                        </form>
                    </nav>
                </div>
            </div>

            {{-- MAIN CONTENT --}}
            <div class="flex-1 space-y-6">

                {{-- Card 1: Avatar & Basic Info --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-bold text-gray-900">Hồ sơ của tôi</h3>
                        <p class="text-sm text-gray-500">Quản lý thông tin hồ sơ để bảo mật tài khoản</p>
                    </div>
                    <div class="p-6">
                        <div class="flex flex-col-reverse md:flex-row gap-8">
                            {{-- Form Info --}}
                            <div class="flex-1">
                                @include('profile.partials.update-profile-information-form')
                            </div>
                            {{-- Form Avatar (Right Side) --}}
                            <div class="w-full md:w-1/3 flex flex-col items-center border-l border-gray-100 pl-0 md:pl-8">
                                @include('profile.partials.update-avatar-form')
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card 2: Address Book --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50">
                        <h3 class="text-lg font-bold text-gray-900">Sổ địa chỉ</h3>
                        <button @click="openAdd()" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium py-2 px-4 rounded-lg transition flex items-center gap-2 shadow-sm">
                            <i data-lucide="plus" class="w-4 h-4"></i>
                            Thêm địa chỉ mới
                        </button>
                    </div>
                    <div class="p-6">
                        @if($addresses->isEmpty())
                            <div class="text-center py-8">
                                <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                                    <i data-lucide="map-pin" class="w-8 h-8 text-gray-400"></i>
                                </div>
                                <p class="text-gray-500">Bạn chưa có địa chỉ nào.</p>
                            </div>
                        @else
                            <div class="space-y-4">
                                @foreach($addresses as $addr)
                                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center p-4 border {{ $addr->is_default ? 'border-indigo-500 bg-indigo-50/50' : 'border-gray-200 hover:border-gray-300' }} rounded-lg transition">
                                        <div class="space-y-1">
                                            <div class="flex items-center gap-2">
                                                <span class="font-bold text-gray-900">{{ $addr->full_name }}</span>
                                                <span class="text-gray-400">|</span>
                                                <span class="text-gray-600">{{ $addr->phone }}</span>
                                                @if($addr->is_default)
                                                    <span class="ml-2 text-[10px] font-bold uppercase text-indigo-600 border border-indigo-200 bg-white px-2 py-0.5 rounded">Mặc định</span>
                                                @endif
                                            </div>
                                            <p class="text-sm text-gray-600">{{ $addr->address_line1 }}</p>
                                            <p class="text-sm text-gray-500">{{ $addr->city }} - {{ $addr->state }} - {{ $addr->country }}</p>
                                        </div>
                                        <div class="mt-4 sm:mt-0 flex items-center gap-3">
                                            <button @click="openEdit({{ $addr }})" class="text-sm font-medium text-blue-600 hover:text-blue-800 hover:underline">Cập nhật</button>
                                            @if(!$addr->is_default)
                                                <form method="POST" action="{{ route('address.destroy', $addr->id) }}" onsubmit="return confirm('Xóa địa chỉ này?')">
                                                    @csrf @method('DELETE')
                                                    <button class="text-sm font-medium text-red-500 hover:text-red-700 hover:underline">Xóa</button>
                                                </form>
                                                <form method="POST" action="{{ route('address.set-default', $addr->id) }}">
                                                    @csrf @method('PATCH')
                                                    <button class="text-sm font-medium text-gray-500 hover:text-gray-700 border border-gray-300 px-3 py-1 rounded hover:bg-gray-50 bg-white transition">Thiết lập mặc định</button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Card 3: Security --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-bold text-gray-900">Đổi mật khẩu</h3>
                    </div>
                    <div class="p-6">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>

                {{-- Card 4: Danger Zone --}}
                <div class="bg-white rounded-xl shadow-sm border border-red-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-red-100 bg-red-50">
                        <h3 class="text-lg font-bold text-red-700">Xóa tài khoản</h3>
                    </div>
                    <div class="p-6">
                        <p class="text-sm text-gray-600 mb-4">Hành động này sẽ xóa vĩnh viễn dữ liệu của bạn và không thể khôi phục.</p>
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- MODAL ADDRESS --}}
    <div x-show="showAddressModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity backdrop-blur-sm" @click="showAddressModal = false"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative transform overflow-hidden rounded-xl bg-white shadow-2xl transition-all sm:w-full sm:max-w-lg">
                <div class="bg-white px-6 py-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-4" x-text="modalTitle"></h3>
                    <form :action="isEdit ? '{{ route('address.store') }}/' + addressForm.id : '{{ route('address.store') }}'" method="POST">
                        @csrf
                        <input type="hidden" name="_method" value="PUT" :disabled="!isEdit">

                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Họ tên</label>
                                    <input type="text" name="full_name" x-model="addressForm.full_name" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại</label>
                                    <input type="text" name="phone" x-model="addressForm.phone" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Địa chỉ cụ thể</label>
                                <input type="text" name="address_line1" x-model="addressForm.address_line1" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Số nhà, ngõ, tên đường...">
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tỉnh / Thành phố</label>
                                    <input type="text" name="city" x-model="addressForm.city" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Quận / Huyện</label>
                                    <input type="text" name="state" x-model="addressForm.state" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Quốc gia</label>
                                <input type="text" name="country" x-model="addressForm.country" value="Vietnam" class="w-full rounded-lg border-gray-300 bg-gray-50 text-gray-500 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" readonly>
                            </div>
                            <div class="flex items-center pt-2">
                                <input id="is_default" name="is_default" type="checkbox" value="1" x-model="addressForm.is_default" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <label for="is_default" class="ml-2 block text-sm text-gray-900">Đặt làm địa chỉ mặc định</label>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end gap-3">
                            <button type="button" @click="showAddressModal = false" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium text-sm transition">Hủy</button>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium text-sm shadow-sm transition">Lưu địa chỉ</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
