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
                    data: 'number'
                },
                {
                    data: 'handler_notes'
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
</script>
