<x-crud.index-basic header="Items" 
                model="item" 
                table_id="indexTable"
                :thead="['SKU', 'Name', 'Price', 'Status', 'Notes', 'Actions']"
                >
    <x-slot name="buttons">
        @include('primary.items.create')
    </x-slot>

    <x-slot name="filters">
        @include('primary.items.exim')
    </x-slot>

    <x-slot name="modals">
        @include('primary.items.edit')
    </x-slot>
</x-crud.index-basic>

<script>
    function edit(data) {
        document.getElementById('edit_id').value = data.id;

        document.getElementById('edit_code').value = data.code;
        document.getElementById('edit_sku').value = data.sku;
        document.getElementById('edit_name').value = data.name;

        $('#edit_price').val(data.price);
        $('#edit_cost').val(data.cost);
        $('#edit_weight').val(data.weight);

        document.getElementById('edit_status').value = data.status === '1' || data.status === 'active' ? 'active' : 'inactive';

        document.getElementById('edit_notes').value = data.notes;

        let form = document.getElementById('editDataForm');
        form.action = `/items/${data.id}`;

        // Dispatch event ke Alpine.js untuk membuka modal
        window.dispatchEvent(new CustomEvent('edit-modal-js'));
    }
</script>

<script>
$(document).ready(function() {
    let table = $('#indexTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('items.data') }}",
        columns: [
            // { data: 'id' },
            // { data: 'code' },
            { data: 'sku' },
            { data: 'name' },
            { data: 'price', 
                className: 'text-right',
                render: function (data, type, row, meta) {
                    return new Intl.NumberFormat('id-ID', { 
                        maximumFractionDigits: 2
                    }).format(data);
                }
            },
            { data: 'status' },
            { data: 'notes' },
            { data: 'actions', orderable: false, searchable: false }
        ]
    });


    // export 
    $('#exportVisibleBtn').on('click', function(e) {
        e.preventDefault();

        let params = table.ajax.params();
        
        let exportUrl = '{{ route("items.export") }}' + '?params=' + encodeURIComponent(JSON.stringify(params));

        window.location.href = exportUrl;
    });
});
</script>