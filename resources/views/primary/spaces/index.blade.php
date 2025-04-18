<x-crud.index-basic header="Spaces" 
                model="space" 
                table_id="indexTable"
                :thead="['Code', 'Parent', 'Type', 'Name', 'Address', 'Status', 'Notes', 'Actions']"
                >
    <x-slot name="buttons">
        @include('primary.spaces.create')
    </x-slot>

    <x-slot name="modals">
        @include('primary.spaces.edit')
    </x-slot>
</x-crud.index-basic>

<script>
    function edit(data) {
        document.getElementById('edit_id').value = data.id;

        document.getElementById('edit_code').value = data.code;

        document.getElementById('edit_name').value = data.name;

        document.getElementById('edit_type_type').value = data.type_type;

        document.getElementById('edit_status').value = data.status === '1' || data.status === 'active' ? 'active' : 'inactive';

        document.getElementById('edit_notes').value = data.notes;

        let form = document.getElementById('editDataForm');
        form.action = `/spaces/${data.id}`;

        // Dispatch event ke Alpine.js untuk membuka modal
        window.dispatchEvent(new CustomEvent('edit-modal-js'));
    }
</script>

<script>
$(document).ready(function() {
    $('#indexTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('spaces.data') }}",
        columns: [
            { data: 'code', render: function(data, type, row) {
                return data ? data : 'N/A';
            }},
            { data: 'parent_display' },
            { data: 'type_display' },
            { data: 'name' },
            { data: 'address' },
            { data: 'status' },
            { data: 'notes' },
            { data: 'actions', orderable: false, searchable: false }
        ]
    });
});
</script>