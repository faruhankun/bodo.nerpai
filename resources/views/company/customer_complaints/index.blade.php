<x-company-layout>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-white">
                    <h3 class="text-lg font-bold dark:text-white mb-4">Manage Customer Complaints</h3>
                    <p class="text-sm dark:text-gray-200 mb-6">Manage your customer complaints.</p>

                    <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>

                    <x-table.table-table id="search-table">
                        <x-table.table-thead>
                            <tr>
                                <x-table.table-th>ID</x-table.table-th>
                                <x-table.table-th>Sales Order</x-table.table-th>
                                <x-table.table-th>Status</x-table.table-th>
                                <x-table.table-th>Actions</x-table.table-th>
                            </tr>
                        </x-table.table-thead>
                        <x-table.table-tbody>
                            @foreach($complaints as $complaint)
                                <x-table.table-tr>
                                    <x-table.table-td>{{ $complaint->id }}</x-table.table-td>
                                    <x-table.table-td>{{ $complaint->salesOrder->id }}</x-table.table-td>
                                    <x-table.table-td>{{ $complaint->status }}</x-table.table-td>
                                    <x-table.table-td>
                                        <a href="{{ route('customer_complaints.show', $complaint->id) }}"
                                            class="btn btn-info">View</a>
                                    </x-table.table-td>
                                </x-table.table-tr>
                            @endforeach
                        </x-table.table-tbody>
                    </x-table.table-table>
                </div>
            </div>
        </div>
    </div>
</x-company-layout>