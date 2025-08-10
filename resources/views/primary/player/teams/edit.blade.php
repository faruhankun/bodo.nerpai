<x-crud.modal-edit-js title="Edit Team">
    <form method="POST" class="mt-4">
        <input type="hidden" name="id" id="edit_id">

        @include('primary.player.teams.partials.dataform', ['form' => ['id' => 'Edit Team', 'mode' => 'edit']])
    </form>
</x-crud.modal-edit-js>
