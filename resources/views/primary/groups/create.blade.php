<x-crud.modal-create title="Create Group" trigger="Create Group">
    <form action="{{ route('groups.store') }}" method="POST" class="mt-4">
        @csrf
        @include('primary.groups.partials.dataform', ['form' => ['id' => 'Create Group', 'mode' => 'create']])
    </form>
</x-crud.modal-create>