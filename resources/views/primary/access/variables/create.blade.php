<x-crud.modal-create title="Create Variable" trigger="Create Variable">
    <form action="{{ route('variables.store') }}" method="POST" class="mt-4">
        @csrf
        @include('primary.access.variables.partials.dataform', ['form' => ['id' => 'Create Variable', 'mode' => 'create']])
    </form>
</x-crud.modal-create>