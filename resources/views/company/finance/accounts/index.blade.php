@php
    $layout = session('layout');
@endphp
<x-dynamic-component :component="'layouts.' . $layout">
    <div class="py-12">
        <div class="max-w-7xl my-10 mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-white">
                    <h3 class="text-lg font-bold dark:text-white">Manage Accounts</h3>
                    <p class="text-sm dark:text-gray-200 mb-6">Create, edit, and manage your Accounts</p>
                    <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>

                    <div
                        class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 mb-4">
                        <div class="flex flex-col md:flex-row items-center space-x-3">
                            @include('company.finance.accounts.create')
                        </div>
                    </div>

                    <!-- Supplier Table -->
                    <x-table-table id="search-table">
                        <x-table-thead>
                            <tr>
                                <x-table-th>Code</x-table-th>
                                <x-table-th>Name</x-table-th>
                                <x-table-th>Type</x-table-th>
                                <x-table-th>Balance</x-table-th>
                                <x-table-th>Note</x-table-th>
                                <x-table-th>Actions</x-table-th>
                            </tr>
                        </x-table-thead>
                        <x-table-tbody>
                            @foreach ($accounts as $account)
                                <x-table-tr>
                                    <x-table-td>{{ $account->code }}</x-table-td>
                                    <x-table-td>{{ $account->name }}</x-table-td>
                                    <x-table-td>{{ $account->account_type?->name }}</x-table-td>
                                    <x-table-td
                                        class="text-right">{{ number_format($account->balance, 2) }}</x-table-td>
                                    <x-table-td>{{ $account->note }}</x-table-td>
                                    <x-table-td>
                                        <div class="flex space-x-2">
                                            @include('company.finance.accounts.edit', [
                                                'edit_account' => $account,
                                            ])
                                            <x-button-delete :route="route('accounts.destroy', $account->id)" />
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

</x-dynamic-component>
