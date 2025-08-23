@props(['id' => ''])

<div>
    <span class="block text-lg text-gray-700 dark:text-gray-300">Alamat</span>
    <div class="grid grid-cols-3 sm:grid-cols-3 gap-6">
        <div class="form-group mb-4">
            <x-input-label for="province">Provinsi</x-input-label>
            <select name="address[province_id]" id="{{ $id ?? '' }}_province_id" class="w-full border rounded p-2" ></select>
            <input type="hidden" name="province" id="{{ $id ?? '' }}_province">
        </div>

        <div class="form-group mb-4">
            <x-input-label for="regency">Kabupaten/Kota</x-input-label>
            <select name="address[regency_id]" id="{{ $id ?? '' }}_regency_id" class="w-full border rounded p-2" ></select>
            <input type="hidden" name="regency" id="{{ $id ?? '' }}_regency">
        </div>

        <div class="form-group mb-4">
            <x-input-label for="district">Kecamatan</x-input-label>
            <select name="address[district_id]" id="{{ $id ?? '' }}_district_id" class="w-full border rounded p-2" ></select>
            <input type="hidden" name="district" id="{{ $id ?? '' }}_district">
        </div>

        <div class="form-group mb-4">
            <x-input-label for="village">Desa/Kelurahan</x-input-label>
            <select name="address[village_id]" id="{{ $id ?? '' }}_village_id" class="w-full border rounded p-2" ></select>
            <input type="hidden" name="village" id="{{ $id ?? '' }}_village">
        </div>

        <div class="form-group mb-4">
            <x-input-label for="postal_code">Kode Pos</x-input-label>
            <x-text-input name="postal_code" id="{{ $id ?? '' }}_postal_code" class="w-full" placeholder="Kode Pos" ></x-text-input>
        </div>

        <div class="form-group mb-4">
            <x-input-label for="address_detail">Detail Alamat</x-input-label>
            <x-input-textarea name="address_detail" id="{{ $id ?? '' }}_address_detail" class="w-full" placeholder="Detail Alamat"></x-input-textarea>
        </div>
    </div>
</div>
