<x-company-layout>
    <div class="py-12">
        <div class="my-10 max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                        Add Customer
                    </h1>
                    <form action="{{ route('customers.store') }}" method="POST">
                        @csrf
                        <div class="flex flex-col gap-6">
                            <div>
                                <x-input-label for="name">Customer Name</x-input-label>
                                <x-input-input type="text" name="name" required x-model="name" />
                            </div>
                            <div>
                                <x-input-label for="email">Email</x-input-label>
                                <x-input-input type="email" name="email" x-model="email" />
                            </div>
                            <div>
                                <x-input-label for="phone_number">Phone Number</x-input-label>
                                <x-input-input type="text" name="phone_number" required x-model="phone_number" />
                            </div>
                            <div>
                                <x-input-label for="address">Address</x-input-label>
                                <x-input-input name="address" x-model="address" />
                            </div>
                            <div>
                                <x-input-label for="status">Status</x-input-label>
                                <x-input-select name="status" x-model="status">
                                    <x-select-option value="Active">Active</x-select-option>
                                    <x-select-option value="Inactive">Inactive</x-select-option>
                                </x-input-select>
                            </div>
                            <div>
                                <x-input-label for="notes">Note</x-input-label>
                                <x-input-textarea name="notes" x-model="notes"></x-input-textarea>
                            </div>
                            <div class="flex gap-3 justify-end">
                                <x-secondary-button>
                                    <a href="{{ route('customers.index') }}">Cancel</a>
                                </x-secondary-button>
                                <x-primary-button type="submit">Create Customer</x-primary-button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-company-layout>
