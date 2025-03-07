@php
    $layout = session('layout');
@endphp
<x-dynamic-component :component="'layouts.' . $layout">
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-white">
                    <h3 class="text-2xl font-bold dark:text-white mb-4">Manage Store Inbounds</h3>
                    <p class="text-sm dark:text-gray-200 mb-6">Manage your inbound orders.</p>

                    <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>
                    <x-table-table id="search-table">
                        <x-table-thead>
                            <tr>
                                <x-table-th>ID</x-table-th>
                                <x-table-th>Date</x-table-th>
                                <x-table-th>Shipper</x-table-th>
                                <x-table-th>Admin</x-table-th>
                                <x-table-th>Status</x-table-th>
                                <x-table-th>Notes</x-table-th>
                                <x-table-th>Actions</x-table-th>
                            </tr>
                        </x-table-thead>
                        <x-table-tbody>
                            @foreach($inbounds as $inbound)
                                @php
                                    $shipment = $inbound->shipment_confirmation->shipment;
                                @endphp
                                <x-table-tr>
                                    <x-table-td>{{ $inbound->id }}</x-table-td>
                                    <x-table-td>{{ $inbound->date }}</x-table-td>
                                    <x-table-td>{{ $shipment->shipper_type }} : {{$shipment->shipper->name ?? 'N/A' }}</x-table-td>
                                    <x-table-td>{{ $inbound->store_employee?->employee->companyuser->user->name ?? 'N/A' }}</x-table-td>
                                    <x-table-td>{{ $inbound->status }}</x-table-td>
                                    <x-table-td>{{ $inbound->notes }}</x-table-td>
                                    <x-table-td>
                                        <x-button-show :route="route('store_inbounds.show', $inbound->id)" />
                                        @if ($inbound->status == 'INB_REQUEST')
                                            <x-button-edit :route="route('store_inbounds.edit', $inbound->id)" />
                                        @endif
                                    </x-table-td>
                                </x-table-tr>
                            @endforeach
                        </x-table-tbody>
                    </x-table-table>

                    <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>
                    <!-- Shipment Incoming -->
                    <div class="my-6">
                        <h3 class="text-lg font-bold dark:text-white mb-4">Incoming Shipments</h3>
                        <x-table-table id="search-table">
                            <x-table-thead>
                                <tr>
                                    <x-table-th>ID</x-table-th>
                                    <x-table-th>Shipper</x-table-th>
                                    <x-table-th>Consignee</x-table-th>
                                    <x-table-th>Transaction</x-table-th>
                                    <x-table-th>Date</x-table-th>
                                    <x-table-th>Status</x-table-th>
                                    <x-table-th>Notes</x-table-th>
                                    <x-table-th>Actions</x-table-th>
                                </tr>
                            </x-table-thead>
                            <x-table-tbody>
                                @foreach ($shipments_incoming as $shipment)
                                    <x-table-tr>
                                        <x-table-td>{{ $shipment->id }}</x-table-td>
                                        <x-table-td>{{ $shipment->shipper_type }} : {{ $shipment->shipper_id }}</x-table-td>
                                        <x-table-td>{{ $shipment->consignee_type }} : {{ $shipment->consignee_id }}</x-table-td>
                                        <x-table-td>{{ $shipment->transaction_type }} : {{ $shipment->transaction_id }}</x-table-td>
                                        <x-table-td>{{ $shipment->ship_date }}</x-table-td>
                                        <x-table-td>{{ $shipment->status }}</x-table-td>
                                        <x-table-td>{{ $shipment->notes }}</x-table-td>
                                        <x-table-td>
                                            <div class="flex items-center space-x-2">
                                                <x-button-show :route="route('shipments.show', $shipment->id)" />
                                            </div>
                                        </x-table-td>
                                    </x-table-tr>
                                @endforeach
                            </x-table-tbody>
                        </x-table-table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-dynamic-component>