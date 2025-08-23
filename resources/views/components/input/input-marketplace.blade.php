@props(['id' => ''])

<div>
    <span class="block text-lg text-gray-700 dark:text-gray-300">Marketplace Username</span>
    <div class="grid grid-cols-3 sm:grid-cols-3 gap-6">
        <div class="form-group mb-4">
            <x-input-label for="shopee_username">Shopee Username</x-input-label>
            <x-text-input name="shopee_username" id="{{ $id ?? '' }}_shopee_username" class="w-full" placeholder="Shopee Username" ></x-text-input>
        </div>


        <div class="form-group mb-4">
            <x-input-label for="tokopedia_username">Tokopedia Username</x-input-label>
            <x-text-input name="tokopedia_username" id="{{ $id ?? '' }}_tokopedia_username" class="w-full" placeholder="Tokopedia Username" ></x-text-input>
        </div>

        
        <div class="form-group mb-4">
            <x-input-label for="whatsapp_number">No Whatsapp</x-input-label>
            <x-text-input name="whatsapp_number" id="{{ $id ?? '' }}_whatsapp_number" class="w-full" placeholder="No Whatsapp" ></x-text-input>
        </div>
    </div>
</div>
