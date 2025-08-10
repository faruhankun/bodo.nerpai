@php
    $player_id = session('player_id') ?? auth()->user()->player->id;

    $space_id = session('space_id') ?? null;
@endphp

<x-crud.index-basic header="Kontak" 
                model="player" 
                table_id="indexTable"
                :thead="['ID', 'Name', 'Email', 'Phone', 'Notes', 'Actions']"
                >
    <x-slot name="buttons">
        @include('primary.player.players.create')
    </x-slot>


    <x-slot name="modals">
        @include('primary.player.players.edit')

        @include('primary.player.players.show')
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



    function create(){
        let form = document.getElementById('createDataForm');
        form.action = '/players';

        // Dispatch event ke Alpine.js untuk membuka modal
        window.dispatchEvent(new CustomEvent('open-modal-create'));
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



    function edit(data) {
        document.getElementById('edit_id').value = data.id;

        document.getElementById('edit_name').value = data.name;

        document.getElementById('edit_email').value = data.email;

        document.getElementById('edit_phone_number').value = data.phone_number;

        document.getElementById('edit_status').value = data.status === '1' || data.status === 'active' ? 'active' : 'inactive';

        document.getElementById('edit_notes').value = data.notes;

        let form = document.getElementById('editDataForm');
        form.action = `/players/${data.id}`;

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
        let space_id = "{{ $space_id }}";

        let indexTable = $('#indexTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "/players/data",
                data: {
                    return_type: 'DT',
                    space: 'this',
                    space_id: space_id,
                }
            },
            columns: [
                { data: 'id' },
                { data: 'name' },
                { data: 'email' },
                { data: 'phone_number' },
                { data: 'notes' },
                { data: 'actions', orderable: false, searchable: false }
            ]
        });
    });
</script>