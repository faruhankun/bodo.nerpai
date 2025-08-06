<x-crud.modal-create title="Create Role" trigger="Create Role">
    <form action="{{ route('roles.store') }}" method="POST" class="mt-4" id="createDataForm">
        @csrf

        @include('primary.access.roles.partials.dataform', ['form' => ['id' => 'Create Role', 'mode' => 'create']])
    </form>
</x-crud.modal-create>