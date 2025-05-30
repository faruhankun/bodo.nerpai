<x-store-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Store Employees') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl my-10 mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                <h3 class="text-lg font-bold dark:text-white">Manage Store Employees</h3>
				<p class="text-sm dark:text-gray-200 mb-6">Create, edit, and manage your store employees listings.</p>
				<div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>
                    
                    <!-- Search and Add New Store Employees -->
                    <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 mb-4">             
                        <div class="flex flex-col md:flex-row items-center space-x-3">
                                @include('store.store_employees.create')
                         </div>
                    </div>
                    <x-table.table-table id="search-table"> 
                        <x-table.table-thead>
                            <tr>
                                <x-table.table-th>#</x-table.table-th>
                                <x-table.table-th>Employee Name</x-table.table-th>
                                <x-table.table-th>Status</x-table.table-th>
                                <x-table.table-th>Role</x-table.table-th>
                                <x-table.table-th>Store Role</x-table.table-th>
                                <x-table.table-th>Actions</x-table.table-th>
                            </tr>
                        </x-table.table-thead>
                        <tbody>
                            @foreach ($store_employees as $employee)
                                <x-table.table-tr>
                                    <x-table.table-td>{{ $employee->id }}</x-table.table-td>
                                    <x-table.table-td>{{ $employee->employee->companyuser->user->name }}</x-table.table-td>
                                    <x-table.table-td>{{ $employee->status }}</x-table.table-td>
                                    <x-table.table-td>{{ $employee->employee->role->name }}</x-table.table-td>
                                    <x-table.table-td>{{ $employee?->store_role?->name }}</x-table.table-td>
                                    <x-table.table-td>
                                    <div class="flex items-center space-x-2">
                                            
                                            @include('store.store_employees.edit', ['employee' => $employee, 'store_roles' => $store_roles])
                                            <x-button-delete :route="route('store_employees.destroy', $employee->id)" />
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
</x-store-layout>