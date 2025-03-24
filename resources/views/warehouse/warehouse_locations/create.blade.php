<x-company-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 leading-tight">
			{{ __('Sales Orders') }}
		</h2>
	</x-slot>

	<div class="py-12">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
			<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
				<div class="p-6 text-gray-900 dark:text-white">
					<form action="{{ route('warehouse_locations.store') }}" method="POST">
						@csrf
						<div class="mb-4">
						<x-input-label for="warehouse_id">Warehouse</x-input-label>
						<select id="warehouse-select" name="warehouse_id"
								class="bg-gray-100 w-full px-4 py-2 border rounded-md focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:text-white"
								required>
								@foreach($warehouses as $warehouse)
									<option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
								@endforeach
							</select>
						</div>

						<div class="mb-4">
						<x-input-label for="room">Room</x-input-label>
						<select name="existing_room" id="existing-room-select"
								class="bg-gray-100 w-full px-4 py-2 border rounded-md focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:text-white">
								@foreach($warehouses as $warehouse)
									@foreach($warehouse->warehouse_locations->unique('room') as $location)
										<option value="{{ $location->room }}" data-warehouse="{{ $warehouse->id }}">
											{{ $location->room }}
										</option>
									@endforeach
								@endforeach
							</select>

							<x-input-label for="new_room">or enter a new room</x-input-label>
							<input type="text" name="new_room" id="new-room-input"
								class="bg-gray-200 w-full px-4 py-2 border rounded-md focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:text-white"
								placeholder="Enter new room name">
						</div>

						<div class="mb-4">
							<x-input-label for="rack">Rack</x-input-label>
							<input type="text" name="rack"
								class="bg-gray-200 w-full px-4 py-2 border rounded-md focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:text-white" placeholder="Enter rack name"
								required>
						</div>
						<div class="flex justify-end">
							<x-primary-button>Add Location</x-primary-button>
							<a href="{{ route('warehouse_locations.index') }}"
								class="border rounded border-gray-400 dark:border-gray-700 p-2 ml-3 text-sm hover:underline text-gray-700 dark:text-gray-400">Cancel</a>
						</div>
					</form>

					
					<script>
						document.getElementById('warehouse-select').addEventListener('change', function () {
							const selectedWarehouse = this.value;
							const roomSelect = document.getElementById('existing-room-select');

							Array.from(roomSelect.options).forEach(option => {
								if (option.dataset.warehouse === selectedWarehouse || option.value === "") {
									option.style.display = 'block';
								} else {
									option.style.display = 'none';
								}
							});

							// Reset room selection if it doesn’t match the selected warehouse
							roomSelect.selectedIndex = 0;
						});

						document.getElementById('existing-room-select').addEventListener('change', function () {
							if (this.value) {
								document.getElementById('new-room-input').value = '';
							}
						});

						document.getElementById('new-room-input').addEventListener('input', function () {
							if (this.value) {
								document.getElementById('existing-room-select').value = '';
							}
						});
					</script>

				</div>
			</div>
		</div>
	</div>
</x-company-layout>