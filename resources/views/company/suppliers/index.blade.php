<x-index-basic header="Suppliers" 
                model="supplier" 
                table_id="indexTable"
                :thead="['ID', 'Name', 'Address', 'Email', 'Phone Number', 'Status', 'Note', 'Actions']"
                >
    <x-slot name="buttons">
        <x-button-add :route="route('suppliers.create')" text="Tambah Supplier" />
    </x-slot>
</x-index-basic>

<script>
$(document).ready(function() {
    $('#indexTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('suppliers.data') }}",
        columns: [
            { data: 'id' },
            { data: 'name' },
            { data: 'address' },
            { data: 'email' },
            { data: 'phone_number' },
            { data: 'status' },
            { data: 'notes' },
            { data: 'actions', orderable: false, searchable: false }
        ]
    });
});
</script>

