<x-company-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Couriers') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-white">
                    <h3 class="text-lg font-bold dark:text-white">Manage Couriers</h3>
                    <p class="text-sm dark:text-gray-200 mb-6">Create, edit, and manage your courier listings.</p>
                    <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>

                    <!-- Actions -->
                    <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 mb-4">
                        <div class="flex flex-col md:flex-row items-center space-x-3">
                                @include('company.couriers.create')
                         </div>
                    </div>

                    <x-table.table-table id="search-table">
                        <x-table.table-thead>
                            <tr>
                                <x-table.table-th>ID</x-table.table-th>
                                <x-table.table-th>Kode</x-table.table-th>
                                <x-table.table-th>Nama</x-table.table-th>
                                <x-table.table-th>Kontak Info</x-table.table-th>
                                <x-table.table-th>Website</x-table.table-th>
                                <x-table.table-th>Status</x-table.table-th>
                                <x-table.table-th>Notes</x-table.table-th>
                                <x-table.table-th>Actions</x-table.table-th>
                            </tr>
                        </x-table.table-thead>
                        <x-table.table-tbody>
                            @foreach ($couriers as $courier)
                                <x-table.table-tr>
                                    <x-table.table-td>{{ $courier->id }}</x-table.table-td>
                                    <x-table.table-td>{{ $courier->code }}</x-table.table-td>
                                    <x-table.table-td>{{ $courier->name }}</x-table.table-td>
                                    <x-table.table-td>{{ $courier->contact_info }}</x-table.table-td>
                                    <x-table.table-td>{{ $courier->website }}</x-table.table-td>
                                    <x-table.table-td>{{ $courier->status }}</x-table.table-td>
                                    <x-table.table-td>{{ $courier->notes }}</x-table.table-td>
                                    <x-table.table-td>
                                        <div class="flex items-center space-x-2">
                                            <!-- <x-button-show :route="route('couriers.show', $courier->id)" /> -->
                                            <x-button-edit :route="route('couriers.edit', $courier->id)" />
                                            <x-button-delete :route="route('couriers.destroy', $courier->id)" />
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
