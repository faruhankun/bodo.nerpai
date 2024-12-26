<x-app-layout>
    <div class="py-12">
        <div class="my-10 max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                        Add Product
                    </h1>
                    <form action="{{ route('products.store') }}" method="POST">
                        @csrf
                        <div class="flex flex-col gap-6">
                            <div>
                                <x-input-label for="name">Product Name</x-input-label>
                                <x-input-input type="text" name="name" required x-model="name" />
                            </div>
                            <div>
                                <x-input-label for="sku">SKU</x-input-label>
                                <x-input-input type="text" name="sku" x-model="sku" />
                            </div>
                            <div>
                                <x-input-label for="price">Price</x-input-label>
                                <x-input-input type="number" name="price" required x-model="number" />
                            </div>
                            <div>
                                <x-input-label for="weight">Weight</x-input-label>
                                <x-input-input type="number" name="weight" x-model="weight" />
                            </div>
                            <div>
                                <x-input-label for="status">Status</x-input-label>
                                <x-input-select name="status" x-model="status">
                                    <x-select-option value="Active">Active</x-select-option>
                                    <x-select-option value="Inactive">Inactive</x-select-option>
                                </x-input-select>
                            </div>
                            <div>
                                <x-input-label for="notes">Note</x-input-label>
                                <x-input-textarea name="notes" x-model="notes"></x-input-textarea>
                            </div>
                            <div class="flex gap-3 justify-end">
                                <a href="{{ route('products.index') }}">
                                    <x-button type="button" variant="secondary">Cancel</x-button>
                                </a>
                                <x-button type="submit" variant="primary">Create Product</x-button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
