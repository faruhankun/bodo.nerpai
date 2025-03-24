<x-modal-create trigger="Add Employees" title="Add Employees To Store">
    <form action="{{ route('store_employees.store') }}" method="POST" class="">
        @csrf

        <!-- Employee to Add -->
        <div class="form-group mb-4">
            <x-input-label for="employee_id" class="block text-sm font-medium text-gray-700">Select Employee</x-input-label>
            <select name="employee_id" id="employee_id" class="w-full px-4 py-2 border rounded">
                @foreach ($employees_without_store as $employee)
                    <option value="{{ $employee->id }}">{{ $employee->companyuser->user->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Actions -->
        <div class="flex justify-end space-x-4 mt-4">
            <x-primary-button type="submit">Add Employee</x-primary-button>
            <button type="button" @click="isOpen = false" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-700">Cancel</button>
        </div>
    </form>
</x-modal-create>
