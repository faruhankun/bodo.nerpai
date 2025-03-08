<x-modal-edit trigger="Update Store Customers" title="Add Store Customers">
    <form action="{{ route('store_customers.update', $store_customer->id) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Customer --}}
        <div class="form-group mb-4">
            <x-input-label for="customer_id" class="block text-sm font-medium text-gray-700">Customer</x-input-label>
            <select name="customer_id" id="customer_id"
                class="w-full px-4 py-2 border rounded dark:bg-gray-700 dark:text-white" disabled>
                <option value="{{ $store_customer->id }}">{{ $store_customer->customer->name }}</option>
            </select>
        </div>

        {{-- Status --}}
        <div class="form-group mb-4">
            <x-input-label for="status" class="block text-sm font-medium text-gray-700">Status</x-input-label>
            <x-input-select name="status" id="status" class="mt-1 block w-full" x-model="status" required>
                <x-select-option value="Active" :selected="$store_customer->status === 'Active'">Active</x-select-option>
                <x-select-option value="Inactive" :selected="$store_customer->status === 'Inactive'">Inactive</x-select-option>
            </x-input-select>
        </div>

        {{-- Notes --}}
        <div class="form-group mb-4">
            <x-input-label for="notes" class="block text-sm font-medium text-gray-700">Notes</x-input-label>
            <x-input-textarea name="notes" id="notes" class="w-full"
                placeholder="Optional notes" value="{{ $store_customer->notes }}" ></x-input-textarea>
        </div>

        <!-- Actions -->
        <div class="flex justify-end space-x-4 mt-4">
            <x-primary-button type="submit">Edit Customer</x-primary-button>
            <button type="button" @click="isOpen = false"
                class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-700">Cancel</button>
        </div>
    </form>
</x-modal-edit>
