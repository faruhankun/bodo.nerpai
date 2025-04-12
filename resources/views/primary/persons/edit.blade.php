<x-crud.modal-edit-js title="Edit Person">
    <input type="hidden" name="id" id="edit_id">

    @include('primary.persons.partials.dataform', ['form' => ['id' => 'Edit Person', 'mode' => 'edit']])
</x-crud.modal-edit-js>
