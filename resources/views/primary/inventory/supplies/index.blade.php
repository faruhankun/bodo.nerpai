@php 
    $space_id = session('space_id') ?? null;

    if(is_null($space_id)){
        abort(403);
    }

@endphp

<x-crud.index-basic header="Supplies" 
                model="supply" 
                table_id="indexTable"
                :thead="['Code', 'Item', 'Qty', 'Cost_per_unit', 'Notes', 'Actions']"
                >
    <x-slot name="buttons">
        @include('primary.inventory.supplies.create')
    </x-slot>

    <x-slot name="modals">

    </x-slot>
</x-crud.index-basic>

<script>
    $(document).ready(function() {
        $('#create_item_id').select2({
            placeholder: 'Search & Select Item',
            minimumInputLength: 2,
            ajax: {
                url: '/items/search',
                dataType: 'json',
                paginate: true,
                data: function(params) {
                    return {
                        q: params.term,
                        space: '{{ $space_id }}',
                        page: params.page || 1
                    };
                },
                processResults: function(data) {
                    return {
                        results: data
                    };
                },
                cache: true
            }
        });
    });
</script>


<script>
    function create(){
        // auto set basecode
        document.getElementById('_basecode').value = document.getElementById('_type_id').selectedOptions[0].dataset.basecode;

        let form = document.getElementById('createDataForm');
        form.action = '/supplies';

        // Dispatch event ke Alpine.js untuk membuka modal
        window.dispatchEvent(new CustomEvent('create-modal'));
    }

    function edit(data) {
        document.getElementById('edit_id').value = data.id;

        document.getElementById('edit_name').value = data.name;

        document.getElementById('edit_type_id').value = data.type_id;
        document.getElementById('edit_basecode').value = document.getElementById('edit_type_id').selectedOptions[0].dataset.basecode;
        document.getElementById('edit_code').value = data.code.substring($('#edit_basecode').val().length);

        document.getElementById('edit_status').value = data.status === '1' || data.status === 'active' ? 'active' : 'inactive';
        document.getElementById('edit_parent_id').value = data.parent_id;

        document.getElementById('edit_notes').value = data.notes;

        let form = document.getElementById('editDataForm');
        form.action = `/supplies/${data.id}`;

        // Dispatch event ke Alpine.js untuk membuka modal
        window.dispatchEvent(new CustomEvent('edit-modal-js'));
    }
</script>

<script>
$(document).ready(function() {
    $('#indexTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('supplies.data') }}",
        pageLength: 25,
        columns: [
            { data: 'code' },
            { data: 'item_display' },
            { data: 'balance' },
            // { data: 'getSupplyBalance', className: 'text-right',
            //     render: function (data, type, row, meta) {
            //         return new Intl.NumberFormat('id-ID', { 
            //             maximumFractionDigits: 2
            //         }).format(data);
            //     }
            //  },
            { data: 'cost_per_unit' },
            { data: 'notes' },
            { data: 'actions', orderable: false, searchable: false }
        ]
    });
});
</script>