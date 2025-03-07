@php
    $layout = session('layout');
@endphp
<x-dynamic-component :component="'layouts.' . $layout">
    <div class="py-12">
        <div class="max-w-7xl my-10 mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-white">
                    <h3 class="text-lg font-bold dark:text-white">Manage Store POS</h3>
                    <p class="text-sm dark:text-gray-200 mb-6">Create, edit, and manage your Store POS Sales</p>
                    <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>
                     
                    <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 mb-4">             
                        <div class="flex flex-col md:flex-row items-center space-x-3">
                            <a href="{{ route('store_pos.create') }}">
                                <x-button-add :route="route('store_pos.create')" text="Tambah Store POS" />
                            </a>
                        </div>
                    </div>
                    <x-table-table id="search-table">
                        <x-table-thead >
                            <tr>
                                <x-table-th>ID</x-table-th>
                                <x-table-th>Number</x-table-th>
                                <x-table-th>Tanggal</x-table-th>
                                <x-table-th>Customer</x-table-th>
                                <x-table-th>Cashier</x-table-th>
                                <x-table-th>Status</x-table-th>
                                <x-table-th>Notes</x-table-th>
                                <x-table-th>Actions</x-table-th>
                            </tr>
                        </x-table-thead>
                        <x-table-tbody>
                            @foreach ($store_pos as $pos)
                                <tr>
                                    <x-table-td>{{ $pos->id }}</x-table-td>
                                    <x-table-td>{{ $pos->number }}</x-table-td>
                                    <x-table-td>{{ $pos->date?->format('Y-m-d') }}</x-table-td>
                                    <x-table-td>{{ $pos->store_customer?->customer->name ?? 'N/A' }}</x-table-td>
                                    <x-table-td>{{ $pos->store_employee?->employee?->companyuser->user->name ?? 'N/A' }}</x-table-td>
                                    <x-table-td>{{ $pos->status }}</x-table-td>
                                    <x-table-td>{{ $pos->notes }}</x-table-td>
                                    <x-table-td class="flex justify-center items-center gap-2">
                                        <div class="flex items-center space-x-2">
                                            <x-button-show :route="route('store_pos.show', $pos->id)" />
                                            @if($pos->status == 'POS_PENDING')
                                                <x-button-delete :route="route('store_pos.destroy', $pos->id)" />
                                            @endif
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
</x-dynamic-component>
