@php
    $request = request();
    $user = auth()->user();
    $space_role = session('space_role') ?? null;

    $space_id = session('space_id') ?? null;
    if(is_null($space_id)){
        abort(403);
    }

    $player = session('player_id') ? \App\Models\Primary\Player::findOrFail(session('player_id')) : Auth::user()->player;



    $model_type_select = $request->get('model_type_select') ?? null;
    $model_type_option = [];

    if($user->can('space.trades.po') && $user->can('space.trades.so') || $space_role == 'owner'){
        $model_type_option['all'] = 'Semua Trades';   
    }

    if($user->can('space.trades.po') || $space_role == 'owner'){
        $model_type_option['PO'] = 'Purchase Order';
    }

    if($user->can('space.trades.so') || $space_role == 'owner'){
        $model_type_option['SO'] = 'Sales Order';
    }

@endphp

<x-crud.index-basic header="Trades" model="trades" table_id="indexTable" 
                    :thead="['ID', 'Date', 'Number', 'Description', 'SKU', 'Status', 'Total', 'Actions']">
    <x-slot name="buttons">
        @include('primary.transaction.trades.create')

        <x-input-select name="model_type_select" id="model-type-select">
            <option value="">-- Filter Model --</option>
            @foreach ($model_type_option as $key => $value)
                <option value="{{ $key }}" {{ $model_type_select == $key ? 'selected' : '' }}>{{ $value }}</option>
            @endforeach
        </x-input-select>
    </x-slot>

    <x-slot name="filters">
        <!-- export import  -->
        <x-crud.exim-csv route_import="{{ route('trades.exim', ['query' => 'import']) }}" route_template="{{ route('trades.exim', ['query' => 'importTemplate']) }}">
            <h1 class="text-2xl dark:text-white font-bold">Under Construction</h1>
        </x-crud.exim-csv>
    </x-slot>


    <x-slot name="modals">
        @include('primary.transaction.trades.showjs')
    </x-slot>
</x-crud.index-basic>



<!-- Tabel & EXIM  -->
<script>
    $(document).ready(function() {
        let indexTable = $('#indexTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('trades.data') }}",
                data: function (d) {
                    d.return_type = 'DT';
                    d.space_id = {{ $space_id }};
                    d.model_type_select = $('#model-type-select').val() || '';
                }
            },
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

                {
                    data: 'status',
                    render: function(data) {
                        return data || 'unknown';
                    }
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
                    data: 'actions', orderable: false, searchable: false
                }
            ]
        });



        
        $('#model-type-select').on('change', function() {
            indexTable.ajax.reload();
        });


        // Export Import
        $('#exportVisibleBtn').on('click', function(e) {
            e.preventDefault();

            let params = indexTable.ajax.params();
            
            let exportUrl = '{{ route("trades.exim") }}' + '?query=export' 
                                + '&model_type_select=' + $('#model-type-select').val() 
                                + '&params=' + encodeURIComponent(JSON.stringify(params));
                                
            window.location.href = exportUrl;
        });

    });



    function showjs(data) {
        const trigger = 'show_modal_js';
        const parsed = typeof data === 'string' ? JSON.parse(data) : data;


        // ajax get data show
        $.ajax({
            url: "/api/trades/" + parsed.id,
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
</script>
