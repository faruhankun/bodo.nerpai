<x-crud.modal-create title="Create Account" trigger="Create Account">
    <form action="{{ route('accountsp.store') }}" method="POST" class="mt-4">
        @csrf
        @include('primary.inventory.accountsp.partials.dataform', ['form' => ['id' => 'Create Account', 'mode' => 'create']])
    </form>
</x-crud.modal-create>