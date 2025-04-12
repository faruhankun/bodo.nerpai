<x-crud.modal-create title="Create Person" trigger="Create Person">
    <form action="{{ route('persons.store') }}" method="POST" class="mt-4">
        @csrf
        @include('primary.persons.partials.dataform', ['form' => ['id' => 'Create Person', 'mode' => 'create']])
    </form>
</x-crud.modal-create>