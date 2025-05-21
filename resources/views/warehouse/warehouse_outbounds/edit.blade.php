<x-company-layout>
<div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-white">
    <h3 class="text-lg font-bold dark:text-white">Edit Outbound Request</h3>
    <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>
    <form action="{{ route('outbound_requests.update', $outboundRequest->id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Warehouse Selection -->
		<div class="mb-4">
			<x-input-label for="warehouse_id">Warehouse</x-input-label>
			<select name="warehouse_id" id="warehouse_id" class="bg-gray-100 w-1/2 px-4 py-2 border rounded-md focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:text-white {{ $outboundRequest->status != 'Requested' ? 'readonly-select' : '' }}" {{ $outboundRequest->status != 'Requested' ? 'readonly' : '' }}>
				@foreach($warehouses as $warehouse)
				<option value="{{ $warehouse->id }}" {{ $outboundRequest->warehouse_id == $warehouse->id ? 'selected' : '' }}>
					{{ $warehouse->name }}
				</option>
				@endforeach
			</select>
		</div>
        <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>

        <x-input-label>Requested Products</x-input-label>
        <x-table.table-table id="search-table" >
    <x-table.table-thead>
        <tr>
            <x-table.table-th rowspan="2">Product</x-table.table-th>
            <x-table.table-th rowspan="2">Requested Quantity</x-table.table-th>
            <x-table.table-th rowspan="2">Stock in Warehouse</x-table.table-th>
            <x-table.table-th class="text-center" colspan="3">Locations</x-table.table-th>
        </tr>
        <tr>
            <x-table.table-th class="text-center border-t border-gray-400 ">Room & Rack</x-table.table-th>
            <x-table.table-th class="text-center border-t border-gray-400 ">Quantity</x-table.table-th>
            <x-table.table-th class="text-center border-t border-gray-400 ">Action</x-table.table-th>
        </tr>
    </x-table.table-thead>
    <x-table.table-tbody>
        @foreach ($outboundRequest->requested_quantities as $productId => $quantity)
            <x-table.table-tr>
                <x-table.table-td>{{ $outboundRequest->sales->products->find($productId)?->name ?? 'Product not found' }}</x-table.table-td>
                <x-table.table-td>{{ $quantity }}</x-table.table-td>
                <x-table.table-td id="stock-in-warehouse-{{ $productId }}">
                    {{ \App\Models\Inventory::where('warehouse_id', $outboundRequest->warehouse_id)
                        ->where('product_id', $productId)
                        ->sum('quantity') }}
                </x-table.table-td>
                <x-table.table-td class="p-2">
                    <select name="locations[{{ $productId }}][room_rack]" class="w-full px-4 py-2 border rounded-md focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:text-white">
                        @foreach ($availableLocations[$productId] ?? [] as $availableLocation)
                            <option value="{{ $availableLocation->id }}">
                                Room: {{ $availableLocation->room }}, Rack: {{ $availableLocation->rack }} (Available: {{ $availableLocation->quantity }})
                            </option>
                        @endforeach
                    </select>
                </x-table.table-td>
                <x-table.table-td>
                    <x-text-input type="number" name="locations[{{ $productId }}][quantity]" value="0" class="form-control" />
                </x-table.table-td>
                <x-table.table-td>
                    @if ($outboundRequest->status == 'Requested')
                        <button type="button"
                             class="ml-3 bg-red-500 text-sm text-white px-4 py-1 rounded-md hover:bg-red-700 remove-location">
                             Remove
                        </button>
                     @endif
                </x-table.table-td>
            </x-table.table-tr>
        @endforeach
    </x-table.table-tbody>
</x-table.table-table>

        <input type="hidden" id="deleted_locations" name="deleted_locations" value="">
            
        <div class="my-4 ">
            <x-input-label for="notes">Notes</x-input-label>
            <x-input-textarea name="notes" class="form-control" placeholder="Optional notes">{{ $outboundRequest->notes }}</x-input-textarea>
        </div>

        @if ($outboundRequest->status != 'Requested' && $outboundRequest->status != 'Pending Confirmation')
        <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>
	
        <h3>Expedition Details</h3>
			<div class="mb-4">
				<x-input-label for="expedition_id">Expedition</x-input-label>
				<select name="expedition_id" class="bg-gray-100 w-full px-4 py-2 border rounded-md focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:text-white {{ $outboundRequest->status != 'Packing & Shipping' ? 'readonly-select' : '' }}" {{ $outboundRequest->status != 'Packing & Shipping' ? 'readonly' : '' }}>
					@foreach($expeditions as $expedition)
						<option value="{{ $expedition->id }}" {{ $outboundRequest->expedition_id == $expedition->id ? 'selected' : '' }}>
							{{ $expedition->name }}
						</option>
					@endforeach
				</select>
			</div>
            <div class="mb-4">
                <x-input-label for="tracking_number">Tracking Number</x-input-label>
                <x-text-input type="text" name="tracking_number" class="form-control" value="{{ $outboundRequest->tracking_number }}" {{ $outboundRequest->status != 'Packing & Shipping' ? 'readonly' : '' }}/>
            </div>
            <div class="mb-4">
                <x-input-label for="real_volume">Real Volume (m³)</x-input-label>
                <x-text-input type="number" step="0.01" name="real_volume" class="form-control" value="{{ $outboundRequest->real_volume }}" {{ $outboundRequest->status != 'Packing & Shipping' ? 'readonly' : '' }}/>
            </div>
            <div class="mb-4">
                <x-input-label for="real_weight">Real Weight (kg)</x-input-label>
                <x-text-input type="number" step="0.01" name="real_weight" class="form-control" value="{{ $outboundRequest->real_weight }}" {{ $outboundRequest->status != 'Packing & Shipping' ? 'readonly' : '' }}/>
            </div>
			<div class="form-group">
                <x-input-label for="packing_fee">Packing Fee</x-input-label>
                <x-text-input type="number" step="0.01" name="packing_fee" class="form-control" value="{{ $outboundRequest->packing_fee }}" {{ $outboundRequest->status != 'Packing & Shipping' ? 'readonly' : '' }}/>
            </div>
            <div class="form-group">
                <x-input-label for="real_shipping_fee">Real Shipping Fee</x-input-label>
                <x-text-input type="number" step="0.01" name="real_shipping_fee" class="form-control" value="{{ $outboundRequest->real_shipping_fee }}" {{ $outboundRequest->status != 'Packing & Shipping' ? 'readonly' : '' }}/>
            </div>
        @endif

		<!-- Status Display -->
            <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>
            <div>

            </div>
            <div class="rounded-lg border border-gray-300 dark:border-gray-700 mb-4">

            <div class="flex p-3 justify-between align-center">
            <h3 class="py-1 text-md font-medium dark:text-white">Sales Status :</h3>
                <span class="font-medium text-yellow-800 inline-block bg-yellow-200 text-black px-3 py-1 rounded-full">{{ $outboundRequest->sales->status }}</span>
            </div>

            <div class="flex-grow border-t border-gray-300 dark:border-gray-700"></div>

            <div class="flex p-3 justify-between align-center">
            <h3 class="py-1 text-md font-medium dark:text-white">Outbound Status :</h3>
                <span class="font-medium text-yellow-800 inline-block bg-yellow-200 text-black px-3 py-1 rounded-full" readonly>{{ $outboundRequest->status }}</span>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end gap-4">
        @switch($outboundRequest->status)
            @case('Requested')
            <x-sec-button type="submit">Verify Stock and Approve</x-sec-button>
            <x-danger-button type="submit">Reject Request</x-danger-button>
            @break

            @case('Pending Confirmation')
                @break

            @case('Packing & Shipping')
                <x-primary-button  name='submit' type="submit" class="btn btn-warning" value="Mark as Shipped">Mark as Shipped</x-primary-button >
                @break

            @case('In Transit')
                <h4 class="text-md font-bold dark:text-white">Waiting Sales Confirmation about Received Quantities by Customer before Complain or Ready to Complete</h4>
                @break

            @case('Customer Complaint')
            <x-sec-button type="submit">Resolve Quantity Problem</x-sec-button>
            @break
        @endswitch
        </div>

        <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>
        <div class="flex justify-start gap-4">
            <x-primary-button type="submit">Update Outbound Request</x-primary-button>
            <x-secondary-button href="{{ route('outbound_requests.index') }}"
            class="border rounded border-gray-400 dark:border-gray-700 p-2 text-lg hover:underline text-gray-700 dark:text-gray-400">Back to List</x-secondary-button>    
        </div>
    </form>
	
	<script>
        // Dynamically update stock in warehouse based on selected warehouse
        document.getElementById('warehouse_id').addEventListener('change', function () {
            const warehouseId = this.value;

            @foreach ($outboundRequest->requested_quantities as $productId => $quantity)
                fetch(`{{ route('inventory.index') }}/getProductStock/warehouses/${warehouseId}/products/{{ $productId }}/stock`)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById(`stock-in-warehouse-{{ $productId }}`).innerText = data.stock || '0';
                    });
            @endforeach
        });
    </script>
	
	<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.add-location').forEach((button) => {
            button.addEventListener('click', function () {
                const productId = this.dataset.productId;
                const tbody = document.getElementById(`locations-${productId}`);
                const rowCount = tbody.querySelectorAll('tr').length;

                var select = [];
                @foreach ($availableLocations as $productId => $locations)  
                select[{{$productId}}] = '';
                    @foreach ($locations as $location) 
                select[{{$productId}}] += `<option value="{{ $location->id }}">Room: {{ $location->room }}, Rack: {{ $location->rack }} (Available: {{ $location->quantity }})</option>`;
                    @endforeach
                @endforeach

                // Add a new row
                const newRow = `
                    <x-table.table-tr>
                        <x-table.table-td>
                            <select name="locations[${productId}][${rowCount}][location_id]" class="bg-gray-100 w-full px-4 py-2 border rounded-md focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:text-white location-select">
                                <option value="" selected>Select a location</option>
                                ` + select[productId] + `
                            </select>
                        </x-table.table-td>
                        <x-table.table-td>
                            <input type="number" name="locations[${productId}][${rowCount}][quantity]" class="form-control" value="0">
                        </x-table.table-td>
                        <x-table.table-td>
                            <x-button2 type="button" class="remove-location mr-3 bg-blue-700">Add Another Product</x-button2>
                        </x-table.table-td>
                    </x-table.table-tr>
                `;
                tbody.insertAdjacentHTML('beforeend', newRow);

                // Reattach remove event listeners
                attachRemoveListeners();
            });
        });

        // Attach event listeners for all remove-location buttons
        function attachRemoveListeners() {
            document.querySelectorAll('.remove-location').forEach((removeButton) => {
                removeButton.addEventListener('click', function () {
                    const row = this.closest('tr');
                    const locationId = row.querySelector('select[name*="[location_id]"]').value;
                    if (locationId) {
                        const deletedLocationsInput = document.getElementById('deleted_locations');
                        deletedLocationsInput.value += `${locationId},`;
                    }
                    row.remove();
                });
            });
        }

        // Initial attachment of remove listeners
        attachRemoveListeners();
    });
    </script>
	
	<script>
    // JavaScript to prevent selection changes on readonly-select elements
    document.querySelectorAll('.readonly-select').forEach(function(select) {
        select.addEventListener('mousedown', function(event) {
            event.preventDefault();
        });
        select.addEventListener('click', function(event) {
            event.preventDefault();
        });
        select.addEventListener('change', function(event) {
            event.preventDefault();
        });
    });
	</script>
    </div> </div> </div> </div>
</x-company-layout>
