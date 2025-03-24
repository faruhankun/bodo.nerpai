<x-modal-edit trigger="Edit" title="Edit Company">
    <form action="{{ route('companies.update', $Tenant->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- companies Name -->
        <div class="form-group">
            <x-input-label for="name">Companies Name</x-input-label>
            <x-text-input type="text" id="name" name="name" class="w-full" value="{{ $Tenant->name }}" required />
        </div>

        <!-- Location -->
        <div class="form-group">
            <x-input-label for="location">Address</x-input-label>
            <x-text-input type="text" id="location" name="location" class="w-full" value="{{ $Tenant->address }}" />
        </div>

        <!-- Actions -->
        <div class="flex justify-end space-x-4">
            <x-primary-button type="submit">Update Company</x-primary-button>
            <button type="button" @click="isOpen = false"
                class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-700">Cancel</button>
        </div>
    </form>
</x-modal-edit>