@php
    $layout = session('layout');
@endphp
<x-dynamic-component :component="'layouts.' . $layout">
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-white">
                    <h3 class="text-2xl font-bold dark:text-white">Edit Shipment Confirmation</h3>
                    <p class="text-sm dark:text-gray-200 mb-3">Update the details of your shipment_confirmation.</p>

                    <div class="p-2 border border-gray-200 rounded-lg shadow-md dark:bg-gray-800 dark:border-gray-600 mb-4">
                        <form action="{{ route('shipments.confirm-update', $shipment_confirmation->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-6">
                                <div
                                    class="p-4 bg-gray-50 border border-gray-200 rounded-lg shadow-md dark:bg-gray-700 dark:border-gray-600">
                                    <p class="text-sm text-gray-500 dark:text-gray-300">Shipment Number</p>
                                    <p class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                        {{ $shipment->shipment_number }}</p>
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

                                <div class="form-group">
                                    <x-input-label for="consignee_name">Consignee Name (opsional)</x-input-label>
                                    <x-input-input name="consignee_name" value="{{ $shipment_confirmation->consignee_name }}"></x-input-input>
                                </div>

                                <div class="form-group">
                                    <x-input-label for="received_time">Received Time</x-input-label>
                                    <input type="datetime-local" name="received_time" class="bg-gray-100 w-full px-4 py-2 border rounded-md focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:text-white" value="{{ ($shipment_confirmation->received_time) }}" >
                                </div>

                                <div class="form-group">
                                    <x-input-label for="notes">Admin Notes</x-input-label>
                                    <textarea name="notes" class="bg-gray-100 w-full px-4 py-2 border rounded-md focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:text-white">{{ $shipment_confirmation->notes }}</textarea>
                                </div>
                            </div>
                            <div class="my-6 flex-grow border-t border-gray-500 dark:border-gray-700"></div>

                            <h3 class="text-lg font-bold mt-6">Products</h3>
                            <x-table.table-table id="search-table">
                                <x-table.table-thead>
                                    <tr>
                                        <x-table.table-th>#</x-table.table-th>
                                        <x-table.table-th>Product</x-table.table-th>
                                        <x-table.table-th>Quantity</x-table.table-th>
                                        <x-table.table-th>Kondisi</x-table.table-th>
                                        <x-table.table-th>Notes</x-table.table-th>
                                        <x-table.table-th>Actions</x-table.table-th>
                                    </tr>
                                </x-table.table-thead>
                                <x-table.table-tbody>
                                    @foreach ($shipment_confirmation->products as $index => $product)
                                        <x-table.table-tr>
                                            <x-table.table-td>{{ $product->pivot->id }}</x-table.table-td>
                                            <x-table.table-td>{{ $product->id }} : {{ $product->name }}</x-table.table-td>
                                            <x-table.table-td>{{ $product->pivot->quantity }}</x-table.table-td>
                                            <x-table.table-td>{{ $product->pivot->condition }}</x-table.table-td>
                                            <x-table.table-td>{{ $product->pivot->notes ?? 'N/A' }}</x-table.table-td>
                                            <x-table.table-td>
                                                <div class="flex items-center space-x-2">
                                                </div>
                                            </x-table.table-td>
                                        </x-table.table-tr>
                                    @endforeach
                                </x-table.table-tbody>
                            </x-table.table-table>
                            <!-- <div id="product-selection" class="grid grid-cols-1 sm:grid-cols-1 gap-6">
                                @foreach ($shipment_confirmation->products as $index => $product)
                                <div class="product-item mb-4 p-4 border border-gray-200 rounded-lg shadow-md dark:bg-gray-800 dark:border-gray-600">
                                <div class="flex inline justify-between space items-center">
										<h3 class="text-md font-bold">Products</h3>
										<button type="button"
											class="ml-3 bg-red-500 text-sm text-white px-4 py-1 rounded-md hover:bg-red-700 remove-product">
											Remove
										</button>
									</div>
									<div class="mb-3 mt-1 flex-grow border-t border-gray-500 dark:border-gray-700">
									</div>    
                                    <x-input-label for="product_id">Select Product</x-input-label>
                                    <select name="products[{{ $index }}][product_id]" class="w-full px-4 py-2 border rounded-md focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:text-white"  required>
                                        @foreach ($products as $availableProduct)
                                        <option value="{{ $availableProduct->id }}" {{ $availableProduct->id == $product->id ? 'selected' : '' }}>{{ $availableProduct->name }} - Rp{{ $availableProduct->price }}</option>
                                        @endforeach
                                    </select>

                                    <x-input-label for="quantity">Quantity</x-input-label>
                                    <input type="number" name="products[{{ $index }}][quantity]" class="w-full px-4 py-2 border rounded-md focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:text-white" value="{{ $product->pivot->quantity }}" required >

                                    <x-input-label for="buying_price">Buying Price</x-input-label>
                                    <input type="number" name="products[{{ $index }}][buying_price]" step="0.01" class="w-full px-4 py-2 border rounded-md focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:text-white" value="{{ $product->pivot->buying_price }}" required >
                                </div>
                                @endforeach
                            </div> -->
                            <!-- <button class="m-4 px-3 py-2 inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 bg-primary text-primary-foreground shadow hover:bg-primary/90" type="button" id="add-product" class="mr-3">
                                Add Another Product
                            </button> -->

                            <div class="my-6 flex-grow border-t border-gray-500 dark:border-gray-700"></div>
                            <div class="m-4">
                                <a href="{{ url()->previous() }}">
                                    <x-secondary-button type="button">Cancel</x-secondary-button>
                                </a>
                                <!-- referrer -->
                                <input type="hidden" name="referrer" value="{{ url()->previous() }}">
                                <x-primary-button>Update Shipment Confirmation</x-primary-button>
                            </div>
                        </form>
                        
                        <!-- <script>
                            document.addEventListener('DOMContentLoaded', () => {
                                const productSelection = document.getElementById('product-selection');
                                let productIndex = {{ $shipment_confirmation->products->count() }};
								

                                document.getElementById('add-product').addEventListener('click', function () {
                                    const newProductDiv = document.createElement('div');
                                    newProductDiv.classList.add('product-item', 'mb-4', 'p-4', 'border', 'border-gray-200', 'rounded-lg', 'shadow-md', 'dark:bg-gray-800', 'dark:border-gray-600');
                                    newProductDiv.innerHTML = `
                                    <div class="flex inline justify-between space items-center">
										<h3 class="text-md font-bold">Products 1</h3>
										<button type="button"
											class="ml-3 bg-red-500 text-sm text-white px-4 py-1 rounded-md hover:bg-red-700 remove-product">
											Remove
										</button>
									</div>
									<div class="mb-3 mt-1 flex-grow border-t border-gray-500 dark:border-gray-700">
									</div>
                                        <x-input-label for="product_id">Select Product</x-input-label>
                                        <select name="products[${productIndex}][product_id]" class="w-full px-4 py-2 border rounded-md focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:text-white" required>
                                            @foreach ($products as $availableProduct)
                                                <option value="{{ $availableProduct->id }}">{{ $availableProduct->name }} - Rp{{ $availableProduct->price }}</option>
                                            @endforeach
                                        </select>

                                        <x-input-label for="quantity">Quantity</x-input-label>
                                        <input type="number" name="products[${productIndex}][quantity]" class="w-full px-4 py-2 border rounded-md focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:text-white" min="1" required>

                                        <x-input-label for="buying_price">Buying Price</x-input-label>
                                        <input type="number" name="products[${productIndex}][buying_price]" step="0.01" class="w-full px-4 py-2 border rounded-md focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:text-white" required>
                                    `;

                                    productSelection.appendChild(newProductDiv);
                                    productIndex++;
                                });
                                productSelection.addEventListener('click', function (event) {
                                    if (event.target && event.target.classList.contains('remove-product')) {
                                        const productDiv = event.target.closest('.product-item');
                                        productDiv.remove(); // Remove the product div
                                    }
                                });
                            });
                        </script> -->

                    </div>

                    <div class="my-6 flex-grow border-t border-gray-500 dark:border-gray-700"></div>
                        <h3 class="text-lg font-bold my-3">Actions</h3>
                        @if ($shipment_confirmation->status == 'PO_REQUEST_TO_SUPPLIER')
                        <div class="flex justify-end m-4">
                            <form action="{{ route('shipments.action', ['shipments' => $shipment_confirmation->id, 'action' => 'PO_CONFIRMED']) }}" method="POST">
                                @csrf
                                @method('POST')
                                <x-primary-button type="submit">Input Invoice Pembelian dari Supplier</x-primary-button>
                            </form>
                        </div>
                        @endif
                </div>
            </div>
        </div>
    </div>
</x-dynamic-component>