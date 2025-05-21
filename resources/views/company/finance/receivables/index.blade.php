@php
    $layout = session('layout');
@endphp
<x-dynamic-component :component="'layouts.' . $layout">
    <div class="py-12">
        <div class="max-w-7xl my-10 mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-white">
                    <h3 class="text-lg font-bold dark:text-white">Manage Accounts Receivables</h3>
                    <p class="text-sm dark:text-gray-200 mb-6">Create, edit, and manage your Receivables listings.</p>
                    <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>
                     
                    <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 mb-4">             
                        <div class="flex flex-col md:flex-row items-center space-x-3">

                         </div>
                    </div>

                    <!-- Customer Table -->
                    <x-table.table-table id="search-table">
                        <x-table.table-thead>
                            <tr>
                                <x-table.table-th>ID</x-table.table-th>
                                <x-table.table-th>Name</x-table.table-th>
                                <x-table.table-th>Status</x-table.table-th>
                                <x-table.table-th>Piutang</x-table.table-th>
                                <x-table.table-th>Note</x-table.table-th>
                                <x-table.table-th>Actions</x-table.table-th>
                            </tr>
                        </x-table.table-thead>
                        <x-table.table-tbody>
                            @foreach ($customers as $customer)
                                @php
                                    $unpaid_amount = $customer->receivables->sum('total_amount');
                                    $paid_amount = $customer->receivables->sum('balance');
                                @endphp

                                @if($unpaid_amount != $paid_amount) 
                                    <x-table.table-tr>
                                        <x-table.table-td>{{ $customer->id }}</x-table.table-td>
                                        <x-table.table-td>{{ $customer->name }}</x-table.table-td>
                                        <x-table.table-td>{{ $customer->status }}</x-table.table-td>
                                        <x-table.table-td>Rp.{{ (number_format($unpaid_amount - $paid_amount, 2)) }}</x-table.table-td>
                                        <x-table.table-td>{{ $customer->notes }}</x-table.table-td>
                                        <x-table.table-td>
                                            <div class="flex items-center space-x-2">
                                                <x-button-show :route="route('receivables.show', $customer->id)" />
                                            </div>
                                        </x-table.table-td>
                                    </x-table.table-tr>
                                @endif
                            @endforeach
                        </x-table.table-tbody>
                    </x-table.table-table>
                </div>
            </div>
        </div>
    </div>
    
</x-dynamic-component>
