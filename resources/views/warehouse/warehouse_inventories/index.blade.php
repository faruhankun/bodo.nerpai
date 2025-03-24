<x-warehouse-layout>
    <div class="py-12">
        <div class="max-w-7xl my-10 mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                <h3 class="text-2xl font-bold dark:text-white">Manage Inventory</h3>
                <p class="text-sm dark:text-gray-200 mb-6">Create, edit, and manage your inventories listings.</p>

                    <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 mb-4">
                    
                        <div class="w-full md:w-auto flex justify-end gap-3">
                        <a href="{{ route('inventory.adjust') }}" class="ml-2">
                                <x-secondary-button :route="route('inventory.adjust')">Adjust Inventory</x-secondary-button>
                            </a>
                            <a href="{{ route('warehouse_inventories.movement_index') }}" class="ml-2">
                                <x-secondary-button :route="route('warehouse_inventories.movement_index')">Inventory Movements</x-secondary-button>
                            </a>
                        </div>
                    </div>

                    <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>

                    <!-- Overall Stock -->
                    <h3 class="text-lg font-bold dark:text-white">Stock Overview</h3>
                    <x-table-table class="table table-bordered" id="search-table">
                        <x-table-thead>
                            <tr>
                                <x-table-th>Product</x-table-th>
                                <x-table-th>Available Stock</x-table-th>
                                <x-table-th>Location</x-table-th>
                                <x-table-th>Incoming Stock</x-table-th>
                                <x-table-th>Reserved Stock</x-table-th>
                                <x-table-th>In Transit Stock</x-table-th>
                                <x-table-th>Cost per Unit</x-table-th>
                            </tr>
                        </x-table-thead>
                        <x-table-tbody>
                            @foreach ($inventories as $inventory)
                                <x-table-tr>
                                    <x-table-td>{{ $inventory->product->name }}</x-table-td>
                                    <x-table-td>{{ $inventory->quantity }}</x-table-td>
                                    <x-table-td>{{ $inventory->warehouse_location?->print_location() ?? 'N/A' }}</x-table-td>
                                    <x-table-td>0</x-table-td> <!-- Replace with logic for incoming -->
                                    <x-table-td>{{ $inventory->reserved_quantity }}</x-table-td>
                                    <x-table-td>{{ $inventory->in_transit_quantity }}</x-table-td>
                                    <x-table-td>{{ $inventory->cost_per_unit }}</x-table-td>
                                </x-table-tr>
                            @endforeach
                        </x-table-tbody>
                    </x-table-table>
                </div>
            </div>
        </div>
    </div>
</x-warehouse-layout>