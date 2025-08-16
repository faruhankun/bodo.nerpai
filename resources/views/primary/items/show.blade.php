@php
    $space_id = get_space_id(request());
    $space = \App\Models\Primary\Space::findOrFail($space_id);
    $spaces = $space->spaceAndChildren();

    $supplies = $data->inventories
                    ->whereIn('space_id', $spaces->pluck('id')->toArray());

    $user = auth()->user();
    $space_role = session('space_role') ?? null;

    $allow_cost = $user->can('space.supplies.cost', 'web') || $space_role == 'owner';
@endphp

<x-crud.modal-show title="Item Details" trigger="View">
    <div class="grid grid-cols-3 sm:grid-cols-3 gap-6">
        <x-div-box-show title="Code">{{ $data->code ?? 'N/A' }}</x-div-box-show>
        <x-div-box-show title="SKU">{{ $data->sku ?? 'N/A' }}</x-div-box-show>
        <x-div-box-show title="Name">{{ $data->name ?? 'N/A' }}</x-div-box-show>
        <x-div-box-show title="Price">{{ $data->price ?? 'N/A' }}</x-div-box-show>

        @if($allow_cost)
            <x-div-box-show title="Cost">{{ $data->cost ?? 'N/A' }}</x-div-box-show>
        @endif

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
                    <x-table.table-th>SKU</x-table.table-th>
                    <x-table.table-th>Space</x-table.table-th>
                    <x-table.table-th>Qty</x-table.table-th>
                    <x-table.table-th>Cost/Unit</x-table.table-th>
                    <x-table.table-th>Notes</x-table.table-th>
                </tr>
            </x-table.table-thead>
            <x-table.table-tbody>
                @php 
                    $cost_total = 0;
                    $balance_total = 0;
                @endphp

                @foreach ($supplies as $supply)
                    @if($supply->balance == 0) @continue @endif
                    <x-table.table-tr>
                        <x-table.table-td>{{ $supply->sku ?? 'N/A' }}</x-table.table-td>
                        <x-table.table-td>{{ $supply->space_type ?? 'N/A' }} : {{ $supply->space?->name ?? 'N/A' }}</x-table.table-td>
                        <x-table.table-td>{{ intval($supply->balance) ?? 'N/A' }}</x-table.table-td>
                        <x-table.table-td class="py-4">Rp{{ $allow_cost ? number_format($supply->cost_per_unit, 2) : 'null' }}</x-table.table-td>
                        <x-table.table-td>{{ $detail->notes ?? 'N/A' }}</x-table.table-td>
                    </x-table.table-tr>

                    @php 
                        $balance_total += $supply->balance;
                        $cost_total += $supply->cost_per_unit * $supply->balance;
                    @endphp
                @endforeach


                <x-table.table-tr>
                    <x-table.table-th></x-table.table-th>
                    <x-table.table-th class="text-lg font-bold">Total</x-table.table-th>
                    <x-table.table-th class="text-lg">{{ $balance_total }}</x-table.table-th>
                    <x-table.table-th class="text-lg">Rp{{ $allow_cost ? number_format($cost_total) : 'null' }}</x-table.table-th>
                    <x-table.table-th></x-table.table-th>
                </x-table.table-tr>
            </x-table.table-tbody>
        </x-table.table-table>
    </div>
    <div class="my-6 flex-grow border-t border-gray-500 dark:border-gray-700"></div>


    <!-- Action Section -->
    <h3 class="text-lg font-bold my-3">Actions</h3>
    <div class="flex gap-3 mt-8">
        <x-secondary-button type="button" onclick="updateInventoryToChildren({{ $data->id }})">Update Inventory to Children</x-secondary-button>
    </div>


    <div class="flex gap-3 justify-end mt-8">
        <x-secondary-button type="button" @click="isOpen = false">Cancel</x-secondary-button>
        <!-- <a href="{{ route('players.edit', $data->id) }}">
            <x-primary-button type="button">Edit Group</x-primary-button>
        </a> -->
    </div>
</x-crud.modal-show>

