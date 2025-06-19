@php
    $player_id = session('player_id') ?? auth()->user()->player->id;
@endphp

<x-crud.index-basic header="Contacts" 
                model="Contact" 
                table_id="indexTable"
                :thead="['Code', 'Name', 'Role', 'Status', 'Notes', 'Actions']"
                >
    <x-slot name="buttons">
    </x-slot>

    <x-slot name="filters">
        <!-- export import  -->
        <x-crud.exim-csv route_import="{{ route('contacts.exim') . '?query=import' }}" route_template="{{ route('contacts.exim') . '?query=importTemplate' }}">
        </x-crud.exim-csv>
    </x-slot>


    <x-slot name="modals">
    </x-slot>
</x-crud.index-basic>



<script>
    function showjs(data){
        alert(JSON.stringify(data));
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
                { data: 'code' },
                { data: 'size.name' },
                { data: 'pivot.type' },
                { data: 'pivot.status' },
                { data: 'pivot.notes' },
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