@php
    $space_id = session('space_id') ?? null;
    if(is_null($space_id)){
        abort(403);
    }

    $player = session('player_id') ? \App\Models\Primary\Player::findOrFail(session('player_id')) : Auth::user()->player;
@endphp

<x-crud.index-basic header="Journal Supplies" model="journal supplies" table_id="indexTable" 
                    :thead="['ID', 'Date', 'Number', 'Description', 'SKU','Total', 'Actions']">
    <x-slot name="buttons">
        @include('primary.transaction.journal_supplies.create')
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
            ajax: "{{ route('journal_supplies.data') }}",   
            pageLength: 10,
            columns: [{
                    data: 'id'
                },
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
                    data: 'number',
                    className: 'text-blue-600',
                    render: function (data, type, row, meta) {
                        return `<a href="javascript:void(0)" onclick='showjs(${JSON.stringify(row.data)})'>${
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
                //     data: 'details',
                //     render: function(data) {
                //         if (!Array.isArray(data)) return '-';
                        
                //         const list_sku = data.map(d => {
                //             const item = d.detail?.item;
                //             return item ? `${item.sku} - ${item.name}` : null;
                //         }).filter(Boolean);

                //         return list_sku.join('<br>');
                //     }
                // },
                {
                    data: 'total',
                    className: 'text-right',
                    render: function(data, type, row, meta) {
                        return new Intl.NumberFormat('id-ID', {
                            maximumFractionDigits: 2
                        }).format(data);
                    }
                },
                {
                    data: 'actions',
                    orderable: false,
                    searchable: false
                }
            ]
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
