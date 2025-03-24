<x-company-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Permissions') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div
                class="bg-white bg-white dark:bg-gray-800 dark:border-gray-700 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <div class="px-6">
                        <h3 class="text-lg dark:text-white font-bold">Manage Permission</h3>
                        <p class="text-sm dark:text-gray-200 mb-4">Atur data permission setiap fitur</p>
                    </div>
                    <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>

                    <div class="mx-auto max-w-screen-xl">
                        <div
                            class="bg-white border-b border-gray-500 dark:bg-gray-800 dark:border-gray-700 overflow-hidden shadow-sm sm:rounded-lg">
                            <div
                                class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 px-6">

                                {{-- <div
                                    class="w-full md:w-auto flex flex-col md:flex-row space-y-2 md:space-y-0 items-stretch md:items-center justify-end md:space-x-3 flex-shrink-0">
                                    <x-button-add :route="route('permissions.create')" text="Add Permission}" />
                                </div> --}}
                            </div>
                            <div class="p-6 text-gray-900 dark:text-white">


                                <x-table-table id="search-table">
                                    <x-table-thead class="mt-3">
                                        <tr>
                                            <x-table-th>Id</x-table-th>
                                            <x-table-th>Nama Permission</x-table-th>
                                            <x-table-th>Aksi</x-table-th>
                                        </tr>
                                    </x-table-thead>
                                    <tbody>
                                        @foreach($company_permissions as $permission)
                                            <x-table-tr>
                                                <x-table-td>{{ $permission->id }}</x-table-td>
                                                <x-table-td>{{ $permission->name }}</x-table-td>
                                                <x-table-td>
                                                    <div class="flex inline">
                                                        <x-button-edit :route="route('company_permissions.edit', $permission->id)" />
                                                        {{-- <x-button-delete :route="route('company_permissions.destroy', $permission->id)" /> --}}
                                                    </div>
                                                </x-table-td>
                                            </x-table-tr>
                                        @endforeach
                                    </tbody>
                                </x-table-table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    




</x-company-layout>