<x-crud.modal-edit-js title="Edit Item">
    <form method="POST" class="mt-4">
        <input type="hidden" name="id" id="edit_id">

        @include('primary.items.partials.dataform', ['form' => ['id' => 'Edit Item', 'mode' => 'edit']])
    </form>
</x-crud.modal-edit-js>
