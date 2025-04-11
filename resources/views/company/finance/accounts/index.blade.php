<x-crud.index-basic header="Accounts" 
                model="account" 
                table_id="indexTable"
                :thead="['Code', 'Name', 'Type', 'Balance', 'Notes', 'Actions']"
                >
    <x-slot name="buttons">
        <x-button2 onclick="create()" class="btn btn-primary">Create Account</x-button2>
    </x-slot>
    
    <x-slot name="modals">
        @include('company.finance.accounts.create', ['create' => ['data' => 'Create Account']])
        @include('company.finance.accounts.edit', ['edit' => ['data' => 'Edit Account']])
    </x-slot>
</x-crud.index-basic>

<script>
    function create(){
        // auto set basecode
        document.getElementById('_basecode').value = document.getElementById('_type_id').selectedOptions[0].dataset.basecode;

        let form = document.getElementById('createDataForm');
        form.action = '/accounts';

        // Dispatch event ke Alpine.js untuk membuka modal
        window.dispatchEvent(new CustomEvent('create-modal'));
    }

    function edit(data) {
        document.getElementById('edit_id').value = data.id;

        document.getElementById('edit_name').value = data.name;

        document.getElementById('edit_type_id').value = data.type_id;
        document.getElementById('edit_basecode').value = document.getElementById('edit_type_id').selectedOptions[0].dataset.basecode;
        document.getElementById('edit_code').value = data.code.substring($('#edit_basecode').val().length);

        document.getElementById('edit_status').value = data.status === '1' || data.status === 'Active' ? 'Active' : 'Inactive';
        document.getElementById('edit_parent_id').value = data.parent_id;

        document.getElementById('edit_notes').value = data.notes;

        let form = document.getElementById('editDataForm');
        form.action = `/accounts/${data.id}`;

        // Dispatch event ke Alpine.js untuk membuka modal
        window.dispatchEvent(new CustomEvent('edit-modal'));
    }
</script>

<script>
$(document).ready(function() {
    $('#indexTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('accounts.data') }}",
        pageLength: 25,
        columns: [
            { data: 'code' },
            { data: 'name' },
            { data: 'account_type.name' },
            { data: 'balance', className: 'text-right' },
            { data: 'notes' },
            { data: 'actions', orderable: false, searchable: false }
        ]
    });
});
</script>


