<x-company-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight dark:text-gray-200">
            {{ __('Our Stores') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-white">
                    <h1 class="text-2xl font-bold mb-6">Stores List</h1>
                    <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>

                    <!-- Actions -->
                    <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 mb-4">
                        <div class="flex flex-col md:flex-row items-center space-x-3">
                                @include('company.stores.create')
                         </div>
                    </div>

                    <!-- store Table -->
                    <x-table.table-table id="search-table">
                        <x-table.table-thead>
                            <tr>
                                <x-table.table-th>ID</x-table.table-th>
                                <x-table.table-th>Kode</x-table.table-th>
                                <x-table.table-th>Nama</x-table.table-th>
                                <x-table.table-th>Alamat</x-table.table-th>
                                <x-table.table-th>Status</x-table.table-th>
                                <x-table.table-th>Manager</x-table.table-th>
                                <x-table.table-th>Actions</x-table.table-th>
                            </tr>
                        </x-table.table-thead>
                        <x-table.table-tbody>
                            @foreach ($stores as $store)
                                <x-table.table-tr>
                                    <x-table.table-td>{{ $store->id }}</x-table.table-td>
                                    <x-table.table-td>{{ $store->code }}</x-table.table-td>
                                    <x-table.table-td>{{ $store->name }}</x-table.table-td>
                                    <x-table.table-td>{{ $store->address }}</x-table.table-td>
                                    <x-table.table-td>{{ $store->status }}</x-table.table-td>
                                    <x-table.table-td>{{ $store->manager->name ?? 'N/A' }}</x-table.table-td>
                                    <x-table.table-td>
                                        <div class="flex items-center space-x-2">
                                            <form method="POST" action="{{ route('stores.switch', $store->id) }}">
                                                @csrf
            
                                                <x-primary-button :href="route('stores.switch', $store->id)" onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                                                    {{ __('Masuk Store') }}
                                                    </x-primary-button>
                                            </form>
                                            <x-button-delete :route="route('stores.destroy', $store->id)" />
                                        </div>
                                    </x-table.table-td>
                                </x-table.table-tr>
                            @endforeach
                        </x-table.table-tbody>
                    </x-table.table-table>
                </div>
            </div>
        </div>
    </div>
    
</x-company-layout>
