<x-crud.modal-edit-js title="Edit Space Player">
    <form method="POST" class="mt-4">
        <input type="hidden" name="id" id="edit_id">

        @include('space.space_players.partials.dataform', ['form' => ['id' => 'Edit Space Player', 'mode' => 'edit']])
    </form>
</x-crud.modal-edit-js>
