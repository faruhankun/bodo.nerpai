<x-company-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Employee') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl my-10 mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                        Edit Employee
                    </h1>

                    <form action="{{ route('employees.update', $employee->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Registration Date -->
                        <div class="mb-4">
                            <x-input-label for="reg_date" class="block text-sm font-medium text-gray-700">Registration
                                Date</x-input-label>
                            <x-text-input type="date" name="reg_date" id="reg_date"
                                value="{{ $employee->reg_date->format('Y-m-d') }}"
                                class="w-full px-4 py-2 border rounded" required></x-text-input>
                        </div>

                        <!-- Out Date -->
                        <div class="mb-4">
                            <x-input-label for="out_date" class="block text-sm font-medium text-gray-700">Out Date</x-input-label >
                            <x-text-input type="date" name="out_date" id="out_date"
                                value="{{ $employee->out_date ? $employee->out_date->format('Y-m-d') : '' }}"
                                class="w-full px-4 py-2 border rounded"></x-text-input>
                            <p class="text-sm text-gray-500">Leave this empty if the employee is still active.</p>
                        </div>

                        <!-- Assign Role -->
                        <div class="mb-4">
                            <x-input-label for="role_id" class="block text-sm font-medium text-gray-700">Assign Role</x-input-label>
                            <select name="role_id" id="role_id" class="w-full px-4 py-2 border rounded">
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}" @if ($employee->role_id == $role->id) selected @endif>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mt-6">
                            <x-primary-button>Simpan</x-primary-button>
                            <a href="{{ route('employees.index') }}">
                                <x-secondary-button type="button">
                                    Cancel
                                </x-button>
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>
</x-company-layout>