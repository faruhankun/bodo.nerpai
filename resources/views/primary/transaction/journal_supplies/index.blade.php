@php
    $space_id = session('space_id') ?? null;
    if(is_null($space_id)){
        abort(403);
    }

    $player = session('player_id') ? \App\Models\Primary\Player::findOrFail(session('player_id')) : Auth::user()->player;
@endphp

<x-crud.index-basic header="Journal Supplies" model="journal supplies" table_id="indexTable" :thead="['ID', 'Date', 'Number', 'Description', 'Total', 'Actions']">
    <x-slot name="panel">
        <div class="grid grid-cols-3 sm:grid-cols-3 gap-6">
            <div class="form-group">
                @include('primary.transaction.journal_supplies.import')
            </div>
            <div class="form-group">
                <!-- <a href="{{ route('journal_supplies.create') }}">
                    <x-button-add :route="route('journal_supplies.create')" text="Add Journal" class="h-fit w-48" />
                </a> -->
                @include('primary.transaction.journal_supplies.create')
            </div>
        </div>
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

    function edit(data) {
        document.getElementById('edit_id').value = data.id;

        document.getElementById('edit_name').value = data.name;

        document.getElementById('edit_type_id').value = data.type_id;
        document.getElementById('edit_basecode').value = document.getElementById('edit_type_id').selectedOptions[0]
            .dataset.basecode;
        document.getElementById('edit_code').value = data.code.substring($('#edit_basecode').val().length);

        document.getElementById('edit_status').value = data.status === '1' || data.status === 'active' ? 'active' :
            'inactive';
        document.getElementById('edit_parent_id').value = data.parent_id;

        document.getElementById('edit_notes').value = data.notes;

        let form = document.getElementById('editDataForm');
        form.action = `/journal_supplies/${data.id}`;

        // Dispatch event ke Alpine.js untuk membuka modal
        window.dispatchEvent(new CustomEvent('edit-modal-js'));
    }
</script>

<script>
    $(document).ready(function() {
        $('#indexTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('journal_supplies.data') }}",   
            pageLength: 25,
            columns: [{
                    data: 'id'
                },
                {
                    data: 'sent_time',
                    render: function(data) {
                        return new Date(data).toLocaleDateString();
                    }
                },
                {
                    data: 'number'
                },
                {
                    data: 'handler_notes'
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


        // setup create
        $('#create_sender_id').val('{{ $player->id }}');

    });
</script>
