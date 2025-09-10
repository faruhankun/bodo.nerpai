@php
    $player_id = session('player_id') ?? auth()->user()->player->id;
    $request = request();
    $user = auth()->user();
    $space_role = session('space_role') ?? null;
    $space_id = session('space_id') ?? null;



    $model_type_select = $request->get('model_type_select') ?? null;
    $model_type_option = [];

    if($user->can('space.trades.po') OR $user->can('space.trades.so') || $space_role == 'owner'){
        $model_type_option['all'] = 'Semua Kontak';

        if($model_type_select == null)
            $model_type_select = 'all';
    }

    if($user->can('space.trades.po') || $space_role == 'owner'){
        $model_type_option['PO'] = 'Pembelian';
    }

    if($user->can('space.trades.so') || $space_role == 'owner'){
        $model_type_option['SO'] = 'Penjualan';

        if($model_type_select == null)
            $model_type_select = 'SO';
    }


    $model_type_option['ITR'] = 'Interaksi';

    if($model_type_select == null)
        $model_type_select = 'ITR';


@endphp

<x-crud.index-basic header="Kontak" 
                model="player" 
                table_id="indexTable"
                :thead="['Id', 'Code', 'Name', 'Trade terakhir', 'Status', 'Notes', 'Actions']"
                >
    <x-slot name="buttons">
        @include('primary.player.players.create')

        <x-input-select name="model_type_select" id="model-type-select">
            <option value="">-- Filter --</option>
            @foreach ($model_type_option as $key => $value)
                <option value="{{ $key }}" {{ $model_type_select == $key ? 'selected' : '' }}>{{ $value }}</option>
            @endforeach
        </x-input-select>
    </x-slot>



    <x-slot name="filters">
        <!-- export import  -->
        @if($user->can('space.players.exim') || $space_role == 'owner')
        <x-crud.exim-csv route_import="{{ route('players.exim') . '?query=import' }}" route_template="{{ route('players.exim') . '?query=importTemplate' }}">
        </x-crud.exim-csv>
        @endif
    </x-slot>



    <x-slot name="modals">
        @include('primary.player.players.edit')

        @include('primary.player.players.showjs')
    </x-slot>
</x-crud.index-basic>



<script>
    function showjs(data){
        let trigger = 'show_modal_js';

        let html = '<pre>' + JSON.stringify(data, null, 2) + '<br><br>';
        // html += '<pre>' + JSON.stringify(data.model2, null, 2) + '<br><br>'
        // html += '<pre>' + JSON.stringify(data.model2.size, null, 2) + '<br><br>';

        $('#dataform_' + trigger).html(html);

        window.dispatchEvent(new CustomEvent('open-' + trigger));
    }



    function create(){
        let form = document.getElementById('createDataForm');
        form.action = '/players';

        // Dispatch event ke Alpine.js untuk membuka modal
        window.dispatchEvent(new CustomEvent('open-modal-create'));
    }

    $('#createDataForm').on('submit', function(e) {
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
                window.dispatchEvent(new CustomEvent('close-modal-create'));

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
                    timer: 5000,
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



    function edit(data) {
        document.getElementById('edit_id').value = data.id;

        document.getElementById('edit_name').value = data.name;

        document.getElementById('edit_code').value = data.code;

        document.getElementById('edit_email').value = data.email;

        document.getElementById('edit_phone_number').value = data.phone_number;

        document.getElementById('edit_status').value = data.status === '1' || data.status === 'active' ? 'active' : 'inactive';

        document.getElementById('edit_notes').value = data.notes;

        document.getElementById('edit_tags').value = data.tags ? JSON.stringify(data.tags) : '[]';
        document.getElementById('edit_links').value = data.links ? JSON.stringify(data.links) : '[]';

        let form = document.getElementById('editDataForm');
        form.action = `/players/${data.id}`;

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
                    timer: 5000,
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
        let space_id = "{{ $space_id }}";

        let indexTable = $('#indexTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "/players/data",
                delay: 200,
                data: function(d) {
                    d.return_type = 'DT';
                    d.space = 'this';
                    d.space_id = space_id;
                    d.model_type_select = $('#model-type-select').val() || '';
                },
            },
            columns: [
                { data: 'id' },
                {
                    data: 'code',
                    className: 'text-blue-600',
                    render: function (data, type, row, meta) {
                        return `<a href="/players/${row.id}" target="_blank">${
                                    data
                                }</a>`;
                    }
                },
                { data: 'name' },
                { data: 'last_transaction',
                    render: function(data, type, row) {
                        let last_transaction = data ? JSON.parse(data) : null;
                        return last_transaction ? ((last_transaction.number ?? 'number') + ' (' + (last_transaction.status ?? 'status') + ')') : 'null';
                    }
                },
                { data: 'status' },
                { data: 'notes' },
                { data: 'actions', orderable: false, searchable: false }
            ]
        });


        $('#model-type-select').on('change', function() {
            indexTable.ajax.reload();
        });



        // export
        // Export Import
        $('#exportVisibleBtn').on('click', function(e) {
            e.preventDefault();

            let params = indexTable.ajax.params();
            
            let exportUrl = '{{ route("players.exim") }}' + '?query=export' 
                                + '&model_type_select=' + $('#model-type-select').val() 
                                + '&params=' + encodeURIComponent(JSON.stringify(params));

            window.location.href = exportUrl;
        });
    });
</script>


<!-- <script>
    function showjs(data) {
        console.log(data);
        const trigger = 'show_modal_js';
        const parsed = typeof data === 'string' ? JSON.parse(data) : data;


        // ajax get data show
        $.ajax({
            url: "/api/players/" + parsed.id,
            type: "GET",
            data: {
                'page_show': 'show'
            },
            success: function(data) {
                let page_show = data.page_show ?? 'null ??';
                $('#datashow_'+trigger).html(page_show);

                window.dispatchEvent(new CustomEvent('open-' + trigger));
            }
        });        
    }
</script> -->