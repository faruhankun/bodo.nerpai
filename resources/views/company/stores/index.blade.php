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
                    <x-table-table id="search-table">
                        <x-table-thead>
                            <tr>
                                <x-table-th>ID</x-table-th>
                                <x-table-th>Kode</x-table-th>
                                <x-table-th>Nama</x-table-th>
                                <x-table-th>Alamat</x-table-th>
                                <x-table-th>Status</x-table-th>
                                <x-table-th>Manager</x-table-th>
                                <x-table-th>Actions</x-table-th>
                            </tr>
                        </x-table-thead>
                        <x-table-tbody>
                            @foreach ($stores as $store)
                                <x-table-tr>
                                    <x-table-td>{{ $store->id }}</x-table-td>
                                    <x-table-td>{{ $store->code }}</x-table-td>
                                    <x-table-td>{{ $store->name }}</x-table-td>
                                    <x-table-td>{{ $store->address }}</x-table-td>
                                    <x-table-td>{{ $store->status }}</x-table-td>
                                    <x-table-td>{{ $store->manager->name ?? 'N/A' }}</x-table-td>
                                    <x-table-td>
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
