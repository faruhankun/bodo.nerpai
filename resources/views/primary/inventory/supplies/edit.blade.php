<x-crud.modal-edit-js title="Edit Supplies">
    <form method="POST" class="mt-4">
        <input type="hidden" name="id" id="edit_id">

        @include('primary.inventory.supplies.partials.dataform', ['form' => ['id' => 'Edit Account', 'mode' => 'edit']])
    </form>
</x-crud.modal-edit-js>
