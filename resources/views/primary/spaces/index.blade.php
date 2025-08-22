@php 
    $user = auth()->user();
    $space_id = get_space_id(request());
    setPermissionsTeamId($space_id);
@endphp


<x-crud.index-basic header="Spaces" 
                model="space" 
                table_id="indexTable"
                :thead="['Code', 'Parent', 'Name', 'Address', 'Status', 'Notes', 'Actions']"
                >
    <x-slot name="buttons">
        @if($user->can('space.spaces.crud', 'web'))
            @include('primary.spaces.create')
        @endif
    </x-slot>

    <x-slot name="modals">
        @include('primary.spaces.edit')
    </x-slot>
</x-crud.index-basic>

<script>
    function edit(data) {
        document.getElementById('edit_id').value = data.id;

        document.getElementById('edit_code').value = data.code;

        document.getElementById('edit_name').value = data.name;

        document.getElementById('edit_address').value = data.address ? JSON.stringify(data.address) : '{"detail":"Blitar, Jawa Timur"}';

        document.getElementById('edit_status').value = data.status === '1' || data.status === 'active' ? 'active' : 'inactive';

        document.getElementById('edit_notes').value = data.notes;

        let form = document.getElementById('editDataForm');
        form.action = `/spaces/${data.id}`;

        // Dispatch event ke Alpine.js untuk membuka modal
        window.dispatchEvent(new CustomEvent('edit-modal-js'));
    }


    $('#editDataForm').on('submit', function(e) {
        e.preventDefault();

        let form = $(this);
        let actionUrl = form.attr('action');
        let formData = form.serialize();

        $.ajax({
            url: actionUrl,
            type: 'POST', // pakai POST kalau pakai method spoofing Laravel (`@method('PUT')`)
            data: formData,
            success: function(response) {
                // Reload data di table DataTable (tanpa reload full halaman)
                $('#indexTable').DataTable().ajax.reload(null, false);

                // Tutup modal (kalau pakai Alpine.js, sesuaikan)
                window.dispatchEvent(new CustomEvent('close-edit-modal-js'));

                // Optional: tampilkan notifikasi
                Swal.fire({
                    title: "Success",
                    text: response.message,
                    icon: "success",
                    timer: 3000,
                    customClass: {
                        popup: 'bg-white p-6 rounded-lg shadow-xl dark:bg-gray-900 dark:border dark:border-sky-500',   // Customize the popup box
                        title: 'text-xl font-semibold text-green-600',
                        header: 'text-sm text-gray-700 dark:text-white',
                        content: 'text-sm text-gray-700 dark:text-white',
                        confirmButton: 'bg-emerald-900 text-white font-bold py-2 px-4 rounded-md hover:bg-emerald-700 focus:ring-2 focus:ring-emerald-300' // Customize the button
                    }
                });

                console.log(response);
            },
            error: function(xhr) {
                Swal.fire({
                    title: "Error",
                    text: xhr.responseJSON.message,
                    icon: "error",
                    timer: 5000,
                    customClass: {
                        popup: 'bg-white p-6 rounded-lg shadow-xl dark:bg-gray-900 dark:border dark:border-sky-500 dark:text-white',   // Customize the popup box
                        title: 'text-xl font-semibold text-green-600',
                        header: 'text-sm text-gray-700 dark:text-white',
                        content: 'text-sm text-gray-700 dark:text-white',
                        confirmButton: 'bg-red-900 text-white font-bold py-2 px-4 rounded-md hover:bg-red-700 focus:ring-2 focus:ring-red-300' // Customize the button
                    }
                });

                console.log(xhr);
            }
        });
    });
</script>

<script>
    $(document).ready(function() {
        $('#indexTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('spaces.data') }}",
                data: function(d) {
                    d.space_id = @json(session('space_id')),
                    d.return_type = 'DT';
                    d.include_self = true;
                }
            },
            columns: [
                {data: 'code', name: 'code'},
                {data: 'parent_display', name: 'parent_display'},
                {data: 'name', name: 'name'},
                {data: 'address', 
                    render: function(data, type, row) {
                        return data ? (data.detail ?? '-') : '-';
                    }
                },
                {data: 'status', name: 'status'},
                {data: 'notes', name: 'notes'},
                {data: 'actions', name: 'actions', orderable: false, searchable: false}
            ]
        });
    });
</script>