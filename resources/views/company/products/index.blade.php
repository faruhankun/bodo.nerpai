<x-crud.index-basic header="Products" 
                model="product" 
                table_id="indexTable"
                :thead="['ID', 'SKU', 'Name', 'Price', 'Status', 'Notes', 'Actions']"
                >
    <x-slot name="buttons">
        @include('company.products.create')
    </x-slot>
</x-crud.index-basic>

<script>
$(document).ready(function() {
    $('#indexTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('products.data') }}",
        columns: [
            { data: 'id' },
            { data: 'sku' },
            { data: 'name' },
            { data: 'price' },
            { data: 'status' },
            { data: 'notes' },
            { data: 'actions', orderable: false, searchable: false }
        ]
    });
});
</script>
