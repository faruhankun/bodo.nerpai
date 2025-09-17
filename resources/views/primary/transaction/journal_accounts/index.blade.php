@php
    $space_id = session('space_id') ?? null;
    if(is_null($space_id)){
        abort(403);
    }

    $player = session('player_id') ? \App\Models\Primary\Player::findOrFail(session('player_id')) : Auth::user()->player;
@endphp


<x-crud.index-basic header="Journal Accounts" model="journal accounts" table_id="indexTable" 
    :thead="['Date', 'Number', 'Team', 'Description', 'Total', 'Actions']">
    <x-slot name="buttons">
        @include('primary.transaction.journal_accounts.create')
    </x-slot>

    <x-slot name="filters">
        <!-- export import  -->
        <x-crud.exim-csv route_import="{{ route('journal_accounts.import') }}" route_template="{{ route('journal_accounts.import_template') }}">
            <h1 class="text-2xl dark:text-white font-bold">Under Construction</h1>
        </x-crud.exim-csv>
    </x-slot>

    <x-slot name="modals">
        @include('primary.transaction.trades.showjs')
    </x-slot>
</x-crud.index-basic>


<script>
    $(document).ready(function() {
        let indexTable = $('#indexTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('journal_accounts.data') }}",   
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
                    data: 'number',
                    className: 'text-blue-600',
                    render: function (data, type, row, meta) {
                        return `<a href="journal_accounts/${row.id}" target="_blank">${
                                    data
                                }</a>`;
                    }
                },
                
                {
                    data: 'data',
                    render: function(data, type, row, meta) {
                        return (row?.sender?.name || 'sender') + '<br>' + (row?.handler?.name || 'handler');
                    }
                },

                {
                    data: 'sender_notes'
                },
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

        // Export Import
        $('#exportVisibleBtn').on('click', function(e) {
            e.preventDefault();

            let params = indexTable.ajax.params();
            
            let exportUrl = '{{ route("journal_accounts.export") }}' + '?params=' + encodeURIComponent(JSON.stringify(params));

            window.location.href = exportUrl;
        });
    });
</script>

<script>
    function showjs(data) {
        const trigger = 'show_modal_js';
        const parsed = typeof data === 'string' ? JSON.parse(data) : data;


        // ajax get data show
        $.ajax({
            url: "/journal_accounts/" + parsed.id,
            type: "GET",
            data: {
                'page_show': 'show'
            },
            success: function(data) {
                let page_show = data.page_show ?? 'null ??';
                $('#datashow_'+trigger).html(page_show);

                let modal_edit_link = '/journal_accounts/' + parsed.id + '/edit';
                $('#modal_edit_link').attr('href', modal_edit_link);

                window.dispatchEvent(new CustomEvent('open-' + trigger));
            }
        });        
    }
</script>
