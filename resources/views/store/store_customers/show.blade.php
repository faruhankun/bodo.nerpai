@php
    $layout = session('layout');
@endphp

<x-dynamic-component :component="'layouts.' . $layout">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Store Customer Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div
                class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-2xl font-semibold text-gray-800 dark:text-gray-200 mb-6">Store Customer Information
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div
                            class="p-4 border border-gray-200 rounded-lg shadow-md dark:bg-gray-700 dark:border-gray-600">
                            <p class="text-sm text-gray-500 dark:text-gray-300">Customer Name</p>
                            <p class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ $store_customer->customer->name }}
                            </p>
                        </div>
                        <div
                            class="p-4 border border-gray-200 rounded-lg shadow-md dark:bg-gray-700 dark:border-gray-600">
                            <p class="text-sm text-gray-500 dark:text-gray-300">Status</p>
                            <p class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ $store_customer->status }}
                            </p>
                        </div>
                        <div
                            class="p-4 border border-gray-200 rounded-lg shadow-md dark:bg-gray-700 dark:border-gray-600">
                            <p class="text-sm text-gray-500 dark:text-gray-300">Notes</p>
                            <p class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ $store_customer->notes ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <h3 class="text-2xl font-semibold text-gray-800 dark:text-gray-200 my-6">Customer Information
                    </h3>

                    <x-table.table-table id="sales-customer-table">
                        <x-table.table-thead>
                            <tr>
                                <x-table.table-th>ID</x-table.table-th>
                                <x-table.table-th>Name</x-table.table-th>
                                <x-table.table-th>Address</x-table.table-th>
                                <x-table.table-th>Email </x-table.table-th>
                                <x-table.table-th>Phone Number</x-table.table-th>
                                <x-table.table-th>Notes</x-table.table-th>
                            </tr>
                        </x-table.table-thead>
                        <x-table.table-tbody>
                            <x-table.table-tr>
                                <x-table.table-td>{{ $store_customer->customer->id }}</x-table.table-td>
                                <x-table.table-td>{{ $store_customer->customer->name }}</x-table.table-td>
                                <x-table.table-td>{{ $store_customer->customer->address }}</x-table.table-td>
                                <x-table.table-td>{{ $store_customer->customer->email }}</x-table.table-td>
                                <x-table.table-td>{{ $store_customer->customer->phone_number }}</x-table.table-td>
                                <x-table.table-td>{{ $store_customer->customer->notes }}</x-table.table-td>
                            </x-table.table-tr>
                        </x-table.table-tbody>
                    </x-table.table-table>

                    <div class="flex gap-3 justify-end mt-8">
                        <a href="{{ route('store_customers.index') }}">
                            <x-secondary-button type="button">Cancel</x-secondary-button>
                        </a>
                        @include('store.store_customers.edit')
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-dynamic-component>
