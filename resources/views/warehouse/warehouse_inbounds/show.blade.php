@php
    $shipment_confirmation = $inbound->shipment_confirmation;
    $shipment = $shipment_confirmation->shipment;
    $layout = session('layout');
@endphp

<x-dynamic-component :component="'layouts.' . $layout">
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-white">
                    <h1 class="text-2xl font-bold mb-6">Inbound Details: {{ $inbound->id }}</h1>
                    <div class="mb-3 mt-1 flex-grow border-t border-gray-500 dark:border-gray-700"></div>

                    <h3 class="text-lg font-bold my-3">Inbound Details</h3>
                    <div class="grid grid-cols-3 sm:grid-cols-3 gap-6 mb-6">
                        <x-div-box-show title="Inbound Date">{{ $inbound->inbound_date ?? 'N/A' }}</x-div-box-show>
                        <x-div-box-show title="Shipment Confirmation ID">{{ $shipment_confirmation->id ?? 'N/A' }}</x-div-box-show>

                        <x-div-box-show title="Consignee">{{ $shipment->consignee_type }} : {{$shipment->consignee->name ?? 'N/A' }}</x-div-box-show>
                        <x-div-box-show title="Admin">{{ $inbound->employee->companyuser->user->name ?? 'N/A' }}</x-div-box-show>
                        
                        <x-div-box-show title="Status">
                            <p
                                class="text-lg font-medium inline-block bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">
                                {{ $inbound->status ?? 'N/A' }}
                            </p>
                        </x-div-box-show>
                        <x-div-box-show title="Admin Notes">{{ $inbound->notes ?? 'N/A' }}</x-div-box-show>
                    </div>
                    <div class="mb-3 mt-1 flex-grow border-t border-gray-500 dark:border-gray-700"></div>


                    <h3 class="text-lg font-bold mt-6">Products</h3>
                    <x-table.table-table id="search-table">
                        <x-table.table-thead>
                            <tr>
                                <x-table.table-th>#</x-table.table-th>
                                <x-table.table-th>Product</x-table.table-th>
                                <x-table.table-th>Quantity</x-table.table-th>
                                <x-table.table-th>Cost</x-table.table-th>
                                <x-table.table-th>Notes</x-table.table-th>
                                <x-table.table-th>Location</x-table.table-th>
                                <x-table.table-th>Actions</x-table.table-th>
                            </tr>
                        </x-table.table-thead>
                        <x-table.table-tbody>
                            @foreach ($inbound->inbound_products as $index => $product)
                                <x-table.table-tr>
                                    <x-table.table-td>{{ $product->id }}</x-table.table-td>
                                    <x-table.table-td>{{ $product->product->id }} : {{ $product->product->name }}</x-table.table-td>
                                    <x-table.table-td>{{ $product->quantity }}</x-table.table-td>
                                    <x-table.table-td>{{ $product->cost_per_unit }}</x-table.table-td>
                                    <x-table.table-td>{{ $product->notes ?? 'N/A' }}</x-table.table-td>
                                    <x-table.table-td>{{ $product->warehouse_location?->print_location() ?? 'N/A' }}</x-table.table-td>
                                    <x-table.table-td>
                                        <div class="flex items-center space-x-2">
                                        </div>
                                    </x-table.table-td>
                                </x-table.table-tr>
                            @endforeach
                        </x-table.table-tbody>
                    </x-table.table-table>


                    <!-- Action Section -->
                    <h3 class="text-lg font-bold my-3">Actions</h3>
                    @if ($inbound->status == 'INB_REQUEST')
                    <div>
                        <div class="flex justify mt-4">
                            <form action="{{ route('warehouse_inbounds.action', ['warehouse_inbounds'=> $inbound->id, 'action' => 'INB_PROCESS']) }}" method="POST">
                                @csrf
                                @method('POST')
                                <x-primary-button type="submit">Process Masukan Barang ke Gudang</x-primary-button>
                            </form>
                        </div>
                    </div>
                    @elseif ($inbound->status == 'INB_PROCESS')
                    <div>
                        <div class="flex justify mt-4">
                            <form action="{{ route('warehouse_inbounds.action', ['warehouse_inbounds'=> $inbound->id, 'action' => 'INB_COMPLETED']) }}" method="POST">
                                @csrf
                                @method('POST')
                                <x-primary-button type="submit">Process Inbound Selesai :)</x-primary-button>
                            </form>
                        </div>
                    </div>
                    @endif

                    <div class="my-6 flex-grow border-t border-gray-500 dark:border-gray-700"></div>

                    <!-- Back Button -->
                    <x-secondary-button>
                        <a href="{{ route('warehouse_inbounds.index') }}">Back to List</a>
                    </x-secondary-button>
                </div>
            </div>
        </div>
    </div>
</x-dynamic-component>
