<x-crud.index-basic header="Players" 
                model="player" 
                table_id="indexTable"
                :thead="['ID', 'Code', 'Size', 'Type', 'Name', 'Status', 'Notes', 'Actions']"
                >
    <x-slot name="buttons">

    </x-slot>

    <x-slot name="modals">
        @include('primary.players.edit', ['edit' => ['data' => 'Edit Account']])
    </x-slot>
</x-crud.index-basic>



<script>
    function edit(data) {
        document.getElementById('edit_id').value = data.id;

        document.getElementById('edit_name').value = data.name;

        document.getElementById('edit_code').value = data.code;

        document.getElementById('edit_status').value = data.status === '1' || data.status === 'active' ? 'active' : 'inactive';

        document.getElementById('edit_notes').value = data.notes;

        let form = document.getElementById('editDataForm');
        form.action = `/players/${data.id}`;

        // Dispatch event ke Alpine.js untuk membuka modal
        window.dispatchEvent(new CustomEvent('edit-modal-js'));
    }
</script>

<script>
$(document).ready(function() {
    $('#indexTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('players.data') }}",
        columns: [
            { data: 'id' },
            { data: 'code' },
            { data: 'size_display' },
            { data: 'type_type' },
            { data: 'size.name' },
            { data: 'status' },
            { data: 'notes' },
            { data: 'actions', orderable: false, searchable: false }
        ]
    });
});
</script>