<x-crud.modal-create title="Add Journal" trigger="Add Journal">
    <form action="{{ route('journal_accounts.store') }}" method="POST" class="mt-4">
        @csrf
        @include('primary.transaction.journal_accounts.partials.dataform', ['form' => ['id' => 'Add Journal', 'mode' => 'create']])

        
        <!-- Actions -->
        <div class="flex justify-end space-x-4 mt-4">
            <x-primary-button type="submit">Add Journal</x-primary-button>
            <button type="button" @click="isOpen = false"
                class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-700">Cancel</button>
        </div>
    </form>
</x-crud.modal-create>