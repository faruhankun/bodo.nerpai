<x-modal-create trigger="Create New Store" title="Create New Store">
    <form action="{{ route('stores.store') }}" method="POST" class="">
        @csrf

        <!-- Store Code -->
        <div class="form-group mb-4">
            <x-input-label for="id">Store Code (Kode Toko)</x-input-label>
            <x-text-input type="text" id="code" name="code" class="w-full" required placeholder="Masukkan kode toko" />
        </div>

        <!-- Store Name -->
        <div class="form-group mb-4">
            <x-input-label for="name">Store Name</x-input-label>
            <x-text-input type="text" id="name" name="name" class="w-full" required placeholder="Masukkan nama toko" />
        </div>

        <!-- Address -->
        <!-- <div class="form-group mb-4">
            <x-input-label for="address">Alamat</x-input-label>
            <x-text-input type="text" id="address" name="address" class="w-full" placeholder="Alamat toko ?"/>
        </div> -->

        <!-- Actions -->
        <div class="flex justify-end space-x-4 mt-4">
            <x-primary-button type="submit">Create Store</x-primary-button>
            <button type="button" @click="isOpen = false" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-700">Cancel</button>
        </div>
    </form>
</x-modal-create>
