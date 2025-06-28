<x-company-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Roles') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class=" sm:px-6 lg:px-8">
            <div
                class="bg-white bg-white dark:bg-gray-800 dark:border-gray-700 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg dark:text-white font-bold">Company Role Access</h3>
                    <p class="text-sm dark:text-gray-200 mb-4">Atur role dan permission setiap fitur</p>

                    <div class="mx-auto max-w-screen-xl">
                        <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden">
                            <div
                                class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4">
                                <div class="w-full md:w-1/2">
                                    <form class="flex items-center">
                                        <label for="simple-search" class="sr-only">Search</label>
                                        <div class="relative w-full">
                                            <div
                                                class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                                <svg aria-hidden="true" class="w-5 h-5 text-gray-500 dark:text-gray-400"
                                                    fill="currentColor" viewbox="0 0 20 20"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd"
                                                        d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                            <input type="text" id="search-role-name"
                                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                                placeholder="Search" required="">
                                        </div>
                                    </form>
                                </div>
                                <div
                                    class="w-full md:w-auto flex flex-col md:flex-row space-y-2 md:space-y-0 items-stretch md:items-center justify-end md:space-x-3 flex-shrink-0">
                                    <x-button-add :route="route('company_roles.create')" text="Add Role" />
                                </div>
                            </div>
                            <x-table.table-table id="roles-table">
                                <x-table.table-thead>
                                    <tr>
                                        <x-table.table-th>#</x-table.table-th>
                                        <x-table.table-th>Role Name</x-table.table-th>
                                        <x-table.table-th>Permissions</x-table.table-th>
                                        <x-table.table-th>Action</x-table.table-th>
                                    </tr>
                                </x-table.table-thead>
                                <tbody>
                                    @foreach ($company_roles as $role)
                                        <x-table.table-tr>
                                            <x-table.table-td>{{ $role->id }}</x-table.table-td>
                                            <x-table.table-th class="dark:text-white">{{ $role->name }}</x-table.table-th>
                                            <x-table.table-td>
                                                @foreach ($role->permissions as $permission)
                                                    <span
                                                        class="inline-block px-2 py-1 bg-blue-100 text-blue-600 text-xs font-medium rounded-lg mr-1 mb-1">
                                                        {{ $permission->name }}
                                                    </span>
                                                @endforeach
                                            </x-table.table-td>
                                            <x-table.table-td>
                                                <div class="flex inline">
                                                    <x-button-edit :route="route('company_roles.edit', $role->id)" />
                                                    <x-button-delete :route="route('company_roles.destroy', $role->id)" />
                                                </div>
                                            </x-table.table-td>
                                        </x-table.table-tr>
                                    @endforeach
                        </tbody>
                        </x-table.table-table>


                    </div>
                </div>
            </div>
        </div>
    </div>


</x-company-layout>