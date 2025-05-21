@php

    $space_id = session('space_id') ?? null;
    if(is_null($space_id)){
        abort(403);
    }
    $space = \App\Models\Primary\Space::findOrFail($space_id);
    $spaces = $space->spaceAndChildren();

    $supplies = $data->inventories
                    ->whereIn('space_id', $spaces->pluck('id')->toArray());
@endphp

<x-crud.modal-show title="Item Details" trigger="View">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <x-div-box-show title="Code">{{ $data->code ?? 'N/A' }}</x-div-box-show>
        <x-div-box-show title="SKU">{{ $data->sku ?? 'N/A' }}</x-div-box-show>
        <x-div-box-show title="Name">{{ $data->name ?? 'N/A' }}</x-div-box-show>
        <x-div-box-show title="Price">{{ $data->price ?? 'N/A' }}</x-div-box-show>
        <x-div-box-show title="Cost">{{ $data->cost ?? 'N/A' }}</x-div-box-show>
        <x-div-box-show title="Status">{{ $data->status }}</x-div-box-show>
        <x-div-box-show title="Notes">{{ $data->notes ?? 'N/A' }}</x-div-box-show>
    </div>
    <br>
    <div class="mb-3 mt-1 flex-grow border-t border-gray-300 dark:border-gray-700"></div>



    <!-- Supplies -->
    <h3 class="text-lg font-bold my-3">Supplies Details</h3>
    <div class="overflow-x-auto">
        <x-table.table-table id="inventories-details">
            <x-table.table-thead>
                <tr>
                    <x-table.table-th>Code</x-table.table-th>
                    <x-table.table-th>Space</x-table.table-th>
                    <x-table.table-th>Qty</x-table.table-th>
                    <x-table.table-th>Cost/Unit</x-table.table-th>
                    <x-table.table-th>Notes</x-table.table-th>
                </tr>
            </x-table.table-thead>
            <x-table.table-tbody>
                @foreach ($supplies as $supply)
                    <x-table.table-tr>
                        <x-table.table-td>{{ $supply->code ?? 'N/A' }}</x-table.table-td>
                        <x-table.table-td>{{ $supply->space_type ?? 'N/A' }} : {{ $supply->space?->name ?? 'N/A' }}</x-table.table-td>
                        <x-table.table-td>{{ intval($supply->balance) ?? 'N/A' }}</x-table.table-td>
                        <x-table.table-td class="py-4">Rp{{ number_format($supply->cost_per_unit, 2) }}</x-table.table-td>
                        <x-table.table-td>{{ $detail->notes ?? 'N/A' }}</x-table.table-td>
                    </x-table.table-tr>
                @endforeach
            </x-table.table-tbody>
        </x-table.table-table>
    </div>
    <div class="my-6 flex-grow border-t border-gray-500 dark:border-gray-700"></div>



    <div class="flex gap-3 justify-end mt-8">
        <x-secondary-button type="button" @click="isOpen = false">Cancel</x-secondary-button>
        <!-- <a href="{{ route('players.edit', $data->id) }}">
            <x-primary-button type="button">Edit Group</x-primary-button>
        </a> -->
    </div>
</x-crud.modal-show>

