<x-crud.modal-create title="Create Space" trigger="Create Space">
    <form action="{{ route('spaces.store') }}" method="POST" class="mt-4">
        @csrf
        @include('primary.spaces.partials.dataform', ['form' => ['id' => 'Create Space', 'mode' => 'create']])
    </form>
</x-crud.modal-create>