<x-company-layout>
	<div class="py-12">
		<div class="max-w-7xl my-10 mx-auto sm:px-6 lg:px-8">
			<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
				<div class="p-6 text-gray-900">
					<h3 class="text-lg font-bold dark:text-white">Inventory Adjustment</h3>
					<div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>

					<form action="{{ route('inventory.adjust') }}" method="POST">
						@csrf
						<div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
							<div class="form-group">
								<x-input-label for="warehouse_id">Select Warehouse</x-input-label>
								<select name="warehouse_id" id="warehouse_id"
									class="bg-gray-100 w-full px-4 py-2 border rounded-md focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:text-white"
									required>
									<option value="">-- Select Warehouse --</option>
									@foreach($warehouses as $warehouse)
										<option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
									@endforeach
								</select>
							</div>

							<div class="form-group">
								<x-input-label for="product_id">Select Product</x-input-label>
								<select name="product_id" id="product_id"
									class="bg-gray-100 w-full px-4 py-2 border rounded-md focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:text-white"
									required>
									<option value="">-- Select Product --</option>
									@foreach($products as $product)
										<option value="{{ $product->id }}">{{ $product->name }}</option>
									@endforeach
								</select>
							</div>

							<div class="form-group">
								<x-input-label for="quantity">Quantity</x-input-label>
								<x-text-input type="number" name="quantity" class="w-full bg-gray-100 form-control"
									min="1" required></x-text-input>
							</div>

							<div class="form-group">
								<x-input-label for="transaction_type">Transaction Type</x-input-label>
								<select name="transaction_type"
									class="bg-gray-100 w-full px-4 py-2 border rounded-md focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:text-white"
									required>
									<option value="Addition">Addition</option>
									<option value="Reduction">Reduction</option>
								</select>
							</div>

							<!-- Room Selection -->
							<div class="form-group">
								<x-input-label for="room">Room</x-input-label>
								<select name="room" id="room"
									class="bg-gray-100 w-full px-4 py-2 border rounded-md focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:text-white"
									required>
									<option value="">-- Select Room --</option>
									<!-- Options will be populated dynamically -->
								</select>
							</div>

							<div class="form-group">
								<x-input-label for="rack">Rack</x-input-label>
								<select name="rack" id="rack"
									class="bg-gray-100 w-full px-4 py-2 border rounded-md focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:text-white"
									required>
									<option value="">-- Select Rack --</option>
									<!-- Options will be populated dynamically -->
								</select>
							</div>

							<div class="form-group">
								<x-input-label for="notes">Notes</x-input-label>
								<x-input-textarea name="notes" class="form-control"
									placeholder="Optional notes"></x-input-textarea>
							</div>
						</div>
						<div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>
						<div class="flex justify-end">
							<x-primary-button>Adjust Inventory</x-primary-button>
							<a href="{{ route('inventory.index') }}"
								class="border rounded border-gray-400 dark:border-gray-700 p-2 ml-3 text-sm hover:underline text-gray-700 dark:text-gray-400">Cancel</a>
						</div>
					</form>

					<!-- JavaScript for Dynamic Rack Filtering based on Room Selection -->
					<script>
						document.addEventListener("DOMContentLoaded", function () {
							document.getElementById('warehouse_id').addEventListener('change', function () {
								const warehouseId = this.value;
								const roomSelect = document.getElementById('room');
								const rackSelect = document.getElementById('rack');

								roomSelect.innerHTML = '<option value="">-- Select Room --</option>';
								rackSelect.innerHTML = '<option value="">-- Select Rack --</option>';

								if (warehouseId) {
									fetch(`{{ route('inventory.index') }}/get-locations/${warehouseId}`)
										.then(response => response.json())
										.then(locations => {
											// Get unique rooms by filtering out duplicates
											const uniqueRooms = [...new Set(locations.map(location => location.room))];

											// Clear the room select options
											roomSelect.innerHTML = '<option value="">-- Select Room --</option>';

											// Populate the room dropdown with unique rooms
											uniqueRooms.forEach(room => {
												const option = document.createElement('option');
												option.value = room;
												option.textContent = room;
												roomSelect.appendChild(option);
											});
										});

								}
							});

							document.getElementById('room').addEventListener('change', function () {
								const selectedRoom = this.value;
								const rackSelect = document.getElementById('rack');
								rackSelect.innerHTML = '<option value="">-- Select Rack --</option>';

								const warehouseId = document.getElementById('warehouse_id').value;
								if (warehouseId && selectedRoom) {
									fetch(`{{ route('inventory.index') }}/get-locations/${warehouseId}`)
										.then(response => response.json())
										.then(locations => {
											locations
												.filter(location => location.room === selectedRoom)
												.forEach(location => {
													const option = document.createElement('option');
													option.value = location.rack;
													option.textContent = location.rack;
													rackSelect.appendChild(option);
												});
										});
								}
							});
						});
					</script>

				</div>
			</div>
		</div>
	</div>
</x-company-layout>