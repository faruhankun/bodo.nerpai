<x-crud.modal-edit-js title="Edit Skill">
    <form method="POST" class="mt-4">
        <input type="hidden" name="id" id="edit_id">

        @include('primary.access.skills.partials.dataform', ['form' => ['id' => 'Edit Skill', 'mode' => 'edit']])
    </form>
</x-crud.modal-edit-js>
