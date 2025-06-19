@php
    $player_id = session('player_id') ?? auth()->user()->player->id;
@endphp

<x-crud.index-basic header="Players" 
                model="player" 
                table_id="indexTable"
                :thead="['ID', 'Number', 'Size', 'Name', 'Type', 'Status', 'Notes', 'Actions']"
                >
    <x-slot name="buttons">
        @include('primary.players.create')
    </x-slot>


    <x-slot name="modals">
        @include('primary.players.edit', ['edit' => ['data' => 'Edit Account']])
    </x-slot>
</x-crud.index-basic>



<script>
    function edit(data) {
        document.getElementById('edit_id').value = data.id;

        document.getElementById('edit_type').value = data.type;

        document.getElementById('edit_status').value = data.status === '1' || data.status === 'active' ? 'active' : 'inactive';

        document.getElementById('edit_notes').value = data.notes;

        let form = document.getElementById('editDataForm');
        form.action = `/players/related/${data.id}`;

        // Dispatch event ke Alpine.js untuk membuka modal
        window.dispatchEvent(new CustomEvent('edit-modal-js'));
    }
</script>

<script>
    $(document).ready(function() {
        let indexTable = $('#indexTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('players.related') }}",
            columns: [
                { data: 'id' },
                { data: 'number' },
                { data: 'size_display' },
                { data: 'model1.name' },
                { data: 'type' },
                { data: 'status' },
                { data: 'notes' },
                { data: 'actions', orderable: false, searchable: false }
            ]
        });
    });
</script>


<script>
    $(document).ready(function() {
        $('#create_new_player_id').select2({
            placeholder: 'Search & Select Player',
            minimumInputLength: 2,
            ajax: {
                url: '/players/search',
                dataType: 'json',
                paginate: true,
                data: function(params) {
                    return {
                        q: params.term,
                        player_id: '{{ $player_id }}',
                        page: params.page || 1
                    };
                },
                processResults: function(data) {
                    return {
                        results: data
                    };
                },
                cache: true
            }
        });
    });
</script>