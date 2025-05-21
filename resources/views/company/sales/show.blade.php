@php
    $sale_shipments_confirmed = true;
@endphp

<x-company-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-white">
                    <h1 class="text-2xl font-bold mb-6">Sales Details : {{ $sale->number }}</h1>
                    <div class="mb-3 mt-1 flex-grow border-t border-gray-300 dark:border-gray-700"></div>

                    <h3 class="text-lg font-bold my-3">General Informations</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-6">
                        <x-div-box-show title="SO Number">{{ $sale->number }}</x-div-box-show>
                        <x-div-box-show title="Sale Date">{{ $sale->date }}</x-div-box-show>
                        <x-div-box-show title="Admin">{{ $sale->employee->companyuser->user->name ?? 'N/A' }}</x-div-box-show>
                        <x-div-box-show title="Total Amount">{{ number_format($sale->total_amount, 2) }}</x-div-box-show>
                        <x-div-box-show title="Warehouse">{{ $sale->warehouse->name }}</x-div-box-show>
                        <x-div-box-show title="Consignee">{{ $sale?->consignee_type ?? 'N/A' }} : {{ $sale->consignee?->name ?? 'N/A' }}</x-div-box-show>
                        <x-div-box-show title="Status">{{ ucfirst($sale->status) }}</x-div-box-show>
                        <x-div-box-show title="Admin Notes">{{ $sale->admin_notes ?? 'N/A' }}</x-div-box-show>
                        <x-div-box-show title="Customer Notes">{{ $sale->customer_notes ?? 'N/A' }}</x-div-box-show>
                    </div>
                    <!-- <div class="mb-3 mt-1 flex-grow border-t border-gray-300 dark:border-gray-700"></div> -->
                    


                    <h3 class="text-lg font-bold mt-6">Items</h3>
                    <x-table.table-table id="search-table">
                        <x-table.table-thead>
                            <tr>
                                <x-table.table-th>#</x-table.table-th>
                                <x-table.table-th>Item</x-table.table-th>
                                <x-table.table-th>Inventory</x-table.table-th>
                                <x-table.table-th>Quantity</x-table.table-th>
                                <x-table.table-th>Discount</x-table.table-th>
                                <x-table.table-th>Price</x-table.table-th>
                                <x-table.table-th>Cost</x-table.table-th>
                                <x-table.table-th>Notes</x-table.table-th>
                                <x-table.table-th>Actions</x-table.table-th>
                            </tr>
                        </x-table.table-thead>
                        <x-table.table-tbody>
                            @foreach ($sale->items as $index => $item)
                                <x-table.table-tr>
                                    <x-table.table-td>{{ $item->item_id }}</x-table.table-td>
                                    <x-table.table-td>{{ $item->item_type }} : {{ $item->item?->name ?? 'N/A'}}</x-table.table-td>
                                    <x-table.table-td>{{ $item->inventory?->warehouse_location?->print_location ?? 'N/A'}}</x-table.table-td>
                                    <x-table.table-td>{{ $item->quantity ?? 'N/A'}}</x-table.table-td>
                                    <x-table.table-td>{{ number_format($item->discount ?? 0, 2) }}%</x-table.table-td>
                                    <x-table.table-td>Rp{{ number_format($item->price ?? 0, 2) }}</x-table.table-td>
                                    <x-table.table-td>Rp{{ number_format($item->cost_per_unit ?? 0, 2) }}</x-table.table-td>
                                    <x-table.table-td>{{ $item->notes ?? 'N/A' }}</x-table.table-td>
                                    <x-table.table-td>
                                        <div class="flex items-center space-x-2">
                                        </div>
                                    </x-table.table-td>
                                </x-table.table-tr>
                            @endforeach
                        </x-table.table-tbody>
                    </x-table.table-table>
                    <div class="mb-3 mt-1 flex-grow border-t border-gray-500 dark:border-gray-700"></div>
                    


                    <h3 class="text-lg font-bold my-3">Shipment Details</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-6">
                        <x-div-box-show title="Courier">{{ $sale->courier?->name ?? 'N/A' }}</x-div-box-show>
                        <x-div-box-show title="Estimated Shipping Fee">{{ number_format($sale->estimated_shipping_fee, 2) }}</x-div-box-show>
                        <x-div-box-show title="Shipping Discount">{{ number_format($sale->shipping_fee_discount, 2) }}</x-div-box-show>
                        <x-div-box-show title="Packing Fee">{{ number_format($sale->packing_fee, 2) }}</x-div-box-show>
                    </div>
                    <div class="mb-3 mt-1 flex-grow border-t border-gray-300 dark:border-gray-700"></div>
                    
                    <!-- Invoice Section -->
                    <h3 class="text-lg font-bold my-3">Invoices</h3>
                    <div class="overflow-x-auto">
                        <x-table.table-table>
                            <x-table.table-thead>
                                <tr class>
                                    <x-table.table-th>Id</x-table.table-th>
                                    <x-table.table-th>Invoice Number</x-table.table-th>
                                    <x-table.table-th>Date</x-table.table-th>
                                    <x-table.table-th>Due Date</x-table.table-th>
                                    <x-table.table-th>Total Amount</x-table.table-th>
                                    <x-table.table-th>Notes</x-table.table-th>
                                    <x-table.table-th>Status</x-table.table-th>
                                    <x-table.table-th>Actions</x-table.table-th>
                                </tr>
                            </x-table.table-thead>
                            <x-table.table-tbody>
                                @foreach ($sale->invoices as $invoice)
                                    <x-table.table-tr>
                                        <x-table.table-td>{{ $invoice->id }}</x-table.table-td>
                                        <x-table.table-td>{{ $invoice->number }}</x-table.table-td>
                                        <x-table.table-td>{{ $invoice->date?->format('Y-m-d') ?? 'N/A' }}</x-table.table-td>
                                        <x-table.table-td>{{ $invoice->due_date?->format('Y-m-d') ?? 'N/A' }}</x-table.table-td>
                                        <x-table.table-td>Rp{{ number_format($invoice->total_amount, 2) }}</x-table.table-td>
                                        <x-table.table-td>{{ $invoice->notes }}</x-table.table-td>
                                        <x-table.table-td>{{ $invoice->status }}</x-table.table-td>
                                        <x-table.table-td>
                                            <div class="flex space-x-2">
                                                <x-button-show :route="route('sale_invoices.show', $invoice->id)" />
                                                <!-- <x-button-edit :route="route('sale_invoices.edit', $invoice->id)" /> -->
                                                <!-- <x-button-delete :route="route('sale_invoices.destroy', $invoice->id)" /> -->
                                            </div>
                                        </x-table.table-td>
                                    </x-table.table-tr>
                                @endforeach
                            </x-table.table-tbody>
                        </x-table.table-table>
                    </div>
                    <div class="my-6 flex-grow border-t border-gray-500 dark:border-gray-700"></div>



                    <!-- Outbound Section -->
                    <h3 class="text-lg font-bold mt-6">Outbounds</h3>
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
                            @foreach($sale->outbounds as $outbound)
                                <x-table.table-tr>
                                    <x-table.table-td>{{ $outbound->id }}</x-table.table-td>
                                    <x-table.table-td>{{ $outbound->number }}</x-table.table-td>
                                    <x-table.table-td>{{ $outbound->date?->format('Y-m-d') ?? 'N/A' }}</x-table.table-td>
                                    <x-table.table-td>{{ $outbound->employee?->companyuser->user->name ?? 'N/A' }}</x-table.table-td>
                                    <x-table.table-td>{{ $outbound->status }}</x-table.table-td>
                                    <x-table.table-td>{{ $outbound->notes }}</x-table.table-td>
                                    <x-table.table-td>
                                        <x-button-show :route="route('warehouse_outbounds.show', $outbound->id)" />
                                    </x-table.table-td>
                                </x-table.table-tr>
                            @endforeach
                        </x-table.table-tbody>
                    </x-table.table-table>
                    <div class="my-6 flex-grow border-t border-gray-500 dark:border-gray-700"></div>



                    <!-- Shipment Section -->
                    <h3 class="text-lg font-bold my-3">Shipments</h3>
                    <div class="overflow-x-auto">
                        <x-table.table-table>
                            <x-table.table-thead>
                                <tr>
                                    <x-table.table-th>ID</x-table.table-th>
                                    <x-table.table-th>Pengirim</x-table.table-th>
                                    <x-table.table-th>Penerima</x-table.table-th>
                                    <x-table.table-th>Transaksi</x-table.table-th>
                                    <x-table.table-th>Tanggal</x-table.table-th>
                                    <x-table.table-th>Status</x-table.table-th>
                                    <x-table.table-th>Notes</x-table.table-th>
                                    <x-table.table-th>Actions</x-table.table-th>
                                </tr>
                            </x-table.table-thead>
                            <x-table.table-tbody>
                                @foreach ($sale->shipments as $shipment)
                                    @php
                                        $sale_shipments_confirmed = $shipment->status === 'SHP_DELIVERY_CONFIRMED';
                                    @endphp

                                    <x-table.table-tr>
                                        <x-table.table-td>{{ $shipment->id }}</x-table.table-td>
                                        <x-table.table-td>{{ $shipment->shipper_type }} : {{ $shipment->shipper?->name }}</x-table.table-td>
                                        <x-table.table-td>{{ $shipment->consignee_type }} : {{ $shipment->consignee?->name }}</x-table.table-td>
                                        <x-table.table-td>{{ $shipment->transaction_type }} : {{ $shipment->transaction?->number }}</x-table.table-td>
                                        <x-table.table-td>{{ $shipment->ship_date }}</x-table.table-td>
                                        <x-table.table-td>{{ $shipment->status }}</x-table.table-td>
                                        <x-table.table-td>{{ $shipment->notes }}</x-table.table-td>
                                        <x-table.table-td>
                                            <div class="flex items-center space-x-2">
                                                <x-button-show :route="route('shipments.show', $shipment->id)" />
                                                <x-button-edit :route="route('shipments.edit', $shipment->id)" />
                                            </div>
                                        </x-table.table-td>
                                    </x-table.table-tr>
                                @endforeach
                            </x-table.table-tbody>
                        </x-table.table-table>
                    </div>
                    <div class="my-6 flex-grow border-t border-gray-500 dark:border-gray-700"></div>



                    <!-- Action Section -->
                    <h3 class="text-lg font-bold my-3">Actions</h3>
                    <div>
                        @php
                            $action = '';
                            switch ($sale->status) {
                                case 'SO_OFFER':
                                    if(count($sale->items) < 1){
                                        break;
                                    }
                                    $action = 'SO_REQUEST';
                                    $submit_text = 'Kirim Penjualan ke Customer';
                                    break;
                                case 'SO_REQUEST':
                                    $action = 'SO_CONFIRMED';
                                    $submit_text = 'Input Invoice untuk Customer';
                                    break;
                                case 'SO_CONFIRMED':
                                    $action = 'SO_DP_CONFIRMED';
                                    $submit_text = 'Konfirmasi Pembayaran/DP Lunas dari Customer';
                                    break;
                                case 'SO_DP_CONFIRMED':
                                    $action = 'SO_OUTBOUND_REQUEST';
                                    $submit_text = 'Request Outbound Gudang';
                                    break;
                        

                                case 'SO_PAYMENT_COMPLETION':
                                    $action = 'SO_COMPLETED';
                                    $submit_text = 'Completed Sales Order';
                                    break;
                                default:
                                    $action = '';
                                    $submit_text = '';
                            }

                            if($sale_shipments_confirmed && $sale->status == 'SO_OUTBOUND_REQUEST')
                            {
                                $action = 'SO_PAYMENT_COMPLETION';
                                $submit_text = 'Koordinasi Pembayaran Pelunasan dari Customer';
                            }
                        @endphp
                        
                        @if($action != '')
                        <div class="flex justify mt-4">
                            <form action="{{ route('sales.action', ['id' => $sale->id, 'action' => $action]) }}" method="POST">
                                @csrf
                                @method('POST')
                                <x-primary-button type="submit">{{ $submit_text }}</x-primary-button>
                            </form>
                        </div>
                        @endif
                    </div>

                    <div class="my-6 flex-grow border-t border-gray-500 dark:border-gray-700"></div>

                    <div class="flex justify-end space-x-4">
                        <x-secondary-button>
                            <a href="{{ route('sales.index') }}">Back to List</a>
                        </x-secondary-button>
                        @if($sale->status == 'SO_OFFER' || $sale->status == 'SO_REQUEST')
                        <x-button href="{{route('sales.edit', $sale->id)}}" text="Edit Sales"
                            class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 dark:bg-green-700 dark:hover:bg-green-800">Edit Sales</x-button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
</x-company-layout>