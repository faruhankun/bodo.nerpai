<x-crud.modal-create title="Connect Player" trigger="Connect Player">
    <form action="{{ route('players.related.store') }}" method="POST" class="mt-4">
        @csrf
        @include('primary.players.partials.dataform', ['form' => ['id' => 'Connect Player', 'mode' => 'create']])
    </form>
</x-crud.modal-create>