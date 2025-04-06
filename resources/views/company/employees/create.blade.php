<x-company-layout>
    <div class="py-12">
        <div class="max-w-7xl my-10 mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                        Tambah Employee
                    </h1>
                    <form action="{{ route('employees.store') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <x-input-label for="user_id" class="block text-sm font-medium text-gray-700">Select User</x-input-label>
                            <select name="user_id" id="user_id" class="w-full px-4 py-2 border rounded">
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->user->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <x-input-label for="reg_date" class="block text-sm font-medium text-gray-700">Registration
                                Date</x-input-label>
                            <x-text-input type="date" name="reg_date" id="reg_date" class="w-full px-4 py-2 border rounded" value="{{ Date('Y-m-d'); }}"></x-text-input>
                        </div>

                        <div class="mb-4">
                            <x-input-label for="role_id" class="block text-sm font-medium text-gray-700">Assign Role</x-input-label>
                            <select name="role_id" id="role_id" class="w-full px-4 py-2 border rounded">
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->name }}</option>
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