<x-crud.modal-create title="Create Supply" trigger="Create Supply">
    <form action="{{ route('supplies.store') }}" method="POST" class="mt-4">
        @csrf

        @include('primary.inventory.supplies.partials.dataform', ['form' => ['id' => 'Create Supply', 'mode' => 'create']])
    </form>
</x-crud.modal-create>