<x-crud.modal-create title="Create Kontak" trigger="Create Kontak">
    <form action="{{ route('players.store') }}" method="POST" class="mt-4" id="createDataForm">
        @csrf

        @include('primary.player.players.partials.dataform', ['form' => ['id' => 'Create Kontak', 'mode' => 'create']])
    </form>
</x-crud.modal-create>