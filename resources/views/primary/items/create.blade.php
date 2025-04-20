<x-crud.modal-create title="Create Item" trigger="Create Item">
    <form action="{{ route('items.store') }}" method="POST" class="mt-4">
        @csrf
        @include('primary.items.partials.dataform', ['form' => ['id' => 'Create Item', 'mode' => 'create']])
    </form>
</x-crud.modal-create>