<x-crud.modal-create title="Create Skill" trigger="Create Skill">
    <form action="{{ route('skills.store') }}" method="POST" class="mt-4" id="createDataForm">
        @csrf

        @include('primary.access.skills.partials.dataform', ['form' => ['id' => 'Create Skill', 'mode' => 'create']])
    </form>
</x-crud.modal-create>