<x-modal-create trigger="Add Setting" title="Add Setting">
    <form action="{{ route('company_settings.store') }}" method="POST">
        @csrf

        <div class="form-group mb-4">
            <x-input-label for="module" class="block text-sm font-medium text-gray-700">Module</x-input-label>
            <x-text-input name="module" id="module" class="w-full" placeholder="Module"></x-text-input>
        </div>

        <div class="form-group mb-4">
            <x-input-label for="key" class="block text-sm font-medium text-gray-700">Key (unique) format: [comp/st/wh].[name]</x-input-label>
            <x-text-input name="key" id="key" class="w-full" placeholder="Key" required></x-text-input>
        </div>

        <div class="form-group mb-4">
            <x-input-label for="value" class="block text-sm font-medium text-gray-700">Value</x-input-label>
            <x-text-input name="value" id="value" class="w-full" placeholder="Value" required></x-text-input>
        </div>

        <div class="form-group mb-4">
            <x-input-label for="source" class="block text-sm font-medium text-gray-700">Source</x-input-label>
            <div class="flex space-x-4">
                <x-text-input name="source_type" id="source_type" class="w-full" placeholder="source_type"></x-text-input>
                <x-text-input name="source_id" id="source_id" class="w-full" placeholder="source_id"></x-text-input>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex justify-end space-x-4 mt-4">
            <x-primary-button type="submit">Add Settings</x-primary-button>
            <button type="button" @click="isOpen = false"
                class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-700">Cancel</button>
        </div>
    </form>
</x-modal-create>
