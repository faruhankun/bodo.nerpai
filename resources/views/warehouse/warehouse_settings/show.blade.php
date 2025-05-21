@php
    $layout = session('layout');
@endphp

<x-dynamic-component :component="'layouts.' . $layout">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Warehouse Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div
                class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-2xl font-semibold text-gray-800 dark:text-gray-200 mb-6">Warehouse Information</h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div
                            class="p-4 border border-gray-200 rounded-lg shadow-md dark:bg-gray-700 dark:border-gray-600">
                            <p class="text-sm text-gray-500 dark:text-gray-300">Kode</p>
                            <p class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ $warehouse->code }}</p>
                        </div>
                        <div
                            class="p-4 border border-gray-200 rounded-lg shadow-md dark:bg-gray-700 dark:border-gray-600">
                            <p class="text-sm text-gray-500 dark:text-gray-300">Warehouse Name</p>
                            <p class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ $warehouse->name }}</p>
                        </div>
                        <div
                            class="p-4 border border-gray-200 rounded-lg shadow-md dark:bg-gray-700 dark:border-gray-600">
                            <p class="text-sm text-gray-500 dark:text-gray-300">Alamat</p>
                            <p class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ $warehouse->address }}</p>
                        </div>
                        <div
                            class="p-4 border border-gray-200 rounded-lg shadow-md dark:bg-gray-700 dark:border-gray-600">
                            <p class="text-sm text-gray-500 dark:text-gray-300">Valuation Method</p>
                            <p class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ $warehouse->valuation_method }}</p>
                        </div>
                        <div
                            class="p-4 border border-gray-200 rounded-lg shadow-md dark:bg-gray-700 dark:border-gray-600">
                            <p class="text-sm text-gray-500 dark:text-gray-300">Status</p>
                            <p class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ $warehouse->status }}</p>
                        </div>
                        <div
                            class="p-4 border border-gray-200 rounded-lg shadow-md dark:bg-gray-700 dark:border-gray-600">
                            <p class="text-sm text-gray-500 dark:text-gray-300">Notes</p>
                            <p class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ $warehouse->notes ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <div class="flex gap-3 justify-end mt-8">
                        <a href="{{ route('warehouses.index') }}">
                            <x-secondary-button type="button">Cancel</x-secondary-button>
                        </a>
                        <a href="{{ route('warehouses.edit', $warehouse->id) }}">
                            <x-primary-button type="button">Edit Warehouse</x-primary-button>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="py">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div
                class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-2xl font-semibold text-gray-800 dark:text-gray-200 mb-6">Warehouse Locations</h3>

                    <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 mb-4">
                        <div class="w-full md:w-auto flex justify-end">
                            <a href="">
                                <x-button-add :route="route('warehouse_locations.create')" text="Add New Location" />
                            </a>
                        </div>
                    </div>

                    <x-table.table-table id="search-table">
                        <x-table.table-thead>
                            <tr>
                                <x-table.table-th>Room</x-table.table-th>
                                <x-table.table-th>Rack</x-table.table-th>
                                <x-table.table-th>Notes</x-table.table-th>
                                <x-table.table-th>Actions</x-table.table-th>
                            </tr>
                        </x-table.table-thead>
                        <x-table.table-tbody>
                            @foreach ($warehouse->warehouse_locations as $location)
                                <x-table.table-tr>
                                    <x-table.table-td>{{ $location->room }}</x-table.table-td>
                                    <x-table.table-td>{{ $location->rack }}</x-table.table-td>
                                    <x-table.table-td>{{ $location->notes }}</x-table.table-td>
                                    <x-table.table-td>
                                        <div class="flex items-center space-x-2">
                                            <x-button-edit :route="route('warehouse_locations.edit', $location->id)" />
                                            <form action="{{ route('warehouse_locations.destroy', $location->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <x-button-delete :route="route('warehouse_locations.destroy', $location->id)" />
                                            </form>
                                        </div>
                                    </x-table.table-td>
                                </x-table.table-tr>
                            @endforeach
                        </x-table.table-tbody>
                    </x-table.table-table>

                    <div class="flex gap-3 justify mt-8">
                        <a href="{{ route('warehouses.index') }}">
                            <x-secondary-button type="button">Cancel</x-secondary-button>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-dynamic-component>
