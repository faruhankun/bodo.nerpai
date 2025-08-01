@php 
    $space_id = session('space_id') ?? null;

    if(is_null($space_id)){
        abort(403);
    }

@endphp

<x-crud.index-basic header="Supplies" 
                model="supply" 
                table_id="indexTable"
                :thead="[
                    'SKU', 'Space', 'Item', 'Qty', 'Cost_per_unit', 'Notes', 'Actions']"
                >
    <x-slot name="buttons">
        @include('primary.inventory.supplies.create')
    </x-slot>

    <x-slot name="filters">
        <!-- export import  -->
        <x-crud.exim-csv route_import="{{ route('supplies.import') }}" route_template="{{ route('supplies.import_template') }}">
            <h1 class="text-2xl dark:text-white font-bold">Under Construction</h1>
        </x-crud.exim-csv>
    </x-slot>

    <x-slot name="modals">
        @include('primary.inventory.supplies.edit')
    </x-slot>
</x-crud.index-basic>


<script>
    $(document).ready(function() {
        $('#create_item_id').select2({
            placeholder: 'Search & Select Item',
            minimumInputLength: 2,
            width: '100%',
            padding: '0px 12px',
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

        // document.getElementById('edit_name').value = data.name;

        document.getElementById('edit_status').value = data.status === '1' || data.status === 'active' ? 'active' : 'inactive';
        // document.getElementById('edit_parent_id').value = data.parent_id;

        document.getElementById('edit_notes').value = data.notes;

        let form = document.getElementById('editDataForm');
        form.action = `/supplies/${data.id}`;

        // Dispatch event ke Alpine.js untuk membuka modal
        window.dispatchEvent(new CustomEvent('edit-modal-js'));
    }

    $('#editDataForm').on('submit', function(e) {
        e.preventDefault();

        let form = $(this);
        let actionUrl = form.attr('action');
        let formData = form.serialize();

        $.ajax({
            url: actionUrl,
            type: 'POST', // pakai POST kalau pakai method spoofing Laravel (`@method('PUT')`)
            data: formData,
            success: function(response) {
                // Reload data di table DataTable (tanpa reload full halaman)
                $('#indexTable').DataTable().ajax.reload(null, false);

                // Tutup modal (kalau pakai Alpine.js, sesuaikan)
                window.dispatchEvent(new CustomEvent('close-edit-modal-js'));

                // Optional: tampilkan notifikasi
                Swal.fire({
                    title: "Success",
                    text: response.message,
                    icon: "success",
                    timer: 3000,
                    customClass: {
                        popup: 'bg-white p-6 rounded-lg shadow-xl dark:bg-gray-900 dark:border dark:border-sky-500',   // Customize the popup box
                        title: 'text-xl font-semibold text-green-600',
                        header: 'text-sm text-gray-700 dark:text-white',
                        content: 'text-sm text-gray-700 dark:text-white',
                        confirmButton: 'bg-emerald-900 text-white font-bold py-2 px-4 rounded-md hover:bg-emerald-700 focus:ring-2 focus:ring-emerald-300' // Customize the button
                    }
                });

                console.log(response);
            },
            error: function(xhr) {
                Swal.fire({
                    title: "Error",
                    text: xhr.responseJSON.message,
                    icon: "error",
                    timer: 3000,
                    customClass: {
                        popup: 'bg-white p-6 rounded-lg shadow-xl dark:bg-gray-900 dark:border dark:border-sky-500 dark:text-white',   // Customize the popup box
                        title: 'text-xl font-semibold text-green-600',
                        header: 'text-sm text-gray-700 dark:text-white',
                        content: 'text-sm text-gray-700 dark:text-white',
                        confirmButton: 'bg-red-900 text-white font-bold py-2 px-4 rounded-md hover:bg-red-700 focus:ring-2 focus:ring-red-300' // Customize the button
                    }
                });

                console.log(xhr);
            }
        });
    });
</script>

<script>
    $(document).ready(function() {
        let indexTable = $('#indexTable').DataTable({
            scrollX: true,
            processing: true,
            serverSide: true,
            ajax: "{{ route('supplies.data') }}",
            pageLength: 25,
            columns: [
                // { data: 'code' },
                { data: 'sku' },
                { data: 'space_display' },
                { data: 'item_display' },
                { data: 'balance', render: function(data, type, row, meta) {
                    return new Intl.NumberFormat('id-ID', {
                        maximumFractionDigits: 2
                    }).format(data);
                }},
                { data: 'cost_per_unit', render: function(data, type, row, meta) {
                    return new Intl.NumberFormat('id-ID', {
                        maximumFractionDigits: 2
                    }).format(data);
                }},
                { data: 'notes' },
                { data: 'actions', orderable: false, searchable: false }
            ]
        });
        
        // Export Import
        $('#exportVisibleBtn').on('click', function(e) {
            e.preventDefault();

            let params = indexTable.ajax.params();
            
            let exportUrl = '{{ route("supplies.export") }}' + '?params=' + encodeURIComponent(JSON.stringify(params));

            window.location.href = exportUrl;
        });
    });
</script>
