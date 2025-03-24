<x-company-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Location') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
			<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
				<div class="p-6 text-gray-900 dark:text-white">
					<h1 class="text-2xl font-bold mb-4">Edit Location</h1>
					<div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>

					<form action="{{ route('warehouse_locations.update', $location->id) }}" method="POST">
						@csrf
						@method('PUT')
						<div class="form-group mb-4">
							<x-input-label for="warehouse">Warehouse</x-input-label>
							<x-text-input type="text" class="w-full" value="{{ $location->warehouse->name }}" readonly />
							<input type="hidden" name="warehouse_id" value="{{ $location->warehouse_id }}">
						</div>

						<!-- Room Selection and Input -->
						<div class="form-group mb-4">
							<x-input-label for="room">Room</x-input-label>
							<x-input-select name="existing_room" class="form-control" id="existing-room-select">
								<option value="">-- Select Existing Room --</option>
								@foreach ($existingRooms as $room)
									<option value="{{ $room }}" {{ $location->room == $room ? 'selected' : '' }}>
										{{ $room }}
									</option>
								@endforeach
							</x-input-select>

							<x-input-label for="new_room" class="mt-2">Or enter a new room</x-input-label>
							<x-text-input type="text" name="new_room" id="new-room-input" class="form-control" placeholder="Enter new room name" />
						</div>

						<div class="form-group mb-4">
							<x-input-label for="rack">Rack</x-input-label>
							<x-text-input type="text" name="rack" class="form-control" value="{{ $location->rack }}" required />
						</div>
						
						<div class="mb-3 mt-1 flex-grow border-t border-gray-300 dark:border-gray-700"></div>
						<div class="flex justify space-x-4">
							<a href="{{ route('warehouse_locations.index') }}">
								<x-secondary-button type="button">Cancel</x-secondary-button>
							</a>
							<x-primary-button type="submit">Update Location</x-primary-button>
						</div>
					</form>
				</div>
			</div>
        </div>
    </div>

    <script>
        document.getElementById('existing-room-select').addEventListener('change', function() {
            if (this.value) {
                document.getElementById('new-room-input').value = '';
            }
        });

        document.getElementById('new-room-input').addEventListener('input', function() {
            if (this.value) {
                document.getElementById('existing-room-select').value = '';
            }
        });
    </script>
</x-company-layout>

