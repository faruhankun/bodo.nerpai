<x-modal-create trigger="Add Store Products" title="Add Store Products">
    <form action="{{ route('store_products.store') }}" method="POST">
        @csrf

        <div class="form-group mb-4">
            <x-input-label for="product_id" class="block text-sm font-medium text-gray-700">Product</x-input-label>
            <select name="product_id" id="product_id"
                class="w-full px-4 py-2 border rounded dark:bg-gray-700 dark:text-white"
                @change="document.getElementById('store_price').value = event.target.selectedOptions[0].dataset.price">
                @foreach ($products as $product)
                    <option value="{{ $product->id }}" data-price="{{ $product->price }}">{{ $product->name }} -
                        Rp{{ $product->price }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group mb-4">
            <x-input-label for="store_price" class="block text-sm font-medium text-gray-700">Store Price</x-input-label>
            <x-text-input type="number" name="store_price" class="w-full" id="store_price"
                :value="$products->first()->price ?? 0"></x-text-input>
        </div>

        <div class="form-group mb-4">
            <x-input-label for="status" class="block text-sm font-medium text-gray-700">Status</x-input-label>
            <select name="status" id="status"
                class="w-full px-4 py-2 border rounded dark:bg-gray-700 dark:text-white">
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
            </select>
        </div>

        <div class="form-group mb-4">
            <x-input-label for="notes" class="block text-sm font-medium text-gray-700">Notes</x-input-label>
            <x-input-textarea name="notes" id="notes" class="w-full"
                placeholder="Optional notes"></x-input-textarea>
        </div>

        <!-- Actions -->
        <div class="flex justify-end space-x-4 mt-4">
            <x-primary-button type="submit">Add Store Products</x-primary-button>
            <button type="button" @click="isOpen = false"
                class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-700">Cancel</button>
        </div>
    </form>
</x-modal-create>
