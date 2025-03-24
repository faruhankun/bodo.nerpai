<x-company-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Product Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div
                class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-2xl font-semibold text-gray-800 dark:text-gray-200 mb-6">Product Information</h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div
                            class="p-4 border border-gray-200 rounded-lg shadow-md dark:bg-gray-700 dark:border-gray-600">
                            <p class="text-sm text-gray-500 dark:text-gray-300">Product Name</p>
                            <p class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ $product->name }}</p>
                        </div>
                        <div
                            class="p-4 border border-gray-200 rounded-lg shadow-md dark:bg-gray-700 dark:border-gray-600">
                            <p class="text-sm text-gray-500 dark:text-gray-300">SKU</p>
                            <p class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ $product->sku }}</p>
                        </div>
                        <div
                            class="p-4 border border-gray-200 rounded-lg shadow-md dark:bg-gray-700 dark:border-gray-600">
                            <p class="text-sm text-gray-500 dark:text-gray-300">Price</p>
                            <p class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                Rp. {{ number_format($product->price, 0, ',', '.') }}</p>
                        </div>
                        <div
                            class="p-4 border border-gray-200 rounded-lg shadow-md dark:bg-gray-700 dark:border-gray-600">
                            <p class="text-sm text-gray-500 dark:text-gray-300">Weight (gram)</p>
                            <p class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ $product->weight }}</p>
                        </div>
                        <div
                            class="p-4 border border-gray-200 rounded-lg shadow-md dark:bg-gray-700 dark:border-gray-600">
                            <p class="text-sm text-gray-500 dark:text-gray-300">Status</p>
                            <p class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ $product->status }}</p>
                        </div>
                        <div
                            class="p-4 border border-gray-200 rounded-lg shadow-md dark:bg-gray-700 dark:border-gray-600">
                            <p class="text-sm text-gray-500 dark:text-gray-300">Notes</p>
                            <p class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ $product->notes ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <div class="flex gap-3 justify-end mt-8">
                        <a href="{{ route('products.index') }}">
                            <x-secondary-button type="button">Cancel</x-secondary-button>
                        </a>
                        <a href="{{ route('products.edit', $product->id) }}">
                            <x-primary-button type="button">Edit Product</x-primary-button>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-company-layout>
