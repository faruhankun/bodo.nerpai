@php
    $layout = session('layout');
@endphp
<x-dynamic-component :component="'layouts.' . $layout">
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-white">
                    <h1 class="text-2xl font-bold mb-6">Shipment Confirmation Details: {{ $shipment_confirmation->id }}</h1>
                    <div class="mb-3 mt-1 flex-grow border-t border-gray-500 dark:border-gray-700"></div>

                    <h3 class="text-lg font-bold my-3">Shipment Confirmation Details</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
                        <div
                            class="p-4 bg-gray-50 border border-gray-200 rounded-lg shadow-md dark:bg-gray-700 dark:border-gray-600">
                            <p class="text-sm text-gray-500 dark:text-gray-300">Shipment Number</p>
                            <p class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ $shipment_confirmation->shipment->shipment_number ?? 'N/A' }}</p>
                        </div>
                        <div
                            class="p-4 bg-gray-50 border border-gray-200 rounded-lg shadow-md dark:bg-gray-700 dark:border-gray-600">
                            <p class="text-sm text-gray-500 dark:text-gray-300">Admin</p>
                            <p class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ $shipment_confirmation->employee->companyuser->user->name ?? 'N/A' }}</p>
                        </div>

                        <div
                            class="p-4 bg-gray-50 border border-gray-200 rounded-lg shadow-md dark:bg-gray-700 dark:border-gray-600">
                            <p class="text-sm text-gray-500 dark:text-gray-300">Consignee</p>
                            <p class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ $shipment->consignee_type }} : {{$shipment->consignee->name ?? 'N/A' }}</p>
                        </div>
                        <div
                            class="p-4 bg-gray-50 border border-gray-200 rounded-lg shadow-md dark:bg-gray-700 dark:border-gray-600">
                            <p class="text-sm text-gray-500 dark:text-gray-300">Consignee</p>
                            <p class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ $shipment_confirmation->consignee_name ?? 'N/A' }}</p>
                        </div>

                        <div
                            class="p-4 bg-gray-50 border border-gray-200 rounded-lg shadow-md dark:bg-gray-700 dark:border-gray-600">
                            <p class="text-sm text-gray-500 dark:text-gray-300">Received Time</p>
                            <p class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ $shipment_confirmation->received_time ?? 'N/A' }}</p>
                        </div>
                        <div
                            class="p-4 bg-gray-50 border border-gray-200 rounded-lg shadow-md dark:bg-gray-700 dark:border-gray-600">
                            <p class="text-sm text-gray-500 dark:text-gray-300">Admin Notes</p>
                            <p class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ $shipment_confirmation->notes ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="mb-3 mt-1 flex-grow border-t border-gray-500 dark:border-gray-700"></div>


                    <h3 class="text-lg font-bold mt-6">Products</h3>
                    <x-table-table id="search-table">
                        <x-table-thead>
                            <tr>
                                <x-table-th>#</x-table-th>
                                <x-table-th>Product</x-table-th>
                                <x-table-th>Quantity</x-table-th>
                                <x-table-th>Kondisi</x-table-th>
                                <x-table-th>Notes</x-table-th>
                                <x-table-th>Actions</x-table-th>
                            </tr>
                        </x-table-thead>
                        <x-table-tbody>
                            @foreach ($shipment_confirmation->products as $index => $product)
                                <x-table-tr>
                                    <x-table-td>{{ $product->pivot->id }}</x-table-td>
                                    <x-table-td>{{ $product->id }} : {{ $product->name }}</x-table-td>
                                    <x-table-td>{{ $product->pivot->quantity }}</x-table-td>
                                    <x-table-td>{{ $product->pivot->condition }}</x-table-td>
                                    <x-table-td>{{ $product->pivot->notes ?? 'N/A' }}</x-table-td>
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
                    <div>
                        <div class="flex justify mt-4">
                            <form action="{{ route('warehouse_inbounds.action', ['warehouse_inbounds'=> '0','shipment_confirmation' => $shipment_confirmation->id, 'action' => 'INB_REQUEST']) }}" method="POST">
                                @csrf
                                @method('POST')
                                <x-primary-button type="submit">Request Inbound to Warehouse</x-primary-button>
                            </form>
                        </div>
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
