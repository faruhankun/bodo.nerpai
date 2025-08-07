<x-crud.index-basic header="Items" 
                model="item" 
                table_id="indexTable"
                :thead="['SKU', 'Name', 'Price', 'Stok', 'Status', 'Notes', 'Actions']"
                >
    <x-slot name="buttons">
        @include('primary.items.create')
    </x-slot>

    <x-slot name="filters">
        @include('primary.items.exim')
    </x-slot>

    <x-slot name="modals">
        @include('primary.items.edit')

        @include('primary.items.partials.edit-supply')

        <div id="react-supplies-modal" data-id="" data-start_date="" data-end_date="" data-account_data></div>
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




    
    function edit_supply(id, status, notes) {
        document.getElementById('edit_supply_id').value = id;

        document.getElementById('edit_supply_status').value = status === '1' || status === 'active' ? 'active' : 'inactive';

        document.getElementById('edit_supply_notes').value = notes;

        let form = document.getElementById('editDataForm2');
        form.action = `/supplies/${id}`;

        // Dispatch event ke Alpine.js untuk membuka modal
        window.dispatchEvent(new CustomEvent('edit-modal-js-2'));
    }


    $('#editDataForm2').on('submit', function(e) {
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
                window.dispatchEvent(new CustomEvent('close-edit-modal-js-2'));

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
    let table = $('#indexTable').DataTable({
        scrollX: true,
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

            { data: 'supplies', 
                style: 'width: 400px',
                render: function(data, type, row, meta) {
                    return data ?? '';
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

<script>
    function show_tx_modal(id, sku, name, acc = {}){
        acc = {
            id: id,
            sku: sku,
            name: name,
            ...acc
        };

        console.log(acc);

        const container = document.getElementById('react-supplies-modal');
        
        const today = new Date();
        const year = today.getFullYear();
        const startDate = `${year}-01-01`;
        const endDate = today.toISOString().split('T')[0]; // format: YYYY-MM-DD

        container.setAttribute('data-id', acc.id);
        container.setAttribute('data-start_date', startDate);
        container.setAttribute('data-end_date', endDate);
        container.setAttribute('data-account_data', JSON.stringify(acc));

        console.log(acc);

        window.dispatchEvent(new CustomEvent('showSuppliesModal'));
    }
</script>

@vite('resources/js/app.jsx')