@php
    $request = request();


    $space_id = session('space_id') ?? null;
    if(is_null($space_id)){
        abort(403);
    }
    $space_children = $space->children;
    $player = session('player_id') ? \App\Models\Primary\Player::findOrFail(session('player_id')) : Auth::user()->player;


    // pilih space yang tidak punya variable space.settings.supplies yang bernilai 0
    $space_with_supplies = $space_children->filter(function ($space) {
        $var = $space->variables->where('key', 'space.setting.supplies')->first();
        if($var){
            if($var->value == 0){
                return false;
            }
        }

        return true;
    });
    $space_select = $request->get('space_select') ?? null;
    $space_select_options = [$space_id => $space->name];    // this space
    foreach($space_with_supplies as $space){
        $space_select_options[$space->id] = $space->name;
    }
    $space_select_options['all'] = 'SEMUA SPACE';
    if($space_select == null)
        $space_select = $space_id;




    $status_select = $request->get('status_select') ?? null;
    $status_select_options = $status_select_options ?? [];
    $status_select_options['all'] = 'Semua Status';
    $status_select_options['exc'] = 'Status Tidak Diketahui';
    if($status_select == null)
        $status_select = 'TX_READY';
@endphp

<x-crud.index-basic header="Journal Supplies" model="journal supplies" table_id="indexTable" 
                    :thead="['Date', 'Space', 'Number', 'Description', 'SKU', 'Status', 'Actions']">
    <x-slot name="buttons">
        @include('primary.transaction.journal_supplies.create')


        <x-input-select name="space_select" id="space-select" class="filter-select">
            <option value="">-- Filter Status --</option>
            @foreach ($space_select_options as $key => $value)
                <option value="{{ $key }}" {{ $space_select == $key ? 'selected' : '' }}>{{ $value }}</option>
            @endforeach
        </x-input-select>


        <x-input-select name="status_select" id="status-select" class="filter-select">
            <option value="">-- Filter Status --</option>
            @foreach ($status_select_options as $key => $value)
                <option value="{{ $key }}" {{ $status_select == $key ? 'selected' : '' }}>{{ $value }}</option>
            @endforeach
        </x-input-select>
    </x-slot>

    <x-slot name="filters">
        <!-- export import  -->
        <x-crud.exim-csv route_import="{{ route('journal_supplies.import') }}" route_template="{{ route('journal_supplies.import_template') }}">
            <h1 class="text-2xl dark:text-white font-bold">Under Construction</h1>
        </x-crud.exim-csv>
    </x-slot>


    <x-slot name="modals">
        @include('primary.transaction.journal_supplies.showjs')
    </x-slot>
</x-crud.index-basic>

<script>
    function create() {
        // auto set basecode
        document.getElementById('_basecode').value = document.getElementById('_type_id').selectedOptions[0].dataset
            .basecode;

        let form = document.getElementById('createDataForm');
        form.action = '/journal_supplies';

        // Dispatch event ke Alpine.js untuk membuka modal
        window.dispatchEvent(new CustomEvent('create-modal'));
    }
</script>


<!-- Tabel & EXIM  -->
<script>
    $(document).ready(function() {
        let indexTable = $('#indexTable').DataTable({
            processing: true,
            serverSide: true,  
            ajax: {
                url: "{{ route('journal_supplies.data') }}",
                data: function (d) {
                    d.return_type = 'DT';
                    d.space_id = {{ $space_id }};
                    d.model_type_select = $('#model-type-select').val() || '';
                    d.status_select = $('#status-select').val() || '';
                    d.space_select = $('#space-select').val() || '';
                    d.limit = 'all';
                }
            },
            pageLength: 10,
            columns: [
                {
                    data: 'sent_time',
                    render: function(data) {
                        let date = new Date(data);
                        let year = date.getFullYear();
                        let month = String(date.getMonth() + 1).padStart(2, '0');
                        let day = String(date.getDate()).padStart(2, '0');
                        return `${year}-${month}-${day}`;
                    }
                },

                {
                    data: 'space.name',
                    render: function(data, type, row, meta) {
                        if (row.space) {
                            return row.space.name;
                        } else {
                            return '??';
                        }
                    }
                },
                
                {
                    data: 'number',
                    className: 'text-blue-600',
                    render: function (data, type, row, meta) {
                        return `<a href="journal_supplies/${row.id}" target="_blank">${
                                    data
                                }</a>`;
                    }
                },

                {
                    data: 'all_notes',
                    render: function(data) {
                        return data || '-';
                    }
                },
                {
                    data: 'sku',
                    name: 'sku', // penting biar bisa search & sort
                    render: function(data) {
                        return data || '-';
                    }
                },

                // {
                //     data: 'total',
                //     className: 'text-right',
                //     render: function(data, type, row, meta) {
                //         return new Intl.NumberFormat('id-ID', {
                //             maximumFractionDigits: 2
                //         }).format(data);
                //     }
                // },
                {
                    data: 'status',
                    render: function(data) {
                        return data || 'unknown';
                    }
                },

                {
                    data: 'actions',
                    orderable: false,
                    searchable: false
                }
            ]
        });



        // filter
        $('.filter-select').on('change', function(e) {
            indexTable.ajax.reload();
        });



        // setup create
        $('#create_sender_id').val('{{ $player->id }}');


        // Export Import
        $('#exportVisibleBtn').on('click', function(e) {
            e.preventDefault();

            let params = indexTable.ajax.params();
            
            let exportUrl = '{{ route("journal_supplies.export") }}' + '?params=' + encodeURIComponent(JSON.stringify(params));

            window.location.href = exportUrl;
        });

    });



    function showjs(data) {
        const trigger = 'show_modal_js';
        const parsed = typeof data === 'string' ? JSON.parse(data) : data;

        // Inject data ke elemen-elemen tertentu di dalam modal
        $('#modal_number').text(parsed.number ?? '-');
        $('#modal_date').text(parsed.sent_time.split('T')[0] ?? '-');
        $('#modal_contributor').html(`Created By: ${parsed.sender?.name ?? 'N/A'}<br>Updated By: ${parsed.handler?.name ?? 'N/A'}`);
        $('#modal_notes').html(`Sender: ${parsed.sender_notes ?? '-'}<br>Handler: ${parsed.handler_notes ?? '-'}`);
        $('#modal_total').text(`Rp${parseFloat(parsed.total ?? 0).toLocaleString('id-ID', {minimumFractionDigits: 2})}`);
        $('#modal_tx_asal').html(`TX: ${parsed.input?.number ?? '-'} <br>Space: ${parsed.input?.space?.name ?? '-'}`);

        document.getElementById('modal_edit_link').href = `/journal_supplies/${parsed.id}/edit`;

        // Inject detail TX
        let html_detail = '';
        for (const item of parsed.details ?? []) {
            html_detail += `
                <tr style="border-bottom: 1px solid #ccc;">
                    <td class="pl-4">${item.detail?.sku ?? '?'} : ${item.detail?.name ?? 'N/A'}</td>
                    <td class="pl-4">${item.quantity ?? 0}</td>
                    <td class="pl-4">${item.model_type ?? '-'}</td>
                    <td class="pl-4">${parseFloat(item.cost_per_unit ?? 0).toLocaleString()}</td>
                    <td class="pl-4">${(item.quantity * item.cost_per_unit).toLocaleString()}</td>
                    <td class="pl-4">${item.notes ?? '-'}</td>
                </tr>
            `;
        }
        $('#modal_tx_details_body').html(html_detail);


        // Inject TX Related
        let html_related = '';
        for (const tx of (parsed.outputs ?? [])) {
            html_related += `
                <tr>
                    <td class="pl-4">${tx.number}</td>
                    <td class="pl-4">${tx.space?.name ?? '-'}</td>
                    <td class="pl-4">${tx.sent_time.split('T')[0] ?? '-'}</td>
                    <td class="pl-4">${tx.sender?.name ?? '-'} <br> ${tx.handler?.name ?? '-'}</td>
                    <td class="pl-4">${parseFloat(tx.total ?? 0).toLocaleString('id-ID', {minimumFractionDigits: 2})}</td>
                    <td class="pl-4">${tx.notes ?? '-'}</td>
                    <td class="pl-4"></td>
                </tr>
            `;
        }
        $('#modal_tx_related_body').html(html_related);


        // Tampilkan modal
        window.dispatchEvent(new CustomEvent('open-' + trigger));
    }

</script>
