@php 
    $purchase_shipments_confirmed = true;
@endphp
<x-company-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-white">
                    <h1 class="text-2xl font-bold mb-6">Purchase Details: {{ $purchase->po_number }}</h1>
                    <div class="mb-3 mt-1 flex-grow border-t border-gray-500 dark:border-gray-700"></div>

                    <h3 class="text-lg font-bold my-3">Supplier Details</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
                    <div
                            class="p-4 bg-gray-50 border border-gray-200 rounded-lg shadow-md dark:bg-gray-700 dark:border-gray-600">
                            <p class="text-sm text-gray-500 dark:text-gray-300">PO Number</p>
                            <p class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ $purchase->po_number }}</p>
                        </div>
                        <div
                            class="p-4 bg-gray-50 border border-gray-200 rounded-lg shadow-md dark:bg-gray-700 dark:border-gray-600">
                            <p class="text-sm text-gray-500 dark:text-gray-300">Purchase Date</p>
                            <p class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ $purchase->po_date }}</p>
                        </div>
                        <div
                            class="p-4 bg-gray-50 border border-gray-200 rounded-lg shadow-md dark:bg-gray-700 dark:border-gray-600">
                            <p class="text-sm text-gray-500 dark:text-gray-300">Supplier</p>
                            <p class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ $purchase->supplier->name ?? 'N/A' }}</p>
                        </div>
                        <div
                            class="p-4 bg-gray-50 border border-gray-200 rounded-lg shadow-md dark:bg-gray-700 dark:border-gray-600">
                            <p class="text-sm text-gray-500 dark:text-gray-300">Warehouse</p>
                            <p class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ $purchase->warehouse->name ?? 'N/A' }}</p>
                        </div>
                        <div
                            class="p-4 bg-gray-50 border border-gray-200 rounded-lg shadow-md dark:bg-gray-700 dark:border-gray-600">
                            <p class="text-sm text-gray-500 dark:text-gray-300">Total Amount</p>
                            <p class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                Rp{{ number_format($purchase->total_amount, 2) }}</p>
                        </div>
                        <div
                            class="p-4 bg-gray-50 border border-gray-200 rounded-lg shadow-md dark:bg-gray-700 dark:border-gray-600">
                            <p class="text-sm text-gray-500 dark:text-gray-300">Status</p>
                            <p
                                class="text-lg font-medium inline-block bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">
                                {{ $purchase->status }}
                            </p>
                        </div>
                    </div>
                    <div class="mb-3 mt-1 flex-grow border-t border-gray-500 dark:border-gray-700"></div>

                    <h3 class="text-lg font-bold my-3">Products</h3>
                    <div class="overflow-x-auto">
                        <x-table-table>
                            <x-table-thead>
                                <tr class>
                                    <x-table-th>Product Name</x-table-th>
                                    <x-table-th>Quantity</x-table-th>
                                    <x-table-th>Buying Price</x-table-th>
                                    <x-table-th>Total Cost</x-table-th>
                                </tr>
                            </x-table-thead>
                            <x-table-tbody>
                                @foreach ($purchase->products as $product)
                                    <x-table-tr>
                                        <x-table-td>{{ $product->name }}</x-table-td>
                                        <x-table-td>{{ $product->pivot->quantity }}</x-table-td>
                                        <x-table-td>Rp{{ number_format($product->pivot->buying_price, 2) }}</x-table-td>
                                        <x-table-td>Rp{{ number_format($product->pivot->total_cost, 2) }}</x-table-td>
                                    </x-table-tr>
                                @endforeach
                            </x-table-tbody>
                        </x-table-table>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div
                            class="mt-6 p-4 border border-gray-100 rounded-lg shadow-md dark:bg-gray-80 dark:border-gray-600">
                            <p class="text-sm text-gray-500 dark:text-gray-300">Notes</p>
                            <p class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ $purchase->notes ?? 'No additional notes' }}</p>
                        </div>
                    </div>
                    <div class="my-6 flex-grow border-t border-gray-500 dark:border-gray-700"></div>

                    <!-- Invoice Section -->
                    <h3 class="text-lg font-bold my-3">Invoices</h3>
                    <div class="overflow-x-auto">
                        <x-table-table>
                            <x-table-thead>
                                <tr class>
                                    <x-table-th>Id</x-table-th>
                                    <x-table-th>Invoice Number</x-table-th>
                                    <x-table-th>Date</x-table-th>
                                    <x-table-th>Due Date</x-table-th>
                                    <x-table-th>Total Amount</x-table-th>
                                    <x-table-th>Notes</x-table-th>
                                    <x-table-th>Status</x-table-th>
                                    <x-table-th>Actions</x-table-th>
                                </tr>
                            </x-table-thead>
                            <x-table-tbody>
                                @foreach ($purchase->purchase_invoices as $invoice)
                                    <x-table-tr>
                                        <x-table-td>{{ $invoice->id }}</x-table-td>
                                        <x-table-td>{{ $invoice->number }}</x-table-td>
                                        <x-table-td>{{ $invoice->date }}</x-table-td>
                                        <x-table-td>{{ $invoice->due_date }}</x-table-td>
                                        <x-table-td>Rp{{ number_format($invoice->total_amount, 2) }}</x-table-td>
                                        <x-table-td>{{ $invoice->notes }}</x-table-td>
                                        <x-table-td>{{ $invoice->status }}</x-table-td>
                                        <x-table-td>
                                            <div class="flex space-x-2">
                                                <x-button-show :route="route('purchase_invoices.show', $invoice->id)" />
                                            </div>
                                        </x-table-td>
                                    </x-table-tr>
                                @endforeach
                            </x-table-tbody>
                        </x-table-table>
                    </div>
                    <div class="my-6 flex-grow border-t border-gray-500 dark:border-gray-700"></div>

                    <!-- Shipment Section -->
                    <h3 class="text-lg font-bold my-3">Shipments</h3>
                    <div class="overflow-x-auto">
                        <x-table-table>
                            <x-table-thead>
                                <tr>
                                    <x-table-th>ID</x-table-th>
                                    <x-table-th>Pengirim</x-table-th>
                                    <x-table-th>Penerima</x-table-th>
                                    <x-table-th>Transaksi</x-table-th>
                                    <x-table-th>Tanggal</x-table-th>
                                    <x-table-th>Status</x-table-th>
                                    <x-table-th>Notes</x-table-th>
                                    <x-table-th>Actions</x-table-th>
                                </tr>
                            </x-table-thead>
                            <x-table-tbody>
                                @foreach ($purchase->shipments as $shipment)
                                    @php
                                        $purchase_shipments_confirmed = $shipment->status == 'SHP_DELIVERY_CONFIRMED';
                                    @endphp

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
                    <div class="my-6 flex-grow border-t border-gray-500 dark:border-gray-700"></div>



                    <!-- Action Section -->
                    <h3 class="text-lg font-bold my-3">Actions</h3>
                    <div>
                        @php
                            switch ($purchase->status) {
                                case 'PO_PLANNED':
                                    $action = 'PO_REQUEST_TO_SUPPLIER';
                                    $submit_text = 'Kirim Pembelian ke Supplier';
                                    break;
                                case 'PO_REQUEST_TO_SUPPLIER':
                                    $action = 'PO_CONFIRMED';
                                    $submit_text = 'Input Invoice Pembelian dari Supplier';
                                    break;
                                case 'PO_CONFIRMED':
                                    $action = 'PO_DP_CONFIRMED';
                                    $submit_text = 'Konfirmasi Pembayaran ke Supplier';
                                    break;
                                case 'PO_DP_CONFIRMED':
                                    $action = 'PO_SHIPMENT_CONFIRMED';
                                    $submit_text = 'Input Shipment Pembelian dari Supplier';
                                    break;

                                case 'PO_COMPLETE_PAYMENT':
                                    $action = 'PO_COMPLETED';
                                    $submit_text = 'Complete Purchase & Close Order';
                                    break;
                                default:
                                    $action = '';
                                    $submit_text = '';
                            }

                            if($purchase_shipments_confirmed && $purchase->status == 'PO_SHIPMENT_CONFIRMED')
                            {
                                $action = 'PO_COMPLETE_PAYMENT';
                                $submit_text = 'Konfirmasi Pembayaran Lunas ke Supplier';
                            }
                        @endphp
                        
                        @if($action != '')
                        <div class="flex justify mt-4">
                            <form action="{{ route('purchases.action', ['purchases' => $purchase->id, 'action' => $action]) }}" method="POST">
                                @csrf
                                @method('POST')
                                <x-primary-button type="submit">{{ $submit_text }}</x-primary-button>
                            </form>
                        </div>
                        @endif
                    </div>

                    <div class="my-6 flex-grow border-t border-gray-500 dark:border-gray-700"></div>

                    <!-- Back Button -->
                    <x-secondary-button>
                        <a href="{{ route('purchases.index') }}">Back to List</a>
                    </x-secondary-button>
                    @if($purchase->status == 'PO_PLANNED' || $purchase->status == 'PO_REQUEST_TO_SUPPLIER')
                        <x-primary-button>
                            <a href="{{ route('purchases.edit', $purchase->id) }}">Edit Purchase</a>
                        </x-primary-button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-company-layout>