@php
    $layout = session('layout') ?? 'lobby';
    $space_role = session('space_role') ?? null;


@endphp

<x-dynamic-component :component="'layouts.' . $layout">
    <div class="py-12">
        <div class=" sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-white">
                    <h1 class="text-2xl font-bold mb-6">Details: {{ $data->name }} in {{ $data?->email ?? 'email' }} in: {{ $data?->phone_number ?? 'phone_number' }}</h1>
                    <div class="mb-3 mt-1 flex-grow border-t border-gray-300 dark:border-gray-700"></div>

                    @include('primary.player.players.partials.datashow')

                    

                    @include('primary.player.players.edit')



                    <!-- Action Section -->
                    <div class="flex justify-end space-x-4">
                        <x-secondary-button>
                            <a href="{{ route('players.index') }}">Back to List</a>
                        </x-secondary-button>

                        @if($space_role == 'admin' || $space_role == 'owner')
                            <a href="javascript:void(0)" onclick="edit({{ json_encode($data) }})">
                                <x-primary-button type="button">Edit Data</x-primary-button>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-dynamic-component>



<script>
    function edit(data) {
        document.getElementById('edit_id').value = data.id;

        document.getElementById('edit_name').value = data.name;

        document.getElementById('edit_code').value = data.code;

        document.getElementById('edit_email').value = data.email;

        document.getElementById('edit_phone_number').value = data.phone_number;

        document.getElementById('edit_status').value = data.status === '1' || data.status === 'active' ? 'active' : 'inactive';

        document.getElementById('edit_notes').value = data.notes;

        let form = document.getElementById('editDataForm');
        form.action = `/players/${data.id}`;

        // Dispatch event ke Alpine.js untuk membuka modal
        window.dispatchEvent(new CustomEvent('edit-modal-js'));
    }


    $(document).ready(function() {
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
                    window.location.reload();

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
    });
</script>