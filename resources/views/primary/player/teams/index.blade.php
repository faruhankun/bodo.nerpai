@php 
    $space_id = session('space_id') ?? null;
@endphp

<x-crud.index-basic header="Teams" 
                model="role" 
                table_id="indexTable"
                :thead="[
                    'Id', 'Name', 'email', 'Role Umum', 'Roles', 'Permissions', 'Notes', 'Actions']"
                >
    <x-slot name="buttons">
        @include('primary.player.teams.create')

    </x-slot>


    <x-slot name="filters">

    </x-slot>


    <x-slot name="modals">
        @include('primary.player.teams.edit')
    </x-slot>
</x-crud.index-basic>



<script>
    function create(){
        let form = document.getElementById('createDataForm');
        form.action = '/teams';

        // Dispatch event ke Alpine.js untuk membuka modal
        window.dispatchEvent(new CustomEvent('modal-create'));
    }

    $('#createDataForm').on('submit', function(e) {
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
                window.dispatchEvent(new CustomEvent('close-modal-create'));

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
                    timer: 3000,
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



    function edit(data) {
        relation = data.player.space_relations.find((relation) => relation.model1_id == {{ $space_id }});
        
        document.getElementById('edit_id').value = data.id;

        document.getElementById('edit_name').value = data.name;


        // from relation
        document.getElementById('edit_type').value = relation.type;
        document.getElementById('edit_notes').value = relation.notes;


        // permissions
        document.querySelectorAll('input[type="checkbox"]').forEach((checkbox) => {
            checkbox.checked = false;
        });

        (data.permissions ?? []).forEach((permission) => {
            const cb = document.getElementById('perm_' + permission.id);
            if(cb) cb.checked = true;
        });



        let form = document.getElementById('editDataForm');
        form.action = `/teams/${data.id}`;

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
                    timer: 3000,
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


<!-- add new user  -->
<script>
    $(document).ready(function() {
        $('#create_user_id').select2({
            placeholder: 'Search & Select User',
            width: '100%',
            padding: '0px 12px',
            ajax: {
                url: '/api/teams/add-user',
                dataType: 'json',
                paginate: true,
                data: function(params) {
                    return {
                        q: params.term,
                        space_id: '{{ $space_id }}',
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



<!-- Roles  -->
<script>
    $(document).ready(function() {
        $('#edit_role_id').select2({
            placeholder: 'Search & Select Roles',
            width: '100%',
            padding: '0px 12px',
            ajax: {
                url: '/api/roles/data',
                dataType: 'json',
                paginate: true,
                data: function(params) {
                    return {
                        q: params.term,
                        space_id: '{{ $space_id }}',
                        page: params.page || 1
                    };
                },
                processResults: function(res) {
                    return {
                        results: res.data.map((role) => {
                            return {
                                id: role.id,
                                text: role.name
                            }
                        })
                    };
                },
                cache: true
            }
        });
    });
</script>


<!-- Index  -->
<script>
    $(document).ready(function() {
        let space_id = "{{ $space_id }}";

        let indexTable = $('#indexTable').DataTable({
            scrollX: true,
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('teams.data') }}",
                data: {
                    return_type: 'DT',
                    roles: space_id ? 'space' : 'all',
                }
            },
            pageLength: 10,
            columns: [
                { data: 'id', name: 'id' },
                { data: 'name' },
                { data: 'email' },
                { data: 'relation_type' },
                { data: 'show_roles' },
                { data: 'show_permissions' },
                { data: 'relation_notes' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false },
            ]
        });
    });
</script>