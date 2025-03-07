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
                        <x-div-box-show title="Inbound Date">{{ $inbound->date ?? 'N/A' }}</x-div-box-show>
                        <x-div-box-show title="Shipment Confirmation ID">{{ $shipment_confirmation->id ?? 'N/A' }}</x-div-box-show>

                        <x-div-box-show title="Consignee">{{ $shipment->consignee_type }} : {{$shipment->consignee->name ?? 'N/A' }}</x-div-box-show>
                        <x-div-box-show title="Admin">{{ $inbound->store_employee?->employee->companyuser->user->name ?? 'N/A' }}</x-div-box-show>
                        
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
                    <x-table-table id="search-table">
                        <x-table-thead>
                            <tr>
                                <x-table-th>#</x-table-th>
                                <x-table-th>Product</x-table-th>
                                <x-table-th>Quantity</x-table-th>
                                <x-table-th>Cost</x-table-th>
                                <x-table-th>Notes</x-table-th>
                                <x-table-th>Location</x-table-th>
                                <x-table-th>Actions</x-table-th>
                            </tr>
                        </x-table-thead>
                        <x-table-tbody>
                            @foreach ($inbound->store_inbound_products as $index => $store_product)
                                <x-table-tr>
                                    <x-table-td>{{ $store_product->id }}</x-table-td>
                                    <x-table-td>{{ $store_product->id }} : {{ $store_product->store_product->product->name }}</x-table-td>
                                    <x-table-td>{{ $store_product->quantity }}</x-table-td>
                                    <x-table-td>{{ $store_product->store_product->price * 0.5 }}</x-table-td>
                                    <x-table-td>{{ $store_product->notes ?? 'N/A' }}</x-table-td>
                                    <x-table-td>{{ $store_product->warehouse_location->print_location() ?? 'N/A' }}</x-table-td>
                                    <x-table-td>
                                        <div class="flex items-center space-x-2">
                                        </div>
                                    </x-table-td>
                                </x-table-tr>
                            @endforeach
                        </x-table-tbody>
                    </x-table-table>


                    <!-- Action Section -->
                    <h3 class="text-lg font-bold my-3">Actions</h3>
                    @if ($inbound->status == 'INB_REQUEST')
                    <div>
                        <div class="flex justify mt-4">
                            <form action="{{ route('store_inbounds.action', ['id'=> $inbound->id, 'action' => 'INB_PROCESS']) }}" method="POST">
                                @csrf
                                @method('POST')
                                <x-primary-button type="submit">Process Masukan Barang ke Gudang</x-primary-button>
                            </form>
                        </div>
                    </div>
                    @elseif ($inbound->status == 'INB_PROCESS')
                    <div>
                        <div class="flex justify mt-4">
                            <form action="{{ route('store_inbounds.action', ['id'=> $inbound->id, 'action' => 'INB_COMPLETED']) }}" method="POST">
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
                        <a href="{{ route('store_inbounds.index') }}">Back to List</a>
                    </x-secondary-button>
                </div>
            </div>
        </div>
    </div>
</x-dynamic-component>
