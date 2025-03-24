<x-company-layout>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-white">
                    <h3 class="text-lg font-bold dark:text-white mb-4">Manage Customer Complaints</h3>
                    <p class="text-sm dark:text-gray-200 mb-6">Manage your customer complaints.</p>

                    <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>

                    <x-table-table id="search-table">
                        <x-table-thead>
                            <tr>
                                <x-table-th>ID</x-table-th>
                                <x-table-th>Sales Order</x-table-th>
                                <x-table-th>Status</x-table-th>
                                <x-table-th>Actions</x-table-th>
                            </tr>
                        </x-table-thead>
                        <x-table-tbody>
                            @foreach($complaints as $complaint)
                                <x-table-tr>
                                    <x-table-td>{{ $complaint->id }}</x-table-td>
                                    <x-table-td>{{ $complaint->salesOrder->id }}</x-table-td>
                                    <x-table-td>{{ $complaint->status }}</x-table-td>
                                    <x-table-td>
                                        <a href="{{ route('customer_complaints.show', $complaint->id) }}"
                                            class="btn btn-info">View</a>
                                    </x-table-td>
                                </x-table-tr>
                            @endforeach
                        </x-table-tbody>
                    </x-table-table>
                </div>
            </div>
        </div>
    </div>
</x-company-layout>