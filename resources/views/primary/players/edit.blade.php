<x-crud.modal-edit-js title="Edit Player">
    <input type="hidden" name="id" id="edit_id">

    @include('primary.players.partials.dataform', ['form' => ['id' => 'Edit Player', 'mode' => 'edit']])
</x-crud.modal-edit-js>