<x-crud.index-basic header="Groups" 
                model="group" 
                table_id="indexTable"
                :thead="['ID', 'Code', 'Name', 'Address', 'Status', 'Notes', 'Actions']"
                >
    <x-slot name="buttons">
        @include('primary.groups.create')
    </x-slot>

    <x-slot name="modals">
        @include('primary.groups.edit')
    </x-slot>
</x-crud.index-basic>

<script>
    function edit(data) {
        document.getElementById('edit_id').value = data.id;

        document.getElementById('edit_name').value = data.name;
        document.getElementById('edit_code').value = data.code;
        document.getElementById('edit_address').value = data.address;

        document.getElementById('edit_status').value = data.status === '1' || data.status === 'active' ? 'active' : 'inactive';

        document.getElementById('edit_notes').value = data.notes;

        let form = document.getElementById('editDataForm');
        form.action = `/groups/${data.id}`;

        // Dispatch event ke Alpine.js untuk membuka modal
        window.dispatchEvent(new CustomEvent('edit-modal-js'));
    }
</script>

<script>
$(document).ready(function() {
    $('#indexTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('groups.data') }}",
        columns: [
            { data: 'id' },
            { data: 'code' },
            { data: 'name' },
            { data: 'address' },
            { data: 'status' },
            { data: 'notes' },
            { data: 'actions', orderable: false, searchable: false }
        ]
    });
});
</script>