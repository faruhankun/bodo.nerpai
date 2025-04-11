<x-crud.modal-create title="Create Product" trigger="Create Product">
        <form action="{{ route('products.store') }}" method="POST" class="mt-4">
                @csrf
                <div class="flex flex-col gap-6">
                    <div>
                        <x-input-label for="name">Product Name</x-input-label>
                        <x-text-input type="text" name="name" id="name" class="mt-1 block w-full"
                            required x-model="name"></x-text-input>
                    </div>
                    <div>
                        <x-input-label for="sku">SKU</x-input-label>
                        <x-text-input type="text" name="sku" id="sku" class="mt-1 block w-full"
                            required></x-text-input>
                    </div>
                    <div>
                        <x-input-label for="price">Price</x-input-label>
                        <x-text-input type="number" name="price" id="price" class="mt-1 block w-full"
                            required x-model="number"></x-text-input>
                    </div>
                    <div>
                        <x-input-label for="weight">Weight (gram)</x-input-label>
                        <x-text-input type="number" name="weight" id="weight" class="mt-1 block w-full"
                            x-model="weight"></x-text-input>
                    </div>
                    <div>
                        <x-input-label for="status">Status</x-input-label>
                        <x-input-select name="status" id="status" class="mt-1 block w-full" x-model="status">
                            <x-select-option value="Active">Active</x-select-option>
                            <x-select-option value="Inactive">Inactive</x-select-option>
                        </x-input-select>
                    </div>
                    <div>
                        <x-input-label for="notes">Note</x-input-label>
                        <x-input-textarea name="notes" id="notes" class="mt-1 block w-full"
                            x-model="notes"></x-textarea>
                    </div>
                    <div class="flex gap-3 justify-end">
                        <x-secondary-button type="button" @click="isOpen = false">Cancel</x-secondary-button>
                        <x-primary-button type="submit">Create Product</x-primary-button>
                    </div>
                </div>
            </form>
</x-crud.modal-create>