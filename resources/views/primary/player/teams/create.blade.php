<x-crud.modal-create title="Add Team" trigger="Add Team">
    <form action="{{ route('teams.store') }}" method="POST" class="mt-4" id="createDataForm">
        @csrf

        @include('primary.player.teams.partials.dataform', ['form' => ['id' => 'Add Team', 'mode' => 'create']])
    </form>
</x-crud.modal-create>