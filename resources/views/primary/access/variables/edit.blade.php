<x-crud.modal-edit-js title="Edit Variable">
    <form method="POST" class="mt-4">
        <input type="hidden" name="id" id="edit_id">

        @include('primary.access.variables.partials.dataform', ['form' => ['id' => 'Edit Variable', 'mode' => 'edit']])
    </form>
</x-crud.modal-edit-js>
