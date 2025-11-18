<!-- resources/views/address/index.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Addresses') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6 flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900">{{ __('Address Book') }}</h3>
                <button onclick="showAddAddressModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                    {{ __('Add New Address') }}
                </button>
            </div>

            @if($addresses->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($addresses as $address)
                        <div class="bg-white rounded-lg shadow p-6 {{ $address->is_default ? 'border-2 border-indigo-500' : '' }}">
                            @if($address->is_default)
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-xs text-indigo-600 font-semibold">{{ __('DEFAULT ADDRESS') }}</span>
                                </div>
                            @endif

                            <h3 class="font-semibold text-lg mb-2">{{ $address->full_name }}</h3>
                            <p class="text-gray-600 mb-1">{{ $address->phone }}</p>
                            <p class="text-gray-600 mb-1">{{ $address->address_line1 }}</p>
                            @if($address->address_line2)
                                <p class="text-gray-600 mb-1">{{ $address->address_line2 }}</p>
                            @endif
                            <p class="text-gray-600 mb-1">{{ $address->city }}, {{ $address->state }}</p>
                            <p class="text-gray-600 mb-4">{{ $address->country }} {{ $address->postal_code }}</p>

                            <div class="flex justify-between">
                                <div>
                                    @if(!$address->is_default)
                                        <form action="{{ route('address.set-default', $address) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-indigo-600 hover:text-indigo-800 text-sm">
                                                {{ __('Set as Default') }}
                                            </button>
                                        </form>
                                    @endif
                                </div>
                                <div class="space-x-2">
                                    <button onclick="editAddress({{ $address->id }})" class="text-gray-600 hover:text-gray-800">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                        </svg>
                                    </button>
                                    <form action="{{ route('address.destroy', $address) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('{{ __('Are you sure you want to delete this address?') }}')">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-lg shadow p-12 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('No addresses') }}</h3>
                    <p class="text-gray-500 mb-4">{{ __("You haven't added any addresses yet.") }}</p>
                    <button onclick="showAddAddressModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                        {{ __('Add New Address') }}
                    </button>
                </div>
            @endif
        </div>
    </div>

    <!-- Add/Edit Address Modal -->
    <div id="addressModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modalTitle">{{ __('Add New Address') }}</h3>
                <form id="addressForm" action="{{ route('address.store') }}" method="POST" class="mt-4 space-y-4">
                    @csrf
                    <input type="hidden" id="addressId" name="address_id">
                    <input type="hidden" id="formMethod" name="_method" value="POST">

                    <div>
                        <label for="full_name" class="block text-sm font-medium text-gray-700">{{ __('Full Name') }}</label>
                        <input type="text" id="full_name" name="full_name" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700">{{ __('Phone') }}</label>
                        <input type="tel" id="phone" name="phone" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>

                    <div>
                        <label for="address_line1" class="block text-sm font-medium text-gray-700">{{ __('Address Line 1') }}</label>
                        <input type="text" id="address_line1" name="address_line1" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>

                    <div>
                        <label for="address_line2" class="block text-sm font-medium text-gray-700">{{ __('Address Line 2') }} ({{ __('Optional') }})</label>
                        <input type="text" id="address_line2" name="address_line2"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>

                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700">{{ __('City') }}</label>
                        <input type="text" id="city" name="city" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>

                    <div>
                        <label for="state" class="block text-sm font-medium text-gray-700">{{ __('State') }}</label>
                        <input type="text" id="state" name="state" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>

                    <div>
                        <label for="country" class="block text-sm font-medium text-gray-700">{{ __('Country') }}</label>
                        <input type="text" id="country" name="country" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>

                    <div>
                        <label for="postal_code" class="block text-sm font-medium text-gray-700">{{ __('Postal Code') }} ({{ __('Optional') }})</label>
                        <input type="text" id="postal_code" name="postal_code"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" id="is_default" name="is_default" value="1"
                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <label for="is_default" class="ml-2 block text-sm text-gray-900">{{ __('Set as default address') }}</label>
                    </div>

                    <div class="flex justify-end space-x-2 pt-4">
                        <button type="button" onclick="closeAddressModal()"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                            {{ __('Cancel') }}
                        </button>
                        <button type="submit"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                            {{ __('Save Address') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if(session('status'))
        <div x-data="{ show: true }" x-show="show" x-transition
             x-init="setTimeout(() => show = false, 3000)"
             class="fixed bottom-4 right-4 bg-green-500 text-white px-4 py-2 rounded-md shadow-lg">
            @switch(session('status'))
                @case('address-created')
                    {{ __('Address created successfully.') }}
                    @break
                @case('address-updated')
                    {{ __('Address updated successfully.') }}
                    @break
                @case('address-deleted')
                    {{ __('Address deleted successfully.') }}
                    @break
                @case('address-default-updated')
                    {{ __('Default address updated successfully.') }}
                    @break
            @endswitch
        </div>
    @endif

    <script>
        function showAddAddressModal() {
            document.getElementById('modalTitle').textContent = '{{ __('Add New Address') }}';
            document.getElementById('addressForm').action = '{{ route('address.store') }}';
            document.getElementById('formMethod').value = 'POST';
            document.getElementById('addressForm').reset();
            document.getElementById('addressModal').classList.remove('hidden');
        }

        function editAddress(id) {
            // Fetch address data via AJAX and populate the form
            fetch(`/api/addresses/${id}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('modalTitle').textContent = '{{ __('Edit Address') }}';
                    document.getElementById('addressForm').action = `/addresses/${id}`;
                    document.getElementById('formMethod').value = 'PUT';
                    document.getElementById('addressId').value = id;

                    document.getElementById('full_name').value = data.full_name;
                    document.getElementById('phone').value = data.phone;
                    document.getElementById('address_line1').value = data.address_line1;
                    document.getElementById('address_line2').value = data.address_line2 || '';
                    document.getElementById('city').value = data.city;
                    document.getElementById('state').value = data.state;
                    document.getElementById('country').value = data.country;
                    document.getElementById('postal_code').value = data.postal_code || '';
                    document.getElementById('is_default').checked = data.is_default;

                    document.getElementById('addressModal').classList.remove('hidden');
                });
        }

        function closeAddressModal() {
            document.getElementById('addressModal').classList.add('hidden');
        }
    </script>
</x-app-layout>
