<x-crud.modal-create title="Add Player" trigger="Add Player">
    <form action="{{ route('space_players.store') }}" method="POST" class="mt-4">
        @csrf
        @include('space.space_players.partials.dataform', ['form' => ['id' => 'Add Player', 'mode' => 'create']])
    </form>
</x-crud.modal-create>