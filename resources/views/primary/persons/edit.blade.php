<x-crud.modal-edit-js title="Edit Person">
    <form method="POST" class="mt-4">
        <input type="hidden" name="id" id="edit_id">

        @include('primary.persons.partials.dataform', ['form' => ['id' => 'Edit Person', 'mode' => 'edit']])
    </form>
</x-crud.modal-edit-js>
