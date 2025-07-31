@php
    $player_id = session('player_id') ?? auth()->user()->player->id;
@endphp

<x-crud.index-basic header="Contacts" 
                model="Contact" 
                table_id="indexTable"
                :thead="['Id', 'Name', 'Role', 'Status', 'Notes', 'Actions']"
                >
    <x-slot name="buttons">
    </x-slot>

    <x-slot name="filters">
        <!-- export import  -->
        <x-crud.exim-csv route_import="{{ route('contacts.exim') . '?query=import' }}" route_template="{{ route('contacts.exim') . '?query=importTemplate' }}">
        </x-crud.exim-csv>
    </x-slot>


    <x-slot name="modals">
        @include('primary.player.contacts.show')
    </x-slot>
</x-crud.index-basic>



<script>
    function showjs(data){
        let trigger = 'show_modal_js';

        let html = '<pre>' + JSON.stringify(data, null, 2) + '<br><br>';
        // html += '<pre>' + JSON.stringify(data.model2, null, 2) + '<br><br>'
        // html += '<pre>' + JSON.stringify(data.model2.size, null, 2) + '<br><br>';

        $('#dataform_' + trigger).html(html);

        window.dispatchEvent(new CustomEvent('open-' + trigger));
    }

    function edit(data) {
        document.getElementById('edit_id').value = data.id;

        document.getElementById('edit_type').value = data.type;

        document.getElementById('edit_status').value = data.status === '1' || data.status === 'active' ? 'active' : 'inactive';

        document.getElementById('edit_notes').value = data.notes;

        let form = document.getElementById('editDataForm');
        form.action = `/contacts/related/${data.id}`;

        // Dispatch event ke Alpine.js untuk membuka modal
        window.dispatchEvent(new CustomEvent('edit-modal-js'));
    }
</script>

<script>
    $(document).ready(function() {
        let indexTable = $('#indexTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('contacts.data') }}",
            columns: [
                { data: 'id' },
                { data: 'model2.size.name', render: function(data, type, row) {
                    return data ? data : 'N/A';
                }},
                { data: 'type' },
                { data: 'status' },
                { data: 'notes' },
                { data: 'actions', orderable: false, searchable: false }
            ]
        });

    
        // Export Import
        $('#exportVisibleBtn').on('click', function(e) {
            e.preventDefault();

            let params = indexTable.ajax.params();
            
            let exportUrl = '{{ route("contacts.exim") }}' + '?query=export&params=' + encodeURIComponent(JSON.stringify(params));

            window.location.href = exportUrl;
        });
    });
</script>