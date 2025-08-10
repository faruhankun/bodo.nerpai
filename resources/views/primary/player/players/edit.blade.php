<x-crud.modal-edit-js title="Edit Kontak">
    <form method="POST" class="mt-4">
        <input type="hidden" name="id" id="edit_id">

        @include('primary.player.players.partials.dataform', ['form' => ['id' => 'Edit Kontak', 'mode' => 'edit']])
    </form>
</x-crud.modal-edit-js>
