<x-modal-edit trigger="Edit Account" title="Edit Account">
    <form action="{{ route('accounts.update', $data->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group mb-4">
            <x-input-label for="name" class="block text-sm font-medium text-gray-700">Name</x-input-label>
            <x-text-input name="name" id="name" class="w-full" placeholder="Account Name"
                :value="$data->name"></x-text-input>
        </div>
        <div class="form-group mb-4">
            <x-input-label for="type_id" class="block text-sm font-medium text-gray-700">Account Type</x-input-label>
            <select name="type_id" id="type_id"
                class="w-full px-4 py-2 border rounded dark:bg-gray-700 dark:text-white"
                @change="document.getElementById('edit-basecode{{ $data->id }}').value = event.target.selectedOptions[0].dataset.basecode">
                @foreach ($account_types as $account_type)
                    <option id="option-{{ $account_type->id }}-for-{{ $data->id }}"
                        value="{{ $account_type->id }}" data-basecode="{{ $account_type->basecode }}"
                        @if ($data->type_id == $account_type->id) selected @endif>
                        {{ $account_type->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group mb-4">
            <x-input-label for="code" class="block text-sm font-medium text-gray-700">Code</x-input-label>
            <div class="flex gap-2">
                <input
                    class="w-20 flex items-center justify-center bg-gray-50 border-indigo-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-300"
                    id="edit-basecode{{ $data->id }}" name="basecode" readonly
                    value="{{ $account_types->firstWhere('id', $data->type_id)->basecode }}"></input>
                <x-text-input class="flex-1" name="code" id="code" class="w-full"
                    value="{{ substr($data->code, strlen($account_types->firstWhere('id', $data->type_id)->basecode)) }}"
                    placeholder="Suffix"></x-text-input>
            </div>
        </div>

        <div class="form-group mb-4">
            <x-input-label for="status-for-{{ $data->id }}"
                class="block text-sm font-medium text-gray-700">Status</x-input-label>
            <x-input-select name="status" class="mt-1 block w-full" required>
                <option value="Active" {{ $data->status == 'Active' ? 'selected' : '' }}>Active</option>
                <option value="Inactive" @selected($data->status == 'Inactive')>Inactive</option>
            </x-input-select>
        </div>

        <div class="form-group mb-4">
            <x-input-label for="parent_id" class="block text-sm font-medium text-gray-700">Parent
                Account</x-input-label>
            <select name="parent_id" id="parent_id"
                class="w-full px-4 py-2 border rounded dark:bg-gray-700 dark:text-white">
                <option value=""><-- Select Parent Account --></option>
                @foreach ($accounts as $account)
                    <option value="{{ $account->id }}" @if ($data->parent_id == $account->id) selected @endif>
                        {{ $account->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group mb-4">
            <x-input-label for="notes" class="block text-sm font-medium text-gray-700">Notes</x-input-label>
            <x-input-textarea name="notes" id="notes" class="w-full" placeholder="Optional notes"
                value="{{ $data->notes }}"></x-input-textarea>
        </div>

        <!-- Actions -->
        <div class="flex justify-end space-x-4 mt-4">
            <x-primary-button type="submit">Edit Account</x-primary-button>
            <button type="button" @click="isOpen = false"
                class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-700">Cancel</button>
        </div>
    </form>
</x-modal-edit>
