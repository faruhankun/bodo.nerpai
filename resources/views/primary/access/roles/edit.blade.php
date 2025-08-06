<x-crud.modal-edit-js title="Edit Role">
    <form method="POST" class="mt-4">
        <input type="hidden" name="id" id="edit_id">

        @include('primary.access.roles.partials.dataform', ['form' => ['id' => 'Edit Role', 'mode' => 'edit']])
    </form>
</x-crud.modal-edit-js>
