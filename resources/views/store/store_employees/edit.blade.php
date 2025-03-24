<x-modal-edit trigger="Edit" title="Edit Store Employee : {{$employee->employee->companyuser->user->name}}">
    <form action="{{ route('store_employees.update', $employee->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Assign Role -->
        <div class="mb-4">
            <x-input-label for="store_role_id" class="block text-sm font-medium text-gray-700">Assign Role</x-input-label>
            <select name="store_role_id" id="store_role_id" class="w-full px-4 py-2 border rounded">
                @foreach ($store_roles as $role)
                    <option value="{{ $role->id }}" @if ($employee->store_role_id == $role->id) selected @endif>
                        {{ $role->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Actions -->
        <div class="flex justify-end space-x-4">
            <x-primary-button type="submit">Update Employee</x-primary-button>
            <button type="button" @click="isOpen = false"
                class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-700">Cancel</button>
        </div>
    </form>
</x-modal-edit>