@php
    $space_id = session('space_id') ?? null;
@endphp

<x-crud.index-basic header="Space Players" 
                model="space_player" 
                table_id="indexTable"
                :thead="['Code', 'Name', 'Role', 'Status', 'Notes', 'Actions']"
                >
    <x-slot name="buttons">
        @include('space.space_players.create')
    </x-slot>

    <x-slot name="modals">
        @include('space.space_players.edit')
    </x-slot>
</x-crud.index-basic>

<script>
    function edit(data) {
        document.getElementById('edit_id').value = data.id;

        $('#edit_type').val(data.pivot.type).trigger('change');

        document.getElementById('edit_status').value = data.pivot.status === '1' || data.pivot.status === 'active' ? 'active' : 'inactive';

        document.getElementById('edit_notes').value = data.pivot.notes;

        let form = document.getElementById('editDataForm');
        form.action = `/space_players/${data.id}`;

        // Dispatch event ke Alpine.js untuk membuka modal
        window.dispatchEvent(new CustomEvent('edit-modal-js'));
    }
</script>

<script>
$(document).ready(function() {
    $('.space_id').val(@json($space_id));

    $('#indexTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('space_players.data') }}",
        columns: [
            { data: 'code' },
            { data: 'size.name' },
            { data: 'pivot.type' },
            { data: 'pivot.status' },
            { data: 'pivot.notes' },
            { data: 'actions', orderable: false, searchable: false }
        ]
    });
});
</script>

<script>
    $(document).ready(function() {
        $('#create_player_id').select2({
            placeholder: 'Search & Select Player',
            minimumInputLength: 2,
            ajax: {
                url: '/space_players/search',
                dataType: 'json',
                paginate: true,
                data: function(params) {
                    return {
                        q: params.term,
                        space_id: @json(session('space_id')),
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