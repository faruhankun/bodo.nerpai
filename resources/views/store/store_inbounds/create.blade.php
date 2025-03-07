<x-company-layout>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-white">
                <div class="p-6 text-gray-900 dark:text-white"></div>
                <h3 class="text-lg dark:text-white font-bold">Add Inbound Request</h3>
                        <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>

                <form action="{{ route('inbound_requests.store') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <x-input-label for="purchase_order_id">Purchase Order</x-input-label>
                        <x-text-input type="text" class="form-control" name="purchase_order_id" value="{{ $purchase->id }}"
                            readonly>
                    </div>

                    <div class="mb-4">
                        <x-input-label for="warehouse_id">Select Warehouse</x-input-label>
                        <select name="warehouse_id" class="w-full px-4 py-2 border rounded-md focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:text-white" required>
                            <option value="{{ $purchase->warehouse_id }}" selected>{{ $purchase->warehouse->name }}
                            </option>
                        </select>
                    </div>
                    <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>

                    <h3>Received Quantities</h3>
                    @foreach($purchase->products as $product)
                        <div class="mb-4">
                            <x-input-label for="received_quantities[{{ $product->id }}]">{{ $product->name }}</x-input-label>
                            <x-text-input type="number" name="received_quantities[{{ $product->id }}]" class="form-control" min="0"
                                placeholder="Enter quantity received">
                        </div>
                    @endforeach

                    <div class="mb-4">
                        <x-input-label for="notes">Notes</x-input-label>
                        <x-input-textarea name="notes" class="form-control" placeholder="Optional notes"></x-input-textarea>
                    </div>

                    <x-primary-button>Create Inbound Request</x-primary-button>
                    <a href="{{ route('sales.index') }}"
                    class="border rounded border-gray-400 dark:border-gray-700 p-2 ml-3 text-sm hover:underline text-gray-700 dark:text-gray-400">Cancel</a>
                   </form>
            </div>
        </div>
    </div>
    </div>
</x-company-layout>