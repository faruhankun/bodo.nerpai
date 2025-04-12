<x-crud.index-basic header="Persons" 
                model="person" 
                table_id="indexTable"
                :thead="['ID', 'Number', 'User:Username', 'Name', 'Email', 'Status', 'Notes', 'Actions']"
                >
    <x-slot name="buttons">
        @include('primary.persons.create', ['create' => ['data' => 'Create Person']])
    </x-slot>

    <x-slot name="modals">
        @include('primary.persons.edit')
    </x-slot>
</x-crud.index-basic>

<script>
    function edit(data) {
        document.getElementById('edit_id').value = data.id;

        document.getElementById('edit_name').value = data.name;
        document.getElementById('edit_full_name').value = data.full_name;

        document.getElementById('edit_birth_date').valueAsDate = new Date(data.birth_date);

        document.getElementById('edit_email').value = data.email;
        document.getElementById('edit_phone_number').value = data.phone_number;

        document.getElementById('edit_status').value = data.status === '1' || data.status === 'active' ? 'active' : 'inactive';

        document.getElementById('edit_notes').value = data.notes;

        let form = document.getElementById('editDataForm');
        form.action = `/persons/${data.id}`;

        // Dispatch event ke Alpine.js untuk membuka modal
        window.dispatchEvent(new CustomEvent('edit-modal-js'));
    }
</script>

<script>
$(document).ready(function() {
    $('#indexTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('persons.data') }}",
        columns: [
            { data: 'id' },
            { data: 'number', render: function(data, type, row) {
                return data ? data : 'N/A';
            }},
            { data: 'user_username' },
            { data: 'name' },
            { data: 'email' },
            { data: 'status' },
            { data: 'notes' },
            { data: 'actions', orderable: false, searchable: false }
        ]
    });
});
</script>