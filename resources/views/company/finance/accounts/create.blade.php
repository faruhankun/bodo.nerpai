<x-modal-create trigger="Add Account" title="Add Account">
    <form action="{{ route('accounts.store') }}" method="POST">
        @csrf

        <div class="form-group mb-4">
            <x-input-label for="name" class="block text-sm font-medium text-gray-700">Name</x-input-label>
            <x-text-input name="name" id="name" class="w-full" placeholder="Account Name"></x-text-input>
        </div>

        <div class="form-group mb-4">
            <x-input-label for="type_id" class="block text-sm font-medium text-gray-700">Account Type</x-input-label>
            <select name="type_id" id="type_id"
                class="w-full px-4 py-2 border rounded dark:bg-gray-700 dark:text-white"
                @change="document.getElementById('basecode').value = event.target.selectedOptions[0].dataset.basecode">
                @foreach ($account_types as $account_type)
                    <option value="{{ $account_type->id }}" data-basecode="{{ $account_type->basecode }}">
                        {{ $account_type->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group mb-4">
            <x-input-label for="code" class="block text-sm font-medium text-gray-700">Code</x-input-label>
            <div class="flex gap-2">
                <input
                    class="w-20 flex items-center justify-center bg-gray-50 border-indigo-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-300"
                    id="basecode" name="basecode" readonly></input>
                <x-text-input class="flex-1" name="code" id="code" class="w-full"
                    placeholder="Suffix"></x-text-input>
            </div>
        </div>

        <div class="form-group mb-4">
            <x-input-label for="status" class="block text-sm font-medium text-gray-700">Status</x-input-label>
            <select name="status" id="status"
                class="w-full px-4 py-2 border rounded dark:bg-gray-700 dark:text-white">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>

        <div class="form-group mb-4">
            <x-input-label for="parent_id" class="block text-sm font-medium text-gray-700">Parent
                Account</x-input-label>
            <select name="parent_id" id="parent_id"
                class="w-full px-4 py-2 border rounded dark:bg-gray-700 dark:text-white">
                <option value=""><-- Select Parent Account --></option>
                @foreach ($accounts as $account)
                    <option value="{{ $account->id }}">{{ $account->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group mb-4">
            <x-input-label for="notes" class="block text-sm font-medium text-gray-700">Notes</x-input-label>
            <x-input-textarea name="notes" id="notes" class="w-full"
                placeholder="Optional notes"></x-input-textarea>
        </div>

        <!-- Actions -->
        <div class="flex justify-end space-x-4 mt-4">
            <x-primary-button type="submit">Add Account</x-primary-button>
            <button type="button" @click="isOpen = false"
                class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-700">Cancel</button>
        </div>
    </form>
</x-modal-create>
