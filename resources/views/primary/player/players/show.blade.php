@php
    $layout = session('layout') ?? 'lobby';
    $space_role = session('space_role') ?? null;

    $id_address = 'address';
@endphp

<x-dynamic-component :component="'layouts.' . $layout">
    <div class="py-12">
        <div class=" sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-white">
                    <h1 class="text-2xl font-bold mb-6">Details: {{ $data->code }} : {{ $data->name }}</h1>
                    <div class="mb-3 mt-1 flex-grow border-t border-gray-300 dark:border-gray-700"></div>

                    @include('primary.player.players.partials.datashow')

                    

                    @include('primary.player.players.edit', ['address' => true])



                    @include('primary.transaction.trades.showjs')

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


        // address
        loadAddressPrefill(data);
        document.getElementById('address_province').value = data.province;
        document.getElementById('address_regency').value = data.regency;
        document.getElementById('address_district').value = data.district;
        document.getElementById('address_village').value = data.village;


        // marketplace
        document.getElementById('edit_shopee_username').value = data.shopee_username ?? '';
        document.getElementById('edit_tokopedia_username').value = data.tokopedia_username ?? '';
        document.getElementById('edit_whatsapp_number').value = data.whatsapp_number ?? '';


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

<script>
    function showjs_tx(data) {
        console.log(data);

        const trigger = 'show_modal_js';
        const parsed = data;


        // ajax get data show
        $.ajax({
            url: "/api/trades/" + parsed.id,
            type: "GET",
            data: {
                'page_show': 'show'
            },
            success: function(data) {
                let page_show = data.page_show ?? 'null ??';
                $('#datashow_'+trigger).html(page_show);

                let modal_edit_link = '/trades/' + parsed.id + '/edit';
                $('#modal_edit_link').attr('href', modal_edit_link);

                window.dispatchEvent(new CustomEvent('open-' + trigger));
            }
        });        
    }
</script>



<script>
    async function loadAddressPrefill(data) {
        let provinsiSelect = document.getElementById("{{ $id_address ?? '' }}_province_id");
        let regencySelect = document.getElementById("{{ $id_address ?? '' }}_regency_id");
        let districtSelect = document.getElementById("{{ $id_address ?? '' }}_district_id");
        let villageSelect = document.getElementById("{{ $id_address ?? '' }}_village_id");

        const BASE_URL = "https://virdausa.github.io/api-wilayah-indonesia/api";

        // --- SET PROVINSI ---
        provinsiSelect.value = data.address.province_id ?? '';

        // --- FETCH & SET KABUPATEN ---
        if (data.address.province_id) {
            let regencies = await fetch(`${BASE_URL}/regencies/${data.address.province_id}.json`).then(r => r.json());
            regencySelect.innerHTML = `<option value="">-- Pilih Kabupaten/Kota --</option>`;
            regencies.forEach(r => {
                regencySelect.innerHTML += `<option value="${r.id}">${r.name}</option>`;
            });
            regencySelect.value = data.address.regency_id ?? '';
        }

        // --- FETCH & SET KECAMATAN ---
        if (data.address.regency_id) {
            let districts = await fetch(`${BASE_URL}/districts/${data.address.regency_id}.json`).then(r => r.json());
            districtSelect.innerHTML = `<option value="">-- Pilih Kecamatan --</option>`;
            districts.forEach(d => {
                districtSelect.innerHTML += `<option value="${d.id}">${d.name}</option>`;
            });
            districtSelect.value = data.address.district_id ?? '';
        }

        // --- FETCH & SET DESA ---
        if (data.address.district_id) {
            let villages = await fetch(`${BASE_URL}/villages/${data.address.district_id}.json`).then(r => r.json());
            villageSelect.innerHTML = `<option value="">-- Pilih Desa --</option>`;
            villages.forEach(v => {
                villageSelect.innerHTML += `<option value="${v.id}">${v.name}</option>`;
            });
            villageSelect.value = data.address.village_id ?? '';
        }

        // --- FIELD LAIN ---
        document.getElementById('address_postal_code').value = data.postal_code ?? '';
        document.getElementById('address_address_detail').value = data.address_detail ?? '';
    }


    document.addEventListener("DOMContentLoaded", function () {
        const BASE_URL = "https://virdausa.github.io/api-wilayah-indonesia/api";

        let provinsiSelect = document.getElementById("{{ $id_address ?? '' }}_province_id");
        let regencySelect = document.getElementById("{{ $id_address ?? '' }}_regency_id");
        let districtSelect = document.getElementById("{{ $id_address ?? '' }}_district_id");
        let villageSelect = document.getElementById("{{ $id_address ?? '' }}_village_id");

        // Load provinsi
        fetch(`${BASE_URL}/provinces.json`)
            .then(res => res.json())
            .then(data => {
                provinsiSelect.innerHTML = `<option value="">-- Pilih Provinsi --</option>`;
                data.forEach(p => {
                    provinsiSelect.innerHTML += `<option value="${p.id}">${p.name}</option>`;
                });
            });

        // Ketika pilih provinsi → load kabupaten/kota
        provinsiSelect.addEventListener("change", function () {
            let provId = this.value;
            regencySelect.innerHTML = `<option>Loading...</option>`;
            districtSelect.innerHTML = `<option value="">-- Pilih Kecamatan --</option>`;
            villageSelect.innerHTML = `<option value="">-- Pilih Desa --</option>`;

            fetch(`${BASE_URL}/regencies/${provId}.json`)
                .then(res => res.json())
                .then(data => {
                    regencySelect.innerHTML = `<option value="">-- Pilih Kabupaten/Kota --</option>`;
                    data.forEach(r => {
                        regencySelect.innerHTML += `<option value="${r.id}">${r.name}</option>`;
                    });
                });
        });

        // Ketika pilih kabupaten/kota → load kecamatan
        regencySelect.addEventListener("change", function () {
            let regId = this.value;
            districtSelect.innerHTML = `<option>Loading...</option>`;
            villageSelect.innerHTML = `<option value="">-- Pilih Desa --</option>`;

            fetch(`${BASE_URL}/districts/${regId}.json`)
                .then(res => res.json())
                .then(data => {
                    districtSelect.innerHTML = `<option value="">-- Pilih Kecamatan --</option>`;
                    data.forEach(d => {
                        districtSelect.innerHTML += `<option value="${d.id}">${d.name}</option>`;
                    });
                });
        });

        // Ketika pilih kecamatan → load desa
        districtSelect.addEventListener("change", function () {
            let disId = this.value;
            villageSelect.innerHTML = `<option>Loading...</option>`;

            fetch(`${BASE_URL}/villages/${disId}.json`)
                .then(res => res.json())
                .then(data => {
                    villageSelect.innerHTML = `<option value="">-- Pilih Desa/Kelurahan --</option>`;
                    data.forEach(v => {
                        villageSelect.innerHTML += `<option value="${v.id}">${v.name}</option>`;
                    });
                });
        });


        // update name
        provinsiSelect.addEventListener("change", function () {
            document.getElementById("{{ $id_address ?? '' }}_province").value = 
                this.options[this.selectedIndex].text;
        });

        regencySelect.addEventListener("change", function () {
            document.getElementById("{{ $id_address ?? '' }}_regency").value = 
                this.options[this.selectedIndex].text;
        });

        districtSelect.addEventListener("change", function () {
            document.getElementById("{{ $id_address ?? '' }}_district").value = 
                this.options[this.selectedIndex].text;
        });

        villageSelect.addEventListener("change", function () {
            document.getElementById("{{ $id_address ?? '' }}_village").value = 
                this.options[this.selectedIndex].text;
        });

    });
</script>
