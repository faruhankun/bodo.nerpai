@php
    $layout = session('layout');
@endphp
<x-dynamic-component :component="'layouts.' . $layout">
    <div class="py-12">
        <div class="max-w-7xl my-10 mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-white">
                    <h3 class="text-lg font-bold dark:text-white">Manage Persons</h3>
                    <p class="text-sm dark:text-gray-200 mb-6">Create, edit, and manage your persons listings.</p>
                    <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>

                    <!-- Search and Add New Customer -->
                    <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 mb-4">
                       
                        <div class="w-full md:w-auto flex justify-end">
                            <x-button-add :route="route('persons.create')" text="Tambah Customer" />
                        </div>
                    </div>
                    <x-table-table id="personsTable">
                        <x-table-thead >
                            <tr>
                                <x-table-th>ID</x-table-th>
                                <x-table-th>Number</x-table-th>
                                <x-table-th>Player</x-table-th>
                                <x-table-th>Name</x-table-th>
                                <x-table-th>Phone Number</x-table-th>
                                <x-table-th>Address</x-table-th>
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
    $('#personsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('persons.data') }}",
        columns: [
            { data: 'id' },
            { data: 'number' },
            { data: 'player.code', render: function(data, type, row) {
                return data ? data : 'N/A';
            }},
            { data: 'name' },
            { data: 'phone_number' },
            { data: 'address' },
            { data: 'status' },
            { data: 'notes' },
            { data: 'actions', orderable: false, searchable: false }
        ]
    });
});
</script>