@php
    use App\Models\Company\Finance\Account;

    // Get accounts for reference
    $accounts = Account::all();
@endphp

<x-company-layout>
    <div class="py-12">
        <div class=" sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-white">
                    <h1 class="text-2xl font-bold mb-6">Journal Entry: {{ $journal_entry->number }}</h1>
                    <div class="mb-3 mt-1 flex-grow border-t border-gray-300 dark:border-gray-700"></div>

                    <!-- General Information Section -->
                    <h3 class="text-lg font-bold my-3">General Information</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-6">
                        <x-div-box-show title="Entry Number">{{ $journal_entry->number }}</x-div-box-show>
                        <x-div-box-show title="Date">{{ $journal_entry->date ?? 'N/A' }}</x-div-box-show>
                        <x-div-box-show title="Type">{{ $journal_entry->type ?? 'N/A' }}</x-div-box-show>
                        <x-div-box-show
                            title="Total Amount">Rp{{ number_format($journal_entry->total, 2) }}</x-div-box-show>
                        <x-div-box-show title="Source">
                            {{ $journal_entry->source_type ? class_basename($journal_entry->source_type) : 'N/A' }} :
                            {{ $journal_entry->source?->name ?? 'N/A' }}
                        </x-div-box-show>
                        <x-div-box-show
                            title="Created By">{{ $journal_entry->created_by->name ?? 'N/A' }}</x-div-box-show>
                        <x-div-box-show title="Description">{{ $journal_entry->description ?? 'N/A' }}</x-div-box-show>
                    </div>
                    <div class="mb-3 mt-1 flex-grow border-t border-gray-300 dark:border-gray-700"></div>

                    <!-- Journal Entry Details Section -->
                    <h3 class="text-lg font-bold my-3">Journal Entry Details</h3>
                    <div class="overflow-x-auto">
                        <x-table.table-table id="journal-entry-details">
                            <x-table.table-thead>
                                <tr>
                                    <x-table.table-th>Account</x-table.table-th>
                                    <x-table.table-th>Debit</x-table.table-th>
                                    <x-table.table-th>Credit</x-table.table-th>
                                    <x-table.table-th>Notes</x-table.table-th>
                                </tr>
                            </x-table.table-thead>
                            <x-table.table-tbody>
                                @foreach ($journal_entry->journal_entry_details as $detail)
                                    <x-table.table-tr>
                                        <x-table.table-td>
                                            {{ $accounts->find($detail->account_id)?->name ?? 'N/A' }}
                                        </x-table.table-td>
                                        <x-table.table-td
                                            class="py-4">Rp{{ number_format($detail->debit, 2) }}</x-table.table-td>
                                        <x-table.table-td>Rp{{ number_format($detail->credit, 2) }}</x-table.table-td>
                                        <x-table.table-td>{{ $detail->notes ?? 'N/A' }}</x-table.table-td>
                                    </x-table.table-tr>
                                @endforeach
                            </x-table.table-tbody>
                        </x-table.table-table>
                    </div>
                    <div class="my-6 flex-grow border-t border-gray-500 dark:border-gray-700"></div>

                    <!-- Action Section -->
                    <div class="flex justify-end space-x-4">
                        <x-secondary-button>
                            <a href="{{ route('journal_entries.index') }}">Back to List</a>
                        </x-secondary-button>
                        <x-button href="{{ route('journal_entries.edit', $journal_entry->id) }}"
                            text="Edit Journal Entry"
                            class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 dark:bg-green-700 dark:hover:bg-green-800">
                            Edit Entry
                        </x-button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-company-layout>
