<x-crud.modal-edit-js title="Show Details">
    <form method="POST" class="mt-4">
        <input type="hidden" name="id" id="edit_id">

        <!-- <div class="flex justify-end items-center m-4 border-solid border-2 dark:border-gray-700">
            <x-div.box-input label="Export" class="m-4">
                <x-secondary-button class="ml-4" id="exportVisibleBtn">Export</x-secondary-button>
            </x-div.box-input>
        </div> -->

        <div id="show_data" class="overflow-y-auto">
            <x-table.table-table id="search-table">
                <x-table.table-thead>
                    <tr>
                        <x-table.table-th>Tanggal</x-table.table-th>
                        <x-table.table-th>Number</x-table.table-th>
                        <x-table.table-th>Deskripsi</x-table.table-th>
                        <x-table.table-th>Notes</x-table.table-th>
                        <x-table.table-th>Debit</x-table.table-th>
                        <x-table.table-th>Kredit</x-table.table-th>
                        <x-table.table-th>Saldo</x-table.table-th>
                    </tr>
                </x-table.table-thead>
                <x-table.table-tbody>
                </x-table.table-tbody>
            </x-table.table-table>
        </div>
    </form>
</x-crud.modal-edit-js>
