<x-company-layout>
    <div class="py-12">
        <div class="max-w-7xl my-10 mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                        Edit Customer: {{ $customer->name }}
                    </h1>
                    <form action="{{ route('customers.update', $customer->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="flex flex-col gap-6">
                            <div>
                                <x-input-label for="name" :value="__('Customer Name')" />
                                <x-input-input type="text" name="name" x-model="name" required
                                    value="{{ $customer->name }}" />
                            </div>
                            <div>
                                <x-input-label for="email" :value="__('Email')" />
                                <x-input-input type="email" name="email" x-model="email"
                                    value="{{ $customer->email }}" required />
                            </div>
                            <div>
                                <x-input-label for="phone_number" :value="__('Phone Number')" />
                                <x-input-input type="text" name="phone_number" x-model="phone_number"
                                    value="{{ $customer->phone_number }}" required />
                            </div>
                            <div>
                                <x-input-label for="address" :value="__('Address')" />
                                <x-input-input name="address" x-model="address" value="{{ $customer->address }}" />
                            </div>
                            <div>
                                <x-input-label for="status" :value="__('Status')" />
                                <x-input-select name="status" x-model="status" required>
                                    <x-select-option value="Active" :selected="$customer->status === 'Active'">Active</x-select-option>
                                    <x-select-option value="Inactive" :selected="$customer->status === 'Inactive'">Inactive</x-select-option>
                                </x-input-select>
                            </div>
                            <div>
                                <x-input-label for="notes" :value="__('Notes')" />
                                <x-input-textarea name="notes"
                                    x-model="notes">{{ $customer->notes }}</x-input-textarea>
                            </div>
                        </div>
                        <div class="flex gap-3 justify-end mt-4">
                            <a href="{{ route('customers.index') }}">
                                <x-button type="button" variant="secondary">
                                    Cancel
                                </x-button>
                            </a>
                            <x-button type="submit" variant="primary">Update Customer</x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-company-layout>
