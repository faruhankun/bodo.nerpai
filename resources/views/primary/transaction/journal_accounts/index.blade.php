@php
    $space_id = session('space_id') ?? null;
    if(is_null($space_id)){
        abort(403);
    }

    $player = session('player_id') ? \App\Models\Primary\Player::findOrFail(session('player_id')) : Auth::user()->player;
@endphp


<x-crud.index-basic header="Journal Accounts" model="journal accounts" table_id="indexTable" :thead="['ID', 'Date', 'Number', 'Description', 'Total', 'Actions']">
    <x-slot name="buttons">
        @include('primary.transaction.journal_accounts.create')
    </x-slot>

    <x-slot name="filters">
        <!-- export import  -->
        <x-crud.exim-csv route_import="{{ route('journal_accounts.import') }}" route_template="{{ route('journal_accounts.import_template') }}">
            <h1 class="text-2xl dark:text-white font-bold">Under Construction</h1>
        </x-crud.exim-csv>
    </x-slot>
</x-crud.index-basic>


<script>
    $(document).ready(function() {
        let indexTable = $('#indexTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('journal_accounts.data') }}",   
            pageLength: 10,
            columns: [{
                    data: 'id'
                },
                {
                    data: 'sent_time',
                    render: function(data) {
                        return new Date(data).toLocaleDateString('id-ID');
                    }
                },
                {
                    data: 'number'
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
