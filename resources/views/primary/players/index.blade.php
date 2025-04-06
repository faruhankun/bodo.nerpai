@php
    $layout = session('layout');
@endphp
<x-dynamic-component :component="'layouts.' . $layout">
    <div class="py-12">
        <div class="max-w-7xl my-10 mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-white">
                    <h3 class="text-lg font-bold dark:text-white">Manage Players</h3>
                    <p class="text-sm dark:text-gray-200 mb-6">Create, edit, and manage your players listings.</p>
                    <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>

                    <x-table-table id="playersTable">
                        <x-table-thead >
                            <tr>
                                <x-table-th>ID</x-table-th>
                                <x-table-th>Code</x-table-th>
                                <x-table-th>Size</x-table-th>
                                <x-table-th>Type</x-table-th>
                                <x-table-th>Name</x-table-th>
                                <x-table-th>Status</x-table-th>
                                <x-table-th>Notes</x-table-th>
                                <x-table-th>Actions</x-table-th>
                            </tr>
                        </x-table-thead>
                    </x-table-table>
                </div>
            </div>
        </div>
    </div>
    
</x-dynamic-component>

<script>
$(document).ready(function() {
    $('#playersTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('players.data') }}",
        columns: [
            { data: 'id' },
            { data: 'number' },
            { data: 'user.username', render: function(data, type, row) {
                return data ? data : 'N/A';
            }},
            { data: 'type_type' },
            { data: 'name' },
            { data: 'status' },
            { data: 'notes' },
            { data: 'actions', orderable: false, searchable: false }
        ]
    });
});
</script>