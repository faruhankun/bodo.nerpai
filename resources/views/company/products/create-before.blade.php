<x-company-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add Product') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div
                class="bg-white border-b border-gray-500 dark:bg-gray-800 dark:border-gray-700 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-white">
                    <h3 class="text-lg font-medium leading-tight mb-4">Create a New Product</h3>

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
                                    x-model="sku"></x-text-input>
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
                                <a href="{{ route('products.index') }}">
                                    <x-secondary-button type="button">Cancel</x-secondary-button>
                                </a>
                                <x-primary-button type="submit">Create Product</x-primary-button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-company-layout>
