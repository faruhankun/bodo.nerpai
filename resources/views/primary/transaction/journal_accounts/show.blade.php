@php
    $layout = session('layout') ?? 'lobby';
@endphp

<x-dynamic-component :component="'layouts.' . $layout">
    <div class="py-12">
        <div class=" sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-white">
                    <h1 class="text-2xl font-bold mb-6">Journal Entry: {{ $data->number }}</h1>
                    <div class="mb-3 mt-1 flex-grow border-t border-gray-300 dark:border-gray-700"></div>


                    
                    <!-- General Information Section -->
                    <h3 class="text-lg font-bold my-3">General Information</h3>
                    <div class="grid grid-cols-3 sm:grid-cols-3 gap-6">
                        <x-div-box-show title="Number">{{ $data->number }}</x-div-box-show>
                        <x-div-box-show title="Date">{{ optional($data->sent_time)?->format('Y-m-d') ?? '??' }}</x-div-box-show>
                        <x-div-box-show title="Source">
                            {{ $data->input_type ?? 'N/A' }} : {{ $data->input?->number ?? 'N/A' }}
                        </x-div-box-show>

                        <x-div-box-show title="Created By">{{ $data->sender?->name ?? 'N/A' }}</x-div-box-show>
                        <x-div-box-show title="Updated By">{{ $data?->handler?->name ?? 'N/A' }}</x-div-box-show>
                        <x-div-box-show title="Total Amount">Rp{{ number_format($data->total, 2) }}</x-div-box-show>
                        
                        <x-div-box-show title="Status">{{ $data->status }}</x-div-box-show>
                        <x-div-box-show title="Description">{{ $data->sender_notes ?? 'N/A' }}</x-div-box-show>
                        <x-div-box-show title="Notes">
                            {{ $data->notes ?? 'N/A' }}
                        </x-div-box-show>
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
                                @foreach ($data->details as $detail)
                                    <x-table.table-tr>
                                        <x-table.table-td>
                                            {{ $detail->detail?->code ?? '?' }} : {{ $detail->detail?->name ?? 'N/A' }}
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
                            <a href="{{ route('journal_accounts.index') }}">Back to List</a>
                        </x-secondary-button>
                        <x-button href="{{ route('journal_accounts.edit', $data->id) }}"
                            text="Edit Journal Entry"
                            class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 dark:bg-green-700 dark:hover:bg-green-800">
                            Edit Entry
                        </x-button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-dynamic-component>
