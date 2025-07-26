<x-crud.index-basic header="Accounts" 
                model="account" 
                table_id="indexTable"
                :thead="['Code', 'Name', 'Type', 'Balance', 'Notes', 'Actions']"
                >
    <x-slot name="buttons">
        @include('primary.inventory.accountsp.create')
    </x-slot>

    <x-slot name="filters">
        <!-- export import  -->
        <x-crud.exim-csv route_import="{{ route('accountsp.import') }}" route_template="{{ route('accountsp.import_template') }}">
        </x-crud.exim-csv>
    </x-slot>

    <x-slot name="modals">
        @include('primary.inventory.accountsp.edit')

        <div id="react-account-modal" data-id="" data-start_date="" data-end_date="" data-account_data></div>
    </x-slot>
</x-crud.index-basic>

<script>
    function create(){
        // auto set basecode
        document.getElementById('_basecode').value = document.getElementById('_type_id').selectedOptions[0].dataset.basecode;

        let form = document.getElementById('createDataForm');
        form.action = '/accountsp';

        // Dispatch event ke Alpine.js untuk membuka modal
        window.dispatchEvent(new CustomEvent('create-modal'));
    }

    function edit(data) {
        document.getElementById('edit_id').value = data.id;

        document.getElementById('edit_name').value = data.name;

        document.getElementById('edit_type_id').value = data.type_id;
        // document.getElementById('edit_basecode').value = document.getElementById('edit_type_id').selectedOptions[0].dataset.basecode;
        // document.getElementById('edit_code').value = data.code.substring($('#edit_basecode').val().length);
        $('#edit_code').val(data.code);

        document.getElementById('edit_status').value = data.status === '1' || data.status === 'active' ? 'active' : 'inactive';
        document.getElementById('edit_parent_id').value = data.parent_id;

        document.getElementById('edit_notes').value = data.notes;

        let form = document.getElementById('editDataForm');
        form.action = `/accountsp/${data.id}`;

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
            processing: true,
            serverSide: true,
            ajax: "{{ route('accountsp.data') }}",
            pageLength: 100,
            columns: [
                { data: 'code' },
                { data: 'name' },
                { data: 'type.name' },
                { data: 'getAccountBalance', className: 'text-right font-bold text-md text-blue-600',
                    render: function (data, type, row, meta) {
                        return `<a href="javascript:void(0)" onclick='show_account_modal(${JSON.stringify(row.data)})'>${new Intl.NumberFormat('id-ID', { 
                            maximumFractionDigits: 2
                        }).format(data)}</a>`;
                    }
                },
                { data: 'notes' },
                { data: 'actions', orderable: false, searchable: false }
            ]
        });


        // Export Import
        $('#exportVisibleBtn').on('click', function(e) {
            e.preventDefault();

            let params = indexTable.ajax.params();
            
            let exportUrl = '{{ route("accountsp.export") }}' + '?params=' + encodeURIComponent(JSON.stringify(params));

            window.location.href = exportUrl;
        });
    });


    function show_account_modal(acc){
        acc = JSON.parse(acc);

        if(acc.id == null){
            return;
        }

        const container = document.getElementById('react-account-modal');
        
        const today = new Date();
        const year = today.getFullYear();
        const startDate = `${year}-01-01`;
        const endDate = today.toISOString().split('T')[0]; // format: YYYY-MM-DD

        container.setAttribute('data-id', acc.id);
        container.setAttribute('data-start_date', startDate);
        container.setAttribute('data-end_date', endDate);
        container.setAttribute('data-account_data', JSON.stringify(acc));

        console.log(acc);

        let summary_type = $('#summary_type').val();
        if(summary_type == 'balance_sheet'){
            container.setAttribute('data-start_date', '');
        }

        window.dispatchEvent(new CustomEvent('showAccountModal'));
    }
</script>

@vite('resources/js/app.jsx')