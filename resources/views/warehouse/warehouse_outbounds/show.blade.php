@php
    $layout = session('layout');
@endphp

<x-dynamic-component :component="'layouts.' . $layout">
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-white">
                    <h1 class="text-2xl font-bold mb-6">Outbound Details: {{ $outbound->id }}</h1>
                    <div class="mb-3 mt-1 flex-grow border-t border-gray-500 dark:border-gray-700"></div>

                    <h3 class="text-lg font-bold my-3">Outbound Details</h3>
                    <div class="grid grid-cols-3 sm:grid-cols-3 gap-6 mb-6">
                        <x-div-box-show title="Outbound Date">{{ $outbound->date?->format('Y-m-d') ?? 'N/A' }}</x-div-box-show>
                        <x-div-box-show title="Transaction">
                            {{ $outbound->source_type ?? 'N/A' }} : {{ $outbound->source->number ?? 'N/A' }}
                        </x-div-box-show>

                        <x-div-box-show title="Consignee">
                            @if ($outbound->source_type == 'ITF')
                                {{ $outbound->source->consignee_type ?? 'N/A' }} 
                                : {{$outbound->source->consignee->name ?? 'N/A' }}
                            @elseif ($outbound->source_type == 'SO')
                                {{ $outbound->source->customer->name ?? 'N/A' }}
                            @endif
                        </x-div-box-show>
                        <x-div-box-show title="Admin">{{ $outbound->employee?->companyuser->user->name ?? 'N/A' }}</x-div-box-show>
                        
                        <x-div-box-show title="Status">
                            <p
                                class="text-lg font-medium inline-block bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">
                                {{ $outbound->status ?? 'N/A' }}
                            </p>
                        </x-div-box-show>
                        <x-div-box-show title="Notes">{{ $outbound->notes ?? 'N/A' }}</x-div-box-show>
                    </div>
                    <div class="mb-3 mt-1 flex-grow border-t border-gray-500 dark:border-gray-700"></div>


                    <h3 class="text-lg font-bold mt-6">Products</h3>
                    <x-table-table id="search-table">
                        <x-table-thead>
                            <tr>
                                <x-table-th>#</x-table-th>
                                <x-table-th>Product</x-table-th>
                                <x-table-th>Quantity</x-table-th>
                                <x-table-th>Stock Available</x-table-th>
                                <x-table-th>Cost</x-table-th>
                                <x-table-th>Location</x-table-th>
                                <x-table-th>Notes</x-table-th>
                                <x-table-th>Actions</x-table-th>
                            </tr>
                        </x-table-thead>
                        <x-table-tbody>
                            @foreach ($outbound->items as $item)
                                <x-table-tr>
                                    <x-table-td>{{ $item->id }}</x-table-td>
                                    <x-table-td>{{ $item->item->product->id }} : {{ $item->item->product->name }}</x-table-td>
                                    <x-table-td>{{ $item->quantity }}</x-table-td>
                                    <x-table-td>{{ $item->item->quantity ?? 0 }}</x-table-td>
                                    <x-table-td>{{ $item->cost_per_unit }}</x-table-td>
                                    <x-table-td>{{ $item->item->warehouse_location?->print_location() ?? 'N/A' }}</x-table-td>
                                    <x-table-td>{{ $item->notes ?? 'N/A' }}</x-table-td>
                                    <x-table-td>
                                        <div class="flex items-center space-x-2">
                                        </div>
                                    </x-table-td>
                                </x-table-tr>
                            @endforeach
                        </x-table-tbody>
                    </x-table-table>
                    <div class="mb-3 mt-1 flex-grow border-t border-gray-500 dark:border-gray-700"></div>


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
                                @foreach ($outbound->shipments as $shipment)
                                    <x-table-tr>
                                        <x-table-td>{{ $shipment->id }}</x-table-td>
                                        <x-table-td>{{ $shipment->shipper_type }} : {{ $shipment->shipper?->name }}</x-table-td>
                                        <x-table-td>{{ $shipment->consignee_type }} : {{ $shipment->consignee?->name }}</x-table-td>
                                        <x-table-td>{{ $shipment->transaction_type }} : {{ $shipment->transaction?->number }}</x-table-td>
                                        <x-table-td>{{ $shipment->ship_date }}</x-table-td>
                                        <x-table-td>{{ $shipment->status }}</x-table-td>
                                        <x-table-td>{{ $shipment->notes }}</x-table-td>
                                        <x-table-td>
                                            <div class="flex items-center space-x-2">
                                                <x-button-show :route="route('shipments.show', $shipment->id)" />
                                                <x-button-edit :route="route('shipments.edit', $shipment->id)" />
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
                    @if ($outbound_action_allowed)
                        @if ($outbound->status == 'OUTB_REQUEST')
                        <div>
                            <div class="flex justify mt-4">
                                <form action="{{ route('warehouse_outbounds.action', ['warehouse_outbounds'=> $outbound->id, 'action' => 'OUTB_PROCESS']) }}" method="POST">
                                    @csrf
                                    @method('POST')
                                    <x-primary-button type="submit">Accept Outbound Request & Process</x-primary-button>
                                </form>
                            </div>
                        </div>
                        @elseif ($outbound->status == 'OUTB_PROCESS')
                        <div>
                            <div class="flex justify mt-4">
                                <form action="{{ route('warehouse_outbounds.action', ['warehouse_outbounds'=> $outbound->id, 'action' => 'OUTB_IN_TRANSIT']) }}" method="POST">
                                    @csrf
                                    @method('POST')
                                    <x-primary-button type="submit">Process & Kirim Outbound Selesai :)</x-primary-button>
                                </form>
                            </div>
                        </div>
                        @elseif ($outbound->status == 'OUTB_IN_TRANSIT')
                        <div>
                            <div class="flex justify mt-4">
                                <form action="{{ route('warehouse_outbounds.action', ['warehouse_outbounds'=> $outbound->id, 'action' => 'OUTB_COMPLETED']) }}" method="POST">
                                    @csrf
                                    @method('POST')
                                    <x-primary-button type="submit">Complete Process Outbound Selesai :)</x-primary-button>
                                </form>
                            </div>
                        </div>
                        @endif
                    @endif

                    <div class="my-6 flex-grow border-t border-gray-500 dark:border-gray-700"></div>

                    <!-- Back Button -->
                    <x-secondary-button>
                        <a href="{{ route('warehouse_outbounds.index') }}">Back to List</a>
                    </x-secondary-button>
                </div>
            </div>
        </div>
    </div>
</x-dynamic-component>
