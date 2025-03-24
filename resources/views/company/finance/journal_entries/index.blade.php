@php
    $layout = session('layout');
@endphp
<x-dynamic-component :component="'layouts.' . $layout">
    <div class="py-12">
        <div class="max-w-7xl my-10 mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-white" x-data="{ isOpen: false, item: {} }">
                    <h3 class="text-lg font-bold dark:text-white">Manage Journal Entries</h3>
                    <p class="text-sm dark:text-gray-200 mb-6">Create, edit, and manage your Journal Entries</p>
                    <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>

                    <div
                        class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 mb-4">
                        <div class="flex flex-col md:flex-row items-center space-x-3">
                            <a href="{{ route('journal_entries.create') }}">
                                <x-button-add :route="route('journal_entries.create')" text="Add Journal Entry" />
                            </a>
                        </div>
                    </div>

                    <!-- Accounts Table -->
                    <x-table-table id="search-table">
                        <x-table-thead>
                            <tr>
                                <x-table-th>ID</x-table-th>
                                <x-table-th>Date</x-table-th>
                                <x-table-th>Number</x-table-th>
                                <x-table-th>Description</x-table-th>
                                <x-table-th>Total</x-table-th>
                                <x-table-th>Actions</x-table-th>
                            </tr>
                        </x-table-thead>
                        <x-table-tbody>
                            @foreach ($journal_entries as $journal_entry)
                                <x-table-tr>
                                    <x-table-td>{{ $journal_entry->id }}</x-table-td>
                                    <x-table-td>{{ $journal_entry->date }}</x-table-td>
                                    <x-table-td>{{ $journal_entry->number }}</x-table-td>
                                    <x-table-td>{{ $journal_entry->description }}</x-table-td>
                                    <x-table-td
                                        class="text-right">{{ number_format($journal_entry->total, 2) }}</x-table-td>
                                    <x-table-td>
                                        <div class="flex space-x-2">
                                            <x-button-show :route="route('journal_entries.show', $journal_entry->id)" />
                                            <x-button-delete :route="route('journal_entries.destroy', $journal_entry->id)" />
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
