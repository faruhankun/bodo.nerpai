<x-crud.index-basic header="Sales" 
                model="sales" 
                table_id="indexTable"
                :thead="['ID', 'Date', 'Number', 'Description', 'Total', 'Actions']"
                >
    <x-slot name="buttons">
        <div class="w-full md:w-auto flex justify-end">
            <a href="{{ route('trades.create') }}">
                <x-button-add :route="route('trades.create', ['type' => 'so'])" text="Add Trade" />
            </a>
        </div>
    </x-slot>
</x-crud.index-basic>

<script>
$(document).ready(function() {
    $('#indexTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('trades.so.data') }}",
        pageLength: 25,
        columns: [
            { data: 'id' },
            { data: 'sent_date' },
            { data: 'number' },
            { data: 'sender_notes' },
            { data: 'total', className: 'text-right',
                render: function (data, type, row, meta) {
                    return new Intl.NumberFormat('id-ID', { 
                        maximumFractionDigits: 2
                    }).format(data);
                }},
            { data: 'actions', orderable: false, searchable: false }
        ]
    });
});
</script>
