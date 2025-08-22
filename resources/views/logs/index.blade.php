@php
    $layout = session('layout') ?? 'lobby';
    $request = request();


    $model_type_select = $request->get('model_type_select') ?? null;
    $model_type_option = \App\Models\User::all()->pluck('name', 'id')->toArray();
@endphp

<x-crud.index-basic header="Logs" model="log" table_id="indexTable" 
                    :thead="['User', 'Waktu', 'Event', 'Description', 'Old Data', 'New Data', 'IP', 'User Agent', 'URL']">
    <x-slot name="buttons">

        <x-input-select name="model_type_select" id="model-type-select">
            <option value="">-- Filter User --</option>
            @foreach ($model_type_option as $key => $value)
                <option value="{{ $key }}" {{ $model_type_select == $key ? 'selected' : '' }}>{{ $value }}</option>
            @endforeach
        </x-input-select>
    </x-slot>

    <x-slot name="filters">
    </x-slot>


    <x-slot name="modals">
    </x-slot>
</x-crud.index-basic>


<script>
    $(document).ready(function() {
        let users = @json($model_type_option);


        let indexTable = $('#indexTable').DataTable({
            scrollX: true,
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('logs.data') }}",
                data: function(d) {
                    d.return_type = 'DT';
                    d.space = 'this';
                    d.user_id = $('#model-type-select').val() || '';
                },
            },
            pageLength: 10,
            columns: [
                { data: 'created_at', 
                    render: function(data) {
                        return new Date(data).toLocaleString('id-ID');
                    }
                },
                { data: 'causer_id',
                    render: function (data, type, row, meta) {
                        return users[data] ?? 'N/A';
                    }
                },
                { data: 'event', name: 'event' },
                { data: 'description', name: 'description' },
                { data: 'properties.old', 
                    render: function (data, type, row, meta) {
                        return row.properties.old ? JSON.stringify(row.properties.old) : '[]';
                    }
                },
                { data: 'properties.attributes', 
                    render: function (data, type, row, meta) {
                        return row.properties.attributes ? JSON.stringify(row.properties.attributes) : '[]';
                    }
                },
                { data: 'properties.ip_address', name: 'properties.ip_address' },
                { data: 'properties.user_agent', 
                    render: function (data, type, row, meta) {
                        return row.properties?.user_agent ? JSON.stringify(row.properties.user_agent) : '[]';
                    }
                },
                { data: 'properties.url', name: 'properties.url' },
            ]
        });


        setInterval(() => {
            indexTable.ajax.reload();
        }, 5000);



        $('#model-type-select').on('change', function() {
            indexTable.ajax.reload();
        });
    });
</script>