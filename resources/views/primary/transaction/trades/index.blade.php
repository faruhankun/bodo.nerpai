@php
    $player_id = session('player_id') ?? auth()->user()->player->id;
@endphp

<x-crud.index-basic header="Trades" 
                model="Trade" 
                table_id="indexTable"
                :thead="['ID', 'Date', 'Number', 'Description', 'Total', 'Actions']"
                >
    <x-slot name="buttons">
    </x-slot>

    <x-slot name="filters">
        <!-- export import  -->
        <x-crud.exim-csv route_import="{{ route('trades.exim') . '?query=import' }}" route_template="{{ route('trades.exim') . '?query=importTemplate' }}">
        </x-crud.exim-csv>
    </x-slot>


    <x-slot name="modals">
        @include('primary.transaction.trades.show')
    </x-slot>
</x-crud.index-basic>



<script>
    function showjs(data){
        let trigger = 'show_modal_js';

        let html = '<pre>' + JSON.stringify(data, null, 2) + '<br><br>';

        $('#dataform_' + trigger).html(html);

        window.dispatchEvent(new CustomEvent('open-' + trigger));
    }
</script>

<script>
    $(document).ready(function() {
        let indexTable = $('#indexTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('trades.data') }}",
            columns: [
                { data: 'id' },
                { data: 'sent_date' },
                { data: 'number' },
                { data: 'sender_notes' },
                { data: 'total', className: 'text-right',
                    render: function (data, type, row, meta) {
                        return new Intl.NumberFormat('id-ID', { 
                            maximumFractionDigits: 2
                        }).format(data);
                    }},
                { data: 'actions', orderable: false, searchable: false }
            ]
        });

    
        // Export Import
        $('#exportVisibleBtn').on('click', function(e) {
            e.preventDefault();

            let params = indexTable.ajax.params();
            
            let exportUrl = '{{ route("trades.exim") }}' + '?query=export&params=' + encodeURIComponent(JSON.stringify(params));

            window.location.href = exportUrl;
        });
    });
</script>