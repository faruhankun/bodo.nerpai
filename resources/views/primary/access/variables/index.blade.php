<x-crud.index-basic header="Variables" 
                model="variable" 
                table_id="indexTable"
                :thead="['ID', 'Key', 'Name', 'Value', 'Notes', 'Actions']"
                >
    <x-slot name="buttons">
        @include('primary.access.variables.create')
    </x-slot>

    <x-slot name="modals">
        @include('primary.access.variables.edit')
    </x-slot>
</x-crud.index-basic>

<script>
    function create(){
        // auto set basecode
        document.getElementById('_basecode').value = document.getElementById('_type_id').selectedOptions[0].dataset.basecode;

        let form = document.getElementById('createDataForm');
        form.action = '/variables';

        // Dispatch event ke Alpine.js untuk membuka modal
        window.dispatchEvent(new CustomEvent('create-modal'));
    }

    function edit(data) {
        document.getElementById('edit_id').value = data.id;

        document.getElementById('edit_name').value = data.name;

        document.getElementById('edit_value').value = data.value;

        document.getElementById('edit_key').value = data.key;

        document.getElementById('edit_status').value = data.status === '1' || data.status === 'active' ? 'active' : 'inactive';

        document.getElementById('edit_notes').value = data.notes;

        document.getElementById('edit_deletable').value = data.deletable;

        let form = document.getElementById('editDataForm');
        form.action = `/variables/${data.id}`;

        // Dispatch event ke Alpine.js untuk membuka modal
        window.dispatchEvent(new CustomEvent('edit-modal-js'));
    }
</script>

<script>
$(document).ready(function() {
    $('#indexTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('variables.data') }}",
        pageLength: 25,
        columns: [
            { data: 'id' },
            { data: 'key' },
            { data: 'name' },
            { data: 'value' },
            { data: 'notes' },
            { data: 'actions', orderable: false, searchable: false }
        ]
    });
});
</script>