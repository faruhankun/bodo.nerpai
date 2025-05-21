<x-company-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Locations') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-white">
                    <h3 class="text-lg font-bold dark:text-white mb-4">Manage Locations</h3>
                    <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>
                    <div
                        class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 mb-4">
                        <div class="w-full md:w-auto flex justify-end">

                            <a href="">
                                <x-button-add :route="route('locations.create')" text="Add New Location" />
                            </a>
                        </div>
                    </div>


                    @foreach($warehouses as $warehouse)
                        <x-input-label>{{ $warehouse->name }}</x-input-label>
                        <x-table.table-table class="search-table">
                            <x-table.table-thead>
                                <tr>
                                    <x-table.table-th>Room</x-table.table-th>
                                    <x-table.table-th>Rack</x-table.table-th>
                                    <x-table.table-th>Actions</x-table.table-th>
                                </tr>
                            </x-table.table-thead>
                            <x-table.table-tbody>
                                @foreach($warehouse->locations as $location)
                                    <x-table.table-tr>
                                        <x-table.table-td>{{ $location->room }}</x-table.table-td>
                                        <x-table.table-td>{{ $location->rack }}</x-table.table-td>
                                        <x-table.table-td class="flex gap-3">
                                            <div class="flex items-center space-x-2">
                                                <x-button-edit :route="route('locations.edit', $location->id)" />

                                            </div>
                                           
                                            <form action="{{ route('locations.destroy', $location->id) }}" method="POST"
                                                style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <x-button-delete :route="route('locations.destroy', $location->id)" />
                                            </form>
                                        </x-table.table-td>
                                    </x-table.table-tr>
                                @endforeach
                            </x-table.table-tbody>
                        </x-table.table-table>
                        <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>

                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-company-layout>