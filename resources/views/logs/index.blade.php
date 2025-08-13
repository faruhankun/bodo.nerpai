@php
    $layout = session('layout') ?? 'lobby';
@endphp

<x-crud.index-basic header="Logs" model="log" table_id="indexTable" 
                    :thead="['User', 'Event', 'Description', 'Old Data', 'New Data', 'IP', 'User Agent', 'URL', 'Waktu']">
    <x-slot name="buttons">
    </x-slot>

    <x-slot name="filters">
    </x-slot>


    <x-slot name="modals">
    </x-slot>
</x-crud.index-basic>


<script>
    $(document).ready(function() {
        let indexTable = $('#indexTable').DataTable({
            scrollX: true,
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('logs.data') }}",
                data: {
                    return_type: 'DT',
                },
            },
            pageLength: 10,
            columns: [
                { data: 'causer_id',
                    render: function (data, type, row, meta) {
                        return row.causer?.name ? row.causer.name : data;
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
                { data: 'created_at', 
                    render: function(data) {
                        return new Date(data).toLocaleString('id-ID');
                    }
                }
            ]
        });


        setInterval(() => {
            indexTable.ajax.reload();
        }, 5000);
    });
</script>