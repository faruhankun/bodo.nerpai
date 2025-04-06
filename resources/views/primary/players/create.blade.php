@php
    $layout = session('layout');
@endphp
<x-dynamic-component :component="'layouts.' . $layout">
    <div class="py-12">
        <div class="max-w-7xl my-10 mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                        Tambah Player
                    </h1>
                    <form action="{{ route('players.store') }}" method="POST">
                        @csrf
                        <!-- Relation -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-6">
                            <div class="form-group">
                                <x-input-label for="size_type">Entity Type</x-input-label>
                                <x-input-select id="size_type" name="size_type" class="w-full">
                                    <option value="PERS">Person</option>
                                    <option value="COMP">Company</option>
                                </x-input-select>
                            </div>
                            <div class="form-group">
                                <x-input-label for="size_id">Entity ID</x-input-label>
                                <x-input-select id="size_id" name="size_id" class="w-full">
                                    <option value="">-- Select Entity --</option>
                                </x-input-select>
                            </div>
                        </div>
                        
                        <div class="mt-6">
                            <x-primary-button>Buat Player</x-primary-button>
                            <a href="{{ route('players.index') }}">
                                <x-secondary-button type="button">
                                    Cancel
                                </x-button>
                            </a>
                        </div>    
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>
</x-dynamic-component>

<script>
    let persons = @json($persons);
    let companies = @json($companies);

    function updateEntityOptions(selectedEntityId = null) {
        let entityTypeSelect = document.getElementById('entity_type').value;
        let entitySelect = document.getElementById('entity_id');
        entitySelect.innerHTML = '';

        let data = entityTypeSelect == 'COMP' ? companies : persons;
        data.forEach(option => {
            let optionElement = document.createElement('option');
            optionElement.value = option.id;
            optionElement.textContent = option.name;

            if(selectedEntityId && selectedEntityId == option.id) {
                optionElement.selected = true;
            }

            entitySelect.appendChild(optionElement);
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        let existingEntityId = "{{ $supplier->entity_id }}";
        updateEntityOptions(existingEntityId);
    });

    document.getElementById('entity_type').addEventListener('change', function() {
        updateEntityOptions();
    });
</script>