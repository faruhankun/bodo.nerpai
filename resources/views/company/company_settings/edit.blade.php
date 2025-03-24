<!-- Modal Edit Setting -->
<div x-cloak x-data="{ open: false }" @edit-setting.window="open = true">
    <div x-show="open" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center">
        <div class="bg-white p-6 rounded-lg w-1/3">
            <!-- Modal Header -->
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white">Edit Setting</h3>
                <button @click="open = false" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                    &times;
                </button>
            </div>

            <div class="modal-body">
                <form id="editSettingForm" method="POST">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="id" id="edit_id">

                    <div class="form-group mb-4">
                        <x-input-label for="module" class="block text-sm font-medium text-gray-700">Module</x-input-label>
                        <x-text-input name="module" id="edit_module" class="w-full" placeholder="Module"></x-text-input>
                    </div>

                    <div class="form-group mb-4">
                        <x-input-label for="key" class="block text-sm font-medium text-gray-700">Key (unique) format: [comp/st/wh].[name]</x-input-label>
                        <x-text-input name="key" id="edit_key" class="w-full" placeholder="Key" required></x-text-input>
                    </div>

                    <div class="form-group mb-4">
                        <x-input-label for="value" class="block text-sm font-medium text-gray-700">Value</x-input-label>
                        <x-text-input name="value" id="edit_value" class="w-full" placeholder="Value" required></x-text-input>
                    </div>

                    <div class="form-group mb-4">
                        <x-input-label for="source" class="block text-sm font-medium text-gray-700">Source</x-input-label>
                        <div class="flex space-x-4">
                            <x-text-input name="source_type" id="edit_source_type" class="w-full" placeholder="source_type"></x-text-input>
                            <x-text-input name="source_id" id="edit_source_id" class="w-full" placeholder="source_id"></x-text-input>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-end space-x-4 mt-4">
                        <x-primary-button type="submit">Update Settings</x-primary-button>
                        <button type="button" @click="open = false"
                            class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-700">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>