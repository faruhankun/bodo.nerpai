<x-company-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Products') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-white">
                    <h3 class="text-lg font-bold dark:text-white">Manage Products</h3>
                    <p class="text-sm dark:text-gray-200 mb-6">Create, edit, and manage your product listings.</p>
                    <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>

                    <div
                        class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 mb-4">
                        
                        <div class="w-full md:w-auto flex justify-end">
                            <a href="{{ route('products.create') }}">
                                <x-button-add :route="route('products.create')" text="Add Product" />
                            </a>
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
                            @foreach ($products as $product)
                                <x-table-tr>
                                    <x-table-td>{{ $product->id }}</x-table-td>
                                    <x-table-td>{{ $product->sku }}</x-table-td>
                                    <x-table-td>{{ $product->name }}</x-table-td>
                                    <x-table-td>{{ $product->price }}</x-table-td>
                                    <x-table-td>{{ $product->status }}</x-table-td>
                                    <x-table-td>{{ $product->notes }}</x-table-td>
                                    <x-table-td>
                                        <div class="flex items-center space-x-2">
                                            <x-button-show :route="route('products.show', $product->id)" />
                                            <x-button-edit :route="route('products.edit', $product->id)" />
                                            <x-button-delete :route="route('products.destroy', $product->id)" />
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
   
</x-company-layout>
