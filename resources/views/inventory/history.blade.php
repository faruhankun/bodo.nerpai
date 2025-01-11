<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-white">
                <h3 class="text-lg font-bold dark:text-white mb-4">Inventory History</h3>

                <a href="{{ route('inventory.index') }}"
                    class="border rounded border-gray-400 dark:border-gray-700 p-2 ml-3 text-sm hover:underline text-gray-700 dark:text-gray-400">Back
                    to Inventory</a>
                    <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>

                <x-table-table id="search-table">
                    <x-table-thead>
                        <tr>
                            <x-table-th>Date</x-table-th>
                            <x-table-th>Product</x-table-th>
                            <x-table-th>Warehouse</x-table-th>
                            <x-table-th>Quantity</x-table-th>
                            <x-table-th>Transaction Type</x-table-th>
                            <x-table-th>Room</x-table-th>
                            <x-table-th>Rack</x-table-th>
                            <x-table-th>Notes</x-table-th>
                        </tr>
                    </x-table-thead>
                    <x-table-tbody>
                        @foreach($history as $entry)
                            <x-table-tr>
                                <x-table-td>{{ $entry->created_at }}</x-table-td>
                                <x-table-td>{{ $entry->product->name }}</x-table-td>
                                <x-table-td>{{ $entry->warehouse->name }}</x-table-td>
                                <x-table-td>{{ $entry->quantity }}</x-table-td>
                                <x-table-td>{{ $entry->transaction_type }}</x-table-td>
                                <x-table-td>{{ $entry->location->room ?? 'N/A' }}</x-table-td>
                                <x-table-td>{{ $entry->location->rack ?? 'N/A' }}</x-table-td>
                                <x-table-td>{{ $entry->notes ?? 'No notes' }}</x-table-td>
                            </x-table-tr>
                        @endforeach
                    </x-table-tbody>
                </x-table-table>
            </div>
        </div>
    </div>
    </div>
</x-app-layout>