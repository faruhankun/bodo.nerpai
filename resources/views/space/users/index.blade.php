<x-lobby-layout>
    <div class="py-12">
        <div class="max-w-7xl my-10 mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-white">
                    <h3 class="text-lg font-bold dark:text-white">Manage users</h3>
                    <p class="text-sm dark:text-gray-200 mb-6">Create, edit, and manage your users listings.</p>
                    <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>

                    <!-- Search and Add New User -->
                    <!-- <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 mb-4">
                       
                        <div class="w-full md:w-auto flex justify-end">
                            <x-button-add :route="route('users.create')" text="Add user" />
                        </div>
                    </div> -->

                    <x-table-table id="search-table">
                        <x-table-thead >
                            <tr>
                                <x-table-th>ID</x-table-th>
                                {{-- <x-table-th>username</x-table-th> --}}
                                <x-table-th>Player Code</x-table-th>
                                <x-table-th>Nama</x-table-th>
                                <x-table-th>Role</x-table-th>
                                <x-table-th>Companies</x-table-th>
                                <x-table-th>Actions</x-table-th>
                            </tr>
                        </x-table-thead>
                        <x-table-tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <x-table-td>{{ $user->id }}</x-table-td>
                                    {{-- <x-table-td>{{ $user->username }}</x-table-td> --}}
                                    <x-table-td>{{ $user->player?->code ?? 'N/A'; }}</x-table-td>
                                    <x-table-td>{{ $user->name }}</x-table-td>
                                    <x-table-td>{{ $user->role->name ?? 'N/A'; }}</x-table-td>
                                    <x-table-td>
                                        @if(count($user->companies) > 0)
                                            @foreach ($user->companies as $company)
                                                {{ $company->name }} : {{ $company->pivot->status }}<br>
                                            @endforeach
                                        @endif
                                    </x-table-td>
                                    <x-table-td class="flex justify-center items-center gap-2">
                                    <div class="flex items-center space-x-2">
                                            <x-button-edit :route="route('users.edit', $user->id)" />
                                            <x-button-delete :route="route('users.destroy', $user->id)" />
                                        </div>
                                    </x-table-td>
                                </tr>
                            @endforeach
                        </x-table-tbody>
                    </x-table-table>
                </div>
            </div>
        </div>
    </div>
    
</x-lobby-layout>
