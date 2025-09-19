<x-crud.modal-create title="Buat Penawaran" trigger="Buat Penawaran">
    <form action="{{ route('quotes.store') }}" method="POST" class="mt-4">
        @csrf
        @include('primary.transaction.quotes.partials.dataform', ['form' => ['id' => 'Buat Penawaran', 'mode' => 'create']])

        
        <!-- Actions -->
        <div class="flex justify-end space-x-4 mt-4">
            <x-primary-button type="submit">Buat Penawaran</x-primary-button>
            <button type="button" @click="isOpen = false"
                class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-700">Cancel</button>
        </div>
    </form>
</x-crud.modal-create>