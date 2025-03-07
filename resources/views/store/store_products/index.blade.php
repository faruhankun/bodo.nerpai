@php
    $layout = session('layout');
@endphp

<x-dynamic-component :component="'layouts.' . $layout">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Store Products') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-white">
                    <h3 class="text-lg font-bold dark:text-white">Manage Store Products</h3>
                    <p class="text-sm dark:text-gray-200 mb-6">Create, edit, and manage your store product listings.</p>
                    <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>

                    <div
                        class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 mb-4">
                        <div class="flex flex-col md:flex-row items-center space-x-3">
                            @include('store.store_products.create')
                        </div>
                    </div>

                    <x-table-table id="search-table">
                        <x-table-thead>
                            <tr>
                                <x-table-th>ID</x-table-th>
                                <x-table-th>Sku</x-table-th>
                                <x-table-th>Name</x-table-th>
                                <x-table-th>Price</x-table-th>
                                <x-table-th>Status</x-table-th>
                                <x-table-th>Notes</x-table-th>
                                <x-table-th>Actions</x-table-th>
                            </tr>
                        </x-table-thead>
                        <x-table-tbody>
                            @foreach ($store_products as $store_product)
                                <x-table-tr>
                                    <x-table-td>{{ $store_product->id }}</x-table-td>
                                    <x-table-td>{{ $store_product->product->sku }}</x-table-td>
                                    <x-table-td>{{ $store_product->product->name }}</x-table-td>
                                    <x-table-td>{{ $store_product->store_price }}</x-table-td>
                                    <x-table-td>{{ $store_product->status }}</x-table-td>
                                    <x-table-td>{{ $store_product->notes }}</x-table-td>
                                    <x-table-td>
                                        <div class="flex items-center space-x-2">
                                            <x-button-show :route="route('store_products.show', $store_product->id)" />
                                            <x-button-edit :route="route('store_products.edit', $store_product->id)" />
                                            <x-button-delete :route="route('store_products.destroy', $store_product->id)" />
                                        </div>
                                    </x-table-td>
                                </x-table-tr>
                            @endforeach
                        </x-table-tbody>
                    </x-table-table>
                </div>
            </div>
        </div>
    </div>
</x-dynamic-component>
