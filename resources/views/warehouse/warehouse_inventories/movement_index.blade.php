<x-warehouse-layout>
    <div class="py-12">
        <div class="max-w-7xl my-10 mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                <h3 class="text-2xl font-bold dark:text-white">List Inventory Movement</h3>
                <p class="text-sm dark:text-gray-200 mb-6">Create, edit, and manage your inventory_movements listings.</p>

                    <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>

                    <!-- List Inventory Movements -->
                    <x-table-table class="table table-bordered" id="search-table">
                        <x-table-thead>
                            <tr>
                                <x-table-th>Time</x-table-th>
                                <x-table-th>Warehouse</x-table-th>
                                <x-table-th>Source</x-table-th>
                                <x-table-th>Product</x-table-th>
                                <x-table-th>Quantity</x-table-th>
                                <x-table-th>Location</x-table-th>
                                <x-table-th>Cost per Unit</x-table-th>
                                <x-table-th>Notes</x-table-th>
                            </tr>
                        </x-table-thead>
                        <x-table-tbody>
                            @foreach ($inventory_movements as $inventory_movement)
                                <x-table-tr>
                                    <x-table-td>{{ $inventory_movement->created_at }}</x-table-td>
                                    <x-table-td>{{ $inventory_movement->warehouse?->name }}</x-table-td>
                                    <x-table-td>{{ $inventory_movement->source_type ?? 'N/A' }} : {{ $inventory_movement->source?->number ?? 'N/A' }}</x-table-td>
                                    <x-table-td>{{ $inventory_movement->product?->name ?? 'N/A' }}</x-table-td>
                                    <x-table-td>{{ $inventory_movement->quantity }}</x-table-td>
                                    <x-table-td>{{ $inventory_movement->warehouse_location?->print_location() ?? 'N/A' }}</x-table-td>
                                    <x-table-td>{{ $inventory_movement->cost_per_unit }}</x-table-td>
                                    <x-table-td>{{ $inventory_movement->notes ?? 'N/A' }}</x-table-td>
                                </x-table-tr>
                            @endforeach
                        </x-table-tbody>
                    </x-table-table>
                </div>
            </div>
        </div>
    </div>
</x-warehouse-layout>