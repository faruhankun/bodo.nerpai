<x-crud.modal-edit-js-2 title="Edit Supplies">
    <form method="POST" class="mt-4">
        <input type="hidden" name="id" id="edit_supply_id">

        @include('primary.inventory.supplies.partials.dataform', ['form' => ['id' => 'Edit Account', 'mode' => 'edit_supply']])
    </form>
</x-crud.modal-edit-js-2>
