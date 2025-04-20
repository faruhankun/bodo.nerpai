<x-crud.modal-edit-js title="Edit Group">
    <form method="POST" class="mt-4">
        <input type="hidden" name="id" id="edit_id">

        @include('primary.groups.partials.dataform', ['form' => ['id' => 'Edit Group', 'mode' => 'edit']])
    </form>
</x-crud.modal-edit-js>
