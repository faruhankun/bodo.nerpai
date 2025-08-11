@php
    $router = 'contacts';

    $trigger = $trigger ?? 'show_modal_js';

    $space_role = session('space_role') ?? null;
@endphp


<x-crud.modal.modal-js title="Details" trigger="{{ $trigger }}">
    <div class="grid grid-cols-3 gap-6">
        <x-div-box-show title="Number" id="modal_number"></x-div-box-show>
        <x-div-box-show title="Date" id="modal_date"></x-div-box-show>
        <x-div-box-show title="Contributor" id="modal_contributor"></x-div-box-show>
        <x-div-box-show title="Notes" id="modal_notes"></x-div-box-show>
        <x-div-box-show title="Total Amount" id="modal_total"></x-div-box-show>
        <x-div-box-show title="TX Asal" id="modal_tx_asal"></x-div-box-show>
    </div>

    <div class="my-4 border-t border-gray-300"></div>


    <h3 class="text-lg font-bold my-3">TX Details</h3>
    <div class="overflow-x-auto">
        <x-table.table-table id="journal-entry-details">
            <x-table.table-thead>
                <tr>
                    <x-table.table-th>Inventory</x-table.table-th>
                    <x-table.table-th>Quantity</x-table.table-th>
                    <x-table.table-th>Type</x-table.table-th>
                    <x-table.table-th>Cost/Unit</x-table.table-th>
                    <x-table.table-th>Subtotal</x-table.table-th>
                    <x-table.table-th>Notes</x-table.table-th>
                </tr>
            </x-table.table-thead>
            <x-table.table-tbody id="modal_tx_details_body">
                {{-- Diisi JS --}}
            </x-table.table-tbody>
        </x-table.table-table>
    </div>

    <div class="my-4 border-t border-gray-300"></div>


    <h3 class="text-lg font-bold my-3">TX Related</h3>
    <div class="overflow-x-auto">
        <x-table.table-table id="journal-outputs">
            <x-table.table-thead>
                <tr>
                    <x-table.table-th>Number</x-table.table-th>
                    <x-table.table-th>Space</x-table.table-th>
                    <x-table.table-th>Date</x-table.table-th>
                    <x-table.table-th>Contributor</x-table.table-th>
                    <x-table.table-th>Total</x-table.table-th>
                    <x-table.table-th>Notes</x-table.table-th>
                    <x-table.table-th>Actions</x-table.table-th>
                </tr>
            </x-table.table-thead>
            <x-table.table-tbody id="modal_tx_related_body">
                {{-- Diisi JS --}}
            </x-table.table-tbody>
        </x-table.table-table>
    </div>


    <div class="flex gap-3 justify-end mt-8">
        <x-secondary-button type="button" @click="isOpen_{{ $trigger }} = false">Cancel</x-secondary-button>

        @if($space_role == 'admin' || $space_role == 'owner')
            <a id="modal_edit_link" target="_blank">
                <x-primary-button type="button">Edit Journal</x-primary-button>
            </a>
        @endif
    </div>
</x-crud.modal.modal-js>

