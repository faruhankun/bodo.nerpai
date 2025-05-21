@php
    $layout = session('layout');
@endphp

<x-dynamic-component :component="'layouts.' . $layout">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Store Customers') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-white">
                    <h3 class="text-lg font-bold dark:text-white">Manage Store Customers</h3>
                    <p class="text-sm dark:text-gray-200 mb-6">Create, edit, and manage your store customer listings.</p>
                    <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>

                    <div
                        class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 mb-4">
                        <div class="flex flex-col md:flex-row items-center space-x-3">
                            @include('store.store_customers.create')
                        </div>
                    </div>

                    <x-table.table-table id="search-table">
                        <x-table.table-thead>
                            <tr>
                                <x-table.table-th>ID</x-table.table-th>
                                <x-table.table-th>Name</x-table.table-th>
                                <x-table.table-th>Address</x-table.table-th>
                                <x-table.table-th>Email </x-table.table-th>
                                <x-table.table-th>Phone Number</x-table.table-th>
                                <x-table.table-th>Status</x-table.table-th>
                                <x-table.table-th>Notes</x-table.table-th>
                                <x-table.table-th>Actions</x-table.table-th>
                            </tr>
                        </x-table.table-thead>
                        <x-table.table-tbody>
                            @foreach ($store_customers as $store_customer)
                                <x-table.table-tr>
                                    <x-table.table-td>{{ $store_customer->id }}</x-table.table-td>
                                    <x-table.table-td>{{ $store_customer->customer->name }}</x-table.table-td>
                                    <x-table.table-td>{{ $store_customer->customer->address }}</x-table.table-td>
                                    <x-table.table-td>{{ $store_customer->customer->email }}</x-table.table-td>
                                    <x-table.table-td>{{ $store_customer->customer->phone_number }}</x-table.table-td>
                                    <x-table.table-td>{{ $store_customer->status }}</x-table.table-td>
                                    <x-table.table-td>{{ $store_customer->notes }}</x-table.table-td>
                                    <x-table.table-td>
                                        <div class="flex items-center space-x-2">
                                            <x-button-show :route="route('store_customers.show', $store_customer->id)" />
                                            <x-button-delete :route="route('store_customers.destroy', $store_customer->id)" />
                                        </div>
                                    </x-table.table-td>
                                </x-table.table-tr>
                            @endforeach
                        </x-table.table-tbody>
                    </x-table.table-table>
                </div>
            </div>
        </div>
    </div>
</x-dynamic-component>
