@extends('layouts.app')

@section('title', 'Chỉnh sửa hồ sơ')

@section('content')
<div x-data="{
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

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- HEADER --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Hồ sơ của bạn</h1>
                <p class="mt-1 text-sm text-gray-500">Quản lý thông tin cá nhân và địa chỉ giao hàng.</p>
            </div>
            <div class="flex-shrink-0">
                {{-- Nút Logout nhanh (Optional) --}}
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-2 px-4 rounded-lg transition duration-300 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Đăng xuất
                    </button>
                </form>
            </div>
        </div>

        {{-- MAIN GRID --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- COL 1 & 2 (LEFT): INFO & ADDRESSES --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- CARD 1: PROFILE INFO --}}
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900">Thông tin chung</h3>
                    </div>
                    <div class="p-6">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>

                {{-- CARD 2: ADDRESS BOOK (ADDRESSES) --}}
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900">Sổ địa chỉ</h3>
                        <button @click="openAdd()"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold py-2 px-3 rounded-lg transition duration-300 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            Thêm mới
                        </button>
                    </div>
                    <div class="p-6">
                        @if($addresses->isEmpty())
                            <div class="text-center py-8 text-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-300 mb-3"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Chưa có địa chỉ nào. Hãy thêm địa chỉ giao hàng.
                            </div>
                        @else
                            <div class="grid gap-4">
                                @foreach($addresses as $addr)
                                    <div
                                        class="border {{ $addr->is_default ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200' }} rounded-lg p-4 flex flex-col sm:flex-row justify-between items-start sm:items-center">
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <span class="font-bold text-gray-800">{{ $addr->full_name }}</span>
                                                <span class="text-gray-500 text-sm">| {{ $addr->phone }}</span>
                                                @if($addr->is_default)
                                                    <span
                                                        class="bg-indigo-100 text-indigo-700 text-xs px-2 py-0.5 rounded-full font-bold">Mặc
                                                        định</span>
                                                @endif
                                            </div>
                                            <p class="text-sm text-gray-600 mt-1">
                                                {{ $addr->address_line1 }}, {{ $addr->city }}, {{ $addr->state }}
                                            </p>
                                        </div>
                                        <div class="mt-3 sm:mt-0 flex gap-3">
                                            {{-- Edit Button --}}
                                            <button @click="openEdit({{ $addr }})"
                                                class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">Sửa</button>

                                            {{-- Delete Form --}}
                                            <form method="POST" action="{{ route('address.destroy', $addr->id) }}"
                                                onsubmit="return confirm('Xóa địa chỉ này?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="text-sm text-red-500 hover:text-red-700 font-medium">Xóa</button>
                                            </form>

                                            {{-- Set Default Form --}}
                                            @if(!$addr->is_default)
                                                <form method="POST" action="{{ route('address.set-default', $addr->id) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit"
                                                        class="text-sm text-gray-500 hover:text-gray-800 font-medium">Đặt mặc
                                                        định</button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

            </div>

            {{-- COL 3 (RIGHT): SECURITY --}}
            <div class="lg:col-span-1 space-y-6">

                {{-- CARD 3: PASSWORD --}}
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900">Bảo mật</h3>
                    </div>
                    <div class="p-6">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>

                {{-- CARD 4: DELETe ACCOUNT --}}
                <div class="bg-white rounded-xl shadow-lg overflow-hidden border-t-4 border-red-500">
                    <div class="px-6 py-5">
                        <h3 class="text-lg font-semibold text-red-600 mb-2">Vùng nguy hiểm</h3>
                        <p class="text-sm text-gray-600 mb-4">Xóa tài khoản và toàn bộ dữ liệu liên quan.</p>
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- =========================== --}}
    {{-- ALPINE MODAL: ADDRESS FORM --}}
    {{-- =========================== --}}
    <div x-show="showAddressModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto"
        aria-labelledby="modal-title" role="dialog" aria-modal="true">

        {{-- Backdrop --}}
        <div x-show="showAddressModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showAddressModal = false"></div>

        {{-- Modal Panel --}}
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div x-show="showAddressModal" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">

                {{-- Form --}}
                {{-- Logic: Nếu edit thì post về /address/{id}, nếu mới thì /address --}}
                <form
                    :action="isEdit ? '{{ route('address.store') }}/' + addressForm.id : '{{ route('address.store') }}'"
                    method="POST">
                    @csrf
                    {{-- Hack method PUT bằng input hidden disable nếu không phải edit --}}
                    <input type="hidden" name="_method" value="PUT" :disabled="!isEdit">

                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                <h3 class="text-xl font-semibold leading-6 text-gray-900" id="modal-title"
                                    x-text="modalTitle"></h3>
                                <div class="mt-4 space-y-4">

                                    {{-- Full Name & Phone --}}
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Họ tên</label>
                                            <input type="text" name="full_name" x-model="addressForm.full_name" required
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Số điện
                                                thoại</label>
                                            <input type="text" name="phone" x-model="addressForm.phone" required
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        </div>
                                    </div>

                                    {{-- Address Line 1 --}}
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Địa chỉ (Số nhà,
                                            đường)</label>
                                        <input type="text" name="address_line1" x-model="addressForm.address_line1"
                                            required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    </div>

                                    {{-- City & State --}}
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Tỉnh / Thành
                                                phố</label>
                                            <input type="text" name="state" x-model="addressForm.state" required
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Quận /
                                                Huyện</label>
                                            <input type="text" name="city" x-model="addressForm.city" required
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        </div>
                                    </div>

                                    {{-- Country --}}
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Quốc gia</label>
                                        <input type="text" name="country" x-model="addressForm.country" value="Vietnam"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    </div>

                                    {{-- Default Checkbox --}}
                                    <div class="flex items-center">
                                        <input id="is_default" name="is_default" type="checkbox" value="1"
                                            x-model="addressForm.is_default"
                                            class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                        <label for="is_default" class="ml-2 block text-sm text-gray-900">Đặt làm địa
                                            chỉ mặc định</label>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                        <button type="submit"
                            class="inline-flex w-full justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 sm:ml-3 sm:w-auto">Lưu
                            địa chỉ</button>
                        <button type="button" @click="showAddressModal = false"
                            class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">Hủy</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
