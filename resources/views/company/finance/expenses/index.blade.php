@php
    $layout = session('layout');
@endphp
<x-dynamic-component :component="'layouts.' . $layout">
    <div class="py-12">
        <div class="max-w-7xl my-10 mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-white">
                    <h3 class="text-lg font-bold dark:text-white">Manage Expenses</h3>
                    <p class="text-sm dark:text-gray-200 mb-6">Create, edit, and manage your Expenses listings.</p>
                    <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>
                     
                    <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 mb-4">             
                        <div class="flex flex-col md:flex-row items-center space-x-3">
                            <a href="{{ route('expenses.create') }}">
                                <x-button-add :route="route('expenses.create')" text="Request Expense" />
                            </a>    
                        </div>
                    </div>
                    <x-table-table id="search-table">
                        <x-table-thead >
                            <tr>
                                <x-table-th>ID</x-table-th>
                                <x-table-th>Number</x-table-th>
                                <x-table-th>Type</x-table-th>
                                <x-table-th>Date</x-table-th>
                                <x-table-th>Consignee</x-table-th>
                                <x-table-th>Amount</x-table-th>
                                <x-table-th>Status</x-table-th>
                                <x-table-th>Notes</x-table-th>
                                <x-table-th>Actions</x-table-th>
                            </tr>
                        </x-table-thead>
                        <x-table-tbody>
                            @foreach ($expenses as $expense)
                                <tr>
                                    <x-table-td>{{ $expense->id }}</x-table-td>
                                    <x-table-td>{{ $expense?->number }}</x-table-td>
                                    <x-table-td>{{ $expense->type }}</x-table-td>
                                    <x-table-td>{{ $expense->date?->format('Y-m-d') }}</x-table-td>
                                    <x-table-td>{{ $expense->consignee_type }} : {{ $expense->consignee?->name }}</x-table-td>
                                    <x-table-td>Rp{{ number_format($expense->amount, 2) }}</x-table-td>
                                    <x-table-td>{{ $expense->status }}</x-table-td>
                                    <x-table-td>{{ $expense->notes }}</x-table-td>
                                    <x-table-td class="flex justify-center items-center gap-2">
                                        <div class="flex items-center space-x-2">
                                            <x-button-show :route="route('expenses.show', $expense->id)" />
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
