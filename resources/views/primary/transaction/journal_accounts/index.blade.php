<x-crud.index-basic header="Journal Accounts" model="journal accounts" table_id="indexTable" :thead="['ID', 'Date', 'Number', 'Description', 'Total', 'Actions']">
    <x-slot name="buttons">
        <div class="w-full md:w-auto flex justify-end">
            <a href="{{ route('journal_accounts.create') }}">
                <x-button-add :route="route('journal_accounts.create')" text="Add Journal Entry" />
            </a>
            <form action="{{ route('journal_accounts.import') }}" method="POST" enctype="multipart/form-data"
                class="ml-4 flex items-center">
                @csrf
                <input type="file" name="file" accept=".csv, .txt" class="form-input" required>
                <button type="submit" class="ml-2 bg-blue-500 text-white px-4 py-2 rounded">
                    Upload CSV
                </button>
            </form>
        </div>
    </x-slot>
</x-crud.index-basic>

<script>
    function create() {
        // auto set basecode
        document.getElementById('_basecode').value = document.getElementById('_type_id').selectedOptions[0].dataset
            .basecode;

        let form = document.getElementById('createDataForm');
        form.action = '/accountsp';

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
        form.action = `/accountsp/${data.id}`;

        // Dispatch event ke Alpine.js untuk membuka modal
        window.dispatchEvent(new CustomEvent('edit-modal-js'));
    }
</script>

<script>
    $(document).ready(function() {
        $('#indexTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('journal_accounts.data') }}",
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
    });
</script>
