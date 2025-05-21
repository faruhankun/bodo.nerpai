@php
    $layout = session('layout');
@endphp
<x-dynamic-component :component="'layouts.' . $layout">
    <div class="py-12">
        <div class="max-w-7xl my-10 mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-white">
                    <h3 class="text-lg font-bold dark:text-white">Manage Inventory Transfer</h3>
                    <p class="text-sm dark:text-gray-200 mb-6">Create, edit, and manage your Transfer listings.</p>
                    <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>
                     
                    <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 mb-4">             
                        <div class="flex flex-col md:flex-row items-center space-x-3">
                                @include($view_create)
                         </div>
                    </div>
                    <x-table.table-table id="search-table">
                        <x-table.table-thead >
                            <tr>
                                <x-table.table-th>ID</x-table.table-th>
                                <x-table.table-th>Number</x-table.table-th>
                                <x-table.table-th>Tanggal</x-table.table-th>
                                <x-table.table-th>Pengirim</x-table.table-th>
                                <x-table.table-th>Penerima</x-table.table-th>
                                <x-table.table-th>Status</x-table.table-th>
                                <x-table.table-th>Notes</x-table.table-th>
                                <x-table.table-th>Actions</x-table.table-th>
                            </tr>
                        </x-table.table-thead>
                        <x-table.table-tbody>
                            @foreach ($inventory_transfers as $transfer)
                                <tr>
                                    <x-table.table-td>{{ $transfer->id }}</x-table.table-td>
                                    <x-table.table-td>{{ $transfer->number }}</x-table.table-td>
                                    <x-table.table-td>{{ $transfer->date?->format('Y-m-d') }}</x-table.table-td>
                                    <x-table.table-td>{{ $transfer->shipper_type }} : {{ $transfer->shipper?->name }}</x-table.table-td>
                                    <x-table.table-td>{{ $transfer->consignee_type }} : {{ $transfer->consignee?->name }}</x-table.table-td>
                                    <x-table.table-td>{{ $transfer->status }}</x-table.table-td>
                                    <x-table.table-td>{{ $transfer->notes }}</x-table.table-td>
                                    <x-table.table-td class="flex justify-center items-center gap-2">
                                        <div class="flex items-center space-x-2">
                                            <x-button-show :route="route('inventory_transfers.show', $transfer->id)" />
                                        </div>
                                    </x-table.table-td>
                                </tr>
                            @endforeach
                        </x-table.table-tbody>
                    </x-table.table-table>
                </div>
            </div>
        </div>
    </div>
    
</x-dynamic-component>
