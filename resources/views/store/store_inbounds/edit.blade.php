<x-company-layout>
<div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-white"></div>
                <h3 class="text-lg dark:text-white font-bold">Edit Inbound Request</h3>
                <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>
    <form action="{{ route('inbound_requests.update', $inboundRequest->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <x-input-label for="purchase_order_id">Purchase Order</x-input-label>
            <x-text-input type="text" class="form-control" name="purchase_order_id" value="{{ $inboundRequest->purchase->id }}" readonly>
        </div>

        <div class="mb-4">
            <x-input-label for="warehouse_id">Warehouse</x-input-label>
            <x-text-input type="text" class="form-control" name="warehouse_id" value="{{ $inboundRequest->warehouse->name }}" readonly>
        </div>
	
		<div class="mb-4">
			<x-input-label for="arrival_date">Arrival Date</x-input-label>
			<x-text-input type="date" class="form-control" name="arrival_date" 
				   value="{{ $inboundRequest->arrival_date ? $inboundRequest->arrival_date : '' }}"
				   {{ $inboundRequest->status == 'In Transit' ? '' : 'disabled' }}>
		</div>

        <div class="mb-4">
            <x-input-label for="status">Inbound Request Status</x-input-label>
			<x-text-input type="text" class="form-control" name="status" value="{{ $inboundRequest->status }}" readonly>
        </div>

        <h3 class="text-lg dark:text-white font-bold">Product</h3>
		@foreach($inboundRequest->purchase->products as $product)
			@if(isset($inboundRequest->requested_quantities[$product->id]) && $inboundRequest->requested_quantities[$product->id] > 0)
				<div class="mb-4">
					<x-input-label>{{ $product->name }}</x-input-label>
					
					<!-- Display Requested Quantity -->
					<div>
						<x-input-label>Requested Quantity</x-input-label>
						<x-text-input type="number" class="form-control" value="{{ $inboundRequest->requested_quantities[$product->id] }}" readonly>
					</div>
					
					<!-- Editable Received Quantity -->
					<div>
						<x-input-label>Received Quantity</x-input-label>
						<x-text-input type="number" name="received_quantities[{{ $product->id }}]" class="form-control" min="0" value="{{ $inboundRequest->received_quantities[$product->id] ?? 0 }}" {{ $inboundRequest->status == 'Received - Pending Verification' ? '' : 'readonly' }}>
					</div>
				</div>
			@endif
		@endforeach

		<!-- New Check Quantities Button -->
		@if($inboundRequest->status == 'Received - Pending Verification')
			<button type="submit" class="btn btn-warning" name="action" value="check_quantities">Check Quantities</button>
		@endif

        <div class="mb-4">
            <x-input-label for="verified_by">Verified By</x-input-label>
            <select name="verified_by" class="bg-gray-100 w-full px-4 py-2 border rounded-md focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:text-white">
                <option value="">Select User</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ $inboundRequest->verified_by == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-4">
            <x-input-label for="notes">Notes</x-input-label>
            <x-input-textarea name="notes" class="form-control" placeholder="Optional notes">{{ $inboundRequest->notes }}</xtextarea>
        </div>

        <button type="submit" class="btn btn-primary">Update Inbound Request</button>
        <a href="{{ route('inbound_requests.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
                </div>
            </div>
        </div>
    </div>
    </x-company-layout>
