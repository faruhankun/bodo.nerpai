<x-crud.modal-show title="Transaction Details" trigger="View">
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
        <x-div-box-show title="Sender Notes">{{ $data->sender_notes ?? 'N/A' }}</x-div-box-show>
        <x-div-box-show title="Handler Notes">{{ $data->handler_notes ?? 'N/A' }}</x-div-box-show>
    </div>
    <br>
    <div class="mb-3 mt-1 flex-grow border-t border-gray-300 dark:border-gray-700"></div>



    <!-- Journal Entry Details Section -->
    <h3 class="text-lg font-bold my-3">TX Details</h3>
    <div class="overflow-x-auto">
        <x-table.table-table id="journal-entry-details">
            <x-table.table-thead>
                <tr>
                    <x-table.table-th>Inventory</x-table.table-th>
                    <x-table.table-th>Quantity</x-table.table-th>
                    <x-table.table-th>Type</x-table.table-th>
                    <x-table.table-th>Cost/Unit</x-table.table-th>
                    <x-table.table-th>Notes</x-table.table-th>
                </tr>
            </x-table.table-thead>
            <x-table.table-tbody>
                @foreach ($data->details as $detail)
                    <x-table.table-tr>
                        <x-table.table-td>{{ $detail->detail?->sku ?? '?' }} : {{ $detail->detail?->name ?? 'N/A' }}</x-table.table-td>
                        <x-table.table-td>{{ number_format($detail->quantity, 0) }}</x-table.table-td>
                        <x-table.table-td>{{ $detail->model_type ?? 'N/A' }}</x-table.table-td>
                        <x-table.table-td class="py-4">Rp{{ number_format($detail->cost_per_unit, 2) }}</x-table.table-td>
                        <x-table.table-td>{{ $detail->notes ?? 'N/A' }}</x-table.table-td>
                    </x-table.table-tr>
                @endforeach
            </x-table.table-tbody>
        </x-table.table-table>
    </div>
    <div class="my-6 flex-grow border-t border-gray-500 dark:border-gray-700"></div>


    <div class="flex gap-3 justify-end mt-8">
        <x-secondary-button type="button" @click="isOpen = false">Cancel</x-secondary-button>
        <a target="_blank" href="{{ route('journal_supplies.edit', $data->id) }}">
            <x-primary-button type="button">Edit Journal</x-primary-button>
        </a>
    </div>
</x-crud.modal-show>

