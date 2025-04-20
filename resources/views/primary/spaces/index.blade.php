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
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": '{!! route('spaces.data') !!}',
            "data": function(d) {
                d.space_id = @json(session('space_id'))
            }
        },
        "columns": [
            {data: 'code', name: 'code'},
            {data: 'parent_display', name: 'parent_display'},
            {data: 'type_display', name: 'type_display'},
            {data: 'name', name: 'name'},
            {data: 'address', name: 'address'},
            {data: 'status', name: 'status'},
            {data: 'notes', name: 'notes'},
            {data: 'actions', name: 'actions', orderable: false, searchable: false}
        ]
    });
});
</script>