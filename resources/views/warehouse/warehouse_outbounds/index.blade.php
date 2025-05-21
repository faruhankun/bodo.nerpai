@php
    $session_layout = session()->get('layout');
@endphp

<x-warehouse-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-white">
                    <h3 class="text-2xl font-bold dark:text-white mb-4">Manage Warehouse Outbounds</h3>
                    <p class="text-sm dark:text-gray-200 mb-6">Manage your outbound orders.</p>

                    <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>
                    <x-table.table-table id="search-table">
                        <x-table.table-thead>
                            <tr>
                                <x-table.table-th>ID</x-table.table-th>
                                <x-table.table-th>Number</x-table.table-th>
                                <x-table.table-th>Date</x-table.table-th>
                                <x-table.table-th>Admin</x-table.table-th>
                                <x-table.table-th>Status</x-table.table-th>
                                <x-table.table-th>Notes</x-table.table-th>
                                <x-table.table-th>Actions</x-table.table-th>
                            </tr>
                        </x-table.table-thead>
                        <x-table.table-tbody>
                            @foreach($outbounds as $outbound)
                                <x-table.table-tr>
                                    <x-table.table-td>{{ $outbound->id }}</x-table.table-td>
                                    <x-table.table-td>{{ $outbound->number }}</x-table.table-td>
                                    <x-table.table-td>{{ $outbound->date?->format('Y-m-d') ?? 'N/A' }}</x-table.table-td>
                                    <x-table.table-td>{{ $outbound->employee?->companyuser->user->name ?? 'N/A' }}</x-table.table-td>
                                    <x-table.table-td>{{ $outbound->status }}</x-table.table-td>
                                    <x-table.table-td>{{ $outbound->notes }}</x-table.table-td>
                                    <x-table.table-td>
                                        <x-button-show :route="route('warehouse_outbounds.show', $outbound->id)" />
                                        <!-- <x-button-edit :route="route('warehouse_outbounds.edit', $outbound->id)" /> -->
                                    </x-table.table-td>
                                </x-table.table-tr>
                            @endforeach
                        </x-table.table-tbody>
                    </x-table.table-table>

                    <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>
                    <!-- Shipment Outgoing -->
                    <div class="my-6">
                        <h3 class="text-lg font-bold dark:text-white mb-4">Outgoing Shipments</h3>
                        <x-table.table-table id="search-table">
                            <x-table.table-thead>
                                <tr>
                                    <x-table.table-th>ID</x-table.table-th>
                                    <x-table.table-th>Consignee</x-table.table-th>
                                    <x-table.table-th>Consignee</x-table.table-th>
                                    <x-table.table-th>Transaction</x-table.table-th>
                                    <x-table.table-th>Date</x-table.table-th>
                                    <x-table.table-th>Status</x-table.table-th>
                                    <x-table.table-th>Notes</x-table.table-th>
                                    <x-table.table-th>Actions</x-table.table-th>
                                </tr>
                            </x-table.table-thead>
                            <x-table.table-tbody>
                                @foreach ($shipments_outgoing as $shipment)
                                    <x-table.table-tr>
                                        <x-table.table-td>{{ $shipment->id }}</x-table.table-td>
                                        <x-table.table-td>{{ $shipment->shipper_type }} : {{ $shipment->shipper_id }}</x-table.table-td>
                                        <x-table.table-td>{{ $shipment->consignee_type }} : {{ $shipment->consignee_id }}</x-table.table-td>
                                        <x-table.table-td>{{ $shipment->transaction_type }} : {{ $shipment->transaction_id }}</x-table.table-td>
                                        <x-table.table-td>{{ $shipment->ship_date }}</x-table.table-td>
                                        <x-table.table-td>{{ $shipment->status }}</x-table.table-td>
                                        <x-table.table-td>{{ $shipment->notes }}</x-table.table-td>
                                        <x-table.table-td>
                                            <div class="flex items-center space-x-2">
                                                <x-button-show :route="route('shipments.show', $shipment->id)" />
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
    </div>
</x-warehouse-layout>