@php
    $layout = session('layout');
@endphp
<x-dynamic-component :component="'layouts.' . $layout">
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-white">
                    <h1 class="text-2xl font-bold mb-6">Shipment Details: {{ $shipment->shipment_number }}</h1>
                    <div class="mb-3 mt-1 flex-grow border-t border-gray-500 dark:border-gray-700"></div>

                    <h3 class="text-lg font-bold my-3">Shipments Details</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
                        <div
                            class="p-4 bg-gray-50 border border-gray-200 rounded-lg shadow-md dark:bg-gray-700 dark:border-gray-600">
                            <p class="text-sm text-gray-500 dark:text-gray-300">Shipment Number</p>
                            <p class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ $shipment->shipment_number }}</p>
                        </div>
                        <div
                            class="p-4 bg-gray-50 border border-gray-200 rounded-lg shadow-md dark:bg-gray-700 dark:border-gray-600">
                            <p class="text-sm text-gray-500 dark:text-gray-300">Shipment Date</p>
                            <p class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ ($shipment->ship_date)?->format('Y-m-d') }}</p>
                        </div>
                        <div
                            class="p-4 bg-gray-50 border border-gray-200 rounded-lg shadow-md dark:bg-gray-700 dark:border-gray-600">
                            <p class="text-sm text-gray-500 dark:text-gray-300">Shipper</p>
                            <p class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ $shipment->shipper_type }} : {{$shipment->shipper->name ?? 'N/A' }}</p>
                        </div>
                        <div
                            class="p-4 bg-gray-50 border border-gray-200 rounded-lg shadow-md dark:bg-gray-700 dark:border-gray-600">
                            <p class="text-sm text-gray-500 dark:text-gray-300">Origin Address</p>
                            <p class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ $shipment->origin_address ?? 'N/A' }}</p>
                        </div>
                        <div
                            class="p-4 bg-gray-50 border border-gray-200 rounded-lg shadow-md dark:bg-gray-700 dark:border-gray-600">
                            <p class="text-sm text-gray-500 dark:text-gray-300">Consignee</p>
                            <p class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ $shipment->consignee_type }} : {{$shipment->consignee->name ?? 'N/A' }}</p>
                        </div>
                        <div
                            class="p-4 bg-gray-50 border border-gray-200 rounded-lg shadow-md dark:bg-gray-700 dark:border-gray-600">
                            <p class="text-sm text-gray-500 dark:text-gray-300">Destination Address</p>
                            <p class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ $shipment->destination_address ?? 'N/A' }}</p>
                        </div>
                        <div
                            class="p-4 bg-gray-50 border border-gray-200 rounded-lg shadow-md dark:bg-gray-700 dark:border-gray-600">
                            <p class="text-sm text-gray-500 dark:text-gray-300">Transaction</p>
                            <p class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ $shipment->transaction_type }} : {{$shipment->transaction->id ?? 'N/A' }}</p>
                        </div>
                        <div
                            class="p-4 bg-gray-50 border border-gray-200 rounded-lg shadow-md dark:bg-gray-700 dark:border-gray-600">
                            <p class="text-sm text-gray-500 dark:text-gray-300">Total Amount</p>
                            <p class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                Rp{{ number_format(($shipment->transaction->total_amount) ?? 0, 2) }}</p>
                        </div>
                    </div>
                    <div class="mb-3 mt-1 flex-grow border-t border-gray-500 dark:border-gray-700"></div>

                    <h3 class="text-lg font-bold my-3">Expedition Details</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
                        <div
                            class="p-4 bg-gray-50 border border-gray-200 rounded-lg shadow-md dark:bg-gray-700 dark:border-gray-600">
                            <p class="text-sm text-gray-500 dark:text-gray-300">Kurir</p>
                            <p class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ $shipment->courier->name ?? 'N/A' }}</p>
                        </div>
                        <div
                            class="p-4 bg-gray-50 border border-gray-200 rounded-lg shadow-md dark:bg-gray-700 dark:border-gray-600">
                            <p class="text-sm text-gray-500 dark:text-gray-300">Tracking Number</p>
                            <p class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ $shipment->tracking_number ?? 'N/A' }}</p>
                        </div>
                        <div
                            class="p-4 bg-gray-50 border border-gray-200 rounded-lg shadow-md dark:bg-gray-700 dark:border-gray-600">
                            <p class="text-sm text-gray-500 dark:text-gray-300">Shipping Fee</p>
                            <p class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                Rp{{ number_format(($shipment->shipping_fee) ?? 0, 2) }}</p>
                        </div>
                        <div
                            class="p-4 bg-gray-50 border border-gray-200 rounded-lg shadow-md dark:bg-gray-700 dark:border-gray-600">
                            <p class="text-sm text-gray-500 dark:text-gray-300">Payment Rules</p>
                            <p class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ $shipment->payment_rules ?? 'N/A' }}</p>
                        </div>
                        <div
                            class="p-4 bg-gray-50 border border-gray-200 rounded-lg shadow-md dark:bg-gray-700 dark:border-gray-600">
                            <p class="text-sm text-gray-500 dark:text-gray-300">Packing Quantity</p>
                            <p class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ $shipment->packing_quantity ?? '1' }}</p>
                        </div>
                        <div
                            class="p-4 bg-gray-50 border border-gray-200 rounded-lg shadow-md dark:bg-gray-700 dark:border-gray-600">
                            <p class="text-sm text-gray-500 dark:text-gray-300">Shipment Date</p>
                            <p class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ ($shipment->delivery_date)?->format('Y-m-d') }}</p>
                        </div>
                    </div>
                    <div class="mb-3 mt-1 flex-grow border-t border-gray-500 dark:border-gray-700"></div>


                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div
                            class="p-4 bg-gray-50 border border-gray-200 rounded-lg shadow-md dark:bg-gray-700 dark:border-gray-600">
                            <p class="text-sm text-gray-500 dark:text-gray-300">Status</p>
                            <p
                                class="text-lg font-medium inline-block bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">
                                {{ $shipment->status }}
                            </p>
                        </div>
                        <div
                            class="mt-6 p-4 border border-gray-100 rounded-lg shadow-md dark:bg-gray-80 dark:border-gray-600">
                            <p class="text-sm text-gray-500 dark:text-gray-300">Notes</p>
                            <p class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ $shipment->notes ?? 'No additional notes' }}</p>
                        </div>
                    </div>
                    <div class="my-6 flex-grow border-t border-gray-500 dark:border-gray-700"></div>

                    <h3 class="text-lg font-bold my-3">Shipment Confirmation</h3>
                    <div class="mb-4">
                        @if ($shipment->shipment_confirmations)
                        <x-table-table>
                            <x-table-thead>
                                <tr>
                                    <x-table-th>ID</x-table-th>
                                    <x-table-th>Consignee</x-table-th>
                                    <x-table-th>Received Time</x-table-th>
                                    <x-table-th>Team</x-table-th>
                                    <x-table-th>Consignee Name</x-table-th>
                                    <x-table-th>Notes</x-table-th>
                                    <x-table-th>Actions</x-table-th>
                                </tr>
                            </x-table-thead>
                            <x-table-tbody>
                                @foreach ($shipment->shipment_confirmations as $shipment_confirmation)
                                    <x-table-tr>
                                        <x-table-td>{{ $shipment_confirmation->id }}</x-table-td>
                                        <x-table-td>{{ $shipment_confirmation->consignee_type }} : {{ $shipment->consignee->name }}</x-table-td>
                                        <x-table-td>{{ $shipment_confirmation->received_time ?? 'N/A' }}</x-table-td>
                                        <x-table-td>{{ $shipment_confirmation->employee->companyuser->user->name }}</x-table-td>
                                        <x-table-td>{{ $shipment_confirmation->consignee_name ?? 'N/A' }}</x-table-td>
                                        <x-table-td>{{ $shipment_confirmation->notes }}</x-table-td>
                                        <x-table-td>
                                            <div class="flex items-center space-x-2">
                                                <x-button-show :route="route('shipments.confirm-show', $shipment_confirmation->id)" />
                                                <x-button-edit :route="route('shipments.confirm', $shipment_confirmation->id)" />
                                            </div>
                                        </x-table-td>
                                    </x-table-tr>
                                @endforeach
                            </x-table-tbody>
                        </x-table-table>
                        @endif
                    </div>
                    <div class="my-6 flex-grow border-t border-gray-500 dark:border-gray-700"></div>

                    <!-- Action Section -->
                    <h3 class="text-lg font-bold my-3">Actions</h3>
                    <div>
                        @if ($shipment->status == 'SHP_IN_TRANSIT' && $shipment_confirm_allowed)
                        <div class="flex justify mt-4">
                            <form action="{{ route('shipments.action', ['shipments' => $shipment->id, 'action' => 'SHP_DELIVERY_CONFIRMED']) }}" method="POST">
                                @csrf
                                @method('POST')
                                <x-primary-button type="submit">Input Shipment Confirmation</x-primary-button>
                            </form>
                        </div>
                        @endif
                    </div>

                    <div class="my-6 flex-grow border-t border-gray-500 dark:border-gray-700"></div>

                    <!-- Back Button -->
                    <x-secondary-button>
                        <a href="{{ route('shipments.index') }}">Back to List</a>
                    </x-secondary-button>
                </div>
            </div>
        </div>
    </div>
</x-dynamic-component>