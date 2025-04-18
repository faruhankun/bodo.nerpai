<x-crud.modal-edit-js title="Edit Space">
    <form method="POST" class="mt-4">
        <input type="hidden" name="id" id="edit_id">

        @include('primary.spaces.partials.dataform', ['form' => ['id' => 'Edit Space', 'mode' => 'edit']])
    </form>
</x-crud.modal-edit-js>
