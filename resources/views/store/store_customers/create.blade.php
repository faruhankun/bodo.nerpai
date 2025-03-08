<x-modal-create trigger="Add Store Customers" title="Add Store Customers">
    <form action="{{ route('store_customers.store') }}" method="POST">
        @csrf

        {{-- Customer --}}
        <div class="form-group mb-4">
            <x-input-label for="customer_id" class="block text-sm font-medium text-gray-700">Customer</x-input-label>
            <select name="customer_id" id="customer_id"
                class="w-full px-4 py-2 border rounded dark:bg-gray-700 dark:text-white">
                @foreach ($customers as $customer)
                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Status --}}
        <div class="form-group mb-4">
            <x-input-label for="status" class="block text-sm font-medium text-gray-700">Status</x-input-label>
            <select name="status" id="status"
                class="w-full px-4 py-2 border rounded dark:bg-gray-700 dark:text-white">
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
            </select>
        </div>

        {{-- Notes --}}
        <div class="form-group mb-4">
            <x-input-label for="notes" class="block text-sm font-medium text-gray-700">Notes</x-input-label>
            <x-input-textarea name="notes" id="notes" class="w-full"
                placeholder="Optional notes"></x-input-textarea>
        </div>

        <!-- Actions -->
        <div class="flex justify-end space-x-4 mt-4">
            <x-primary-button type="submit">Add Store Customers</x-primary-button>
            <button type="button" @click="isOpen = false"
                class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-700">Cancel</button>
        </div>
    </form>
</x-modal-create>
