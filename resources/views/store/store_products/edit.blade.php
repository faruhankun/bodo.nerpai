<x-store-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Store Product: ') . $store_product->product->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl my-10 mx-auto sm:px-6 lg:px-8">
            <div
                class="bg-white border-b border-gray-500 dark:bg-gray-800 dark:border-gray-700 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-white">
                    <h3 class="text-lg font-medium leading-tight mb-4">
                        Update Store Product Details
                    </h3>

                    <form action="{{ route('store_products.update', $store_product->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="flex flex-col gap-6">
                            <div>
                                <x-input-label for="sku">SKU</x-input-label>
                                <x-text-input type="text" name="sku" id="sku" class="mt-1 block w-full"
                                    x-model="sku" required value="{{ $store_product->product->sku }}"
                                    disabled></x-text-input>
                            </div>
                            <div>
                                <x-input-label for="store_price">Store Price</x-input-label>
                                <x-text-input type="number" name="store_price" id="store_price" class="mt-1 block w-full"
                                    x-model="price" required value="{{ $store_product->store_price }}"></x-text-input>
                            </div>
                            <div>
                                <x-input-label for="status">Status</x-input-label>
                                <x-input-select name="status" id="status" class="mt-1 block w-full" x-model="status"
                                    required>
                                    <x-select-option value="Active" :selected="$store_product->status === 'Active'">Active</x-select-option>
                                    <x-select-option value="Inactive" :selected="$store_product->status === 'Inactive'">Inactive</x-select-option>
                                </x-input-select>
                            </div>
                            <div>
                                <x-input-label for="notes">Notes</x-input-label>
                                <x-input-textarea name="notes" id="notes" class="mt-1 block w-full"
                                    :value="$store_product->notes" x-model="value">
                                </x-input-textarea>
                            </div>
                        </div>
                        <div class="flex gap-3 justify-end mt-4">
                            <a href="{{ route('store_products.index') }}">
                                <x-secondary-button type="button">Cancel</x-secondary-button>
                            </a>
                            <x-primary-button type="submit">Update Product</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-store-layout>
