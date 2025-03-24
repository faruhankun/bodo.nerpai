<x-lobby-layout>
    <div class="py-12">
        <div class="max-w-7xl my-10 mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                        Edit User: {{ $user->name }}
                    </h1>
                    <form action="{{ route('users.update', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="flex flex-col gap-6">
                            <div>
                                <x-input-label for="name" :value="__('User Name')" />
                                <x-input-input type="text" name="name" x-model="name" required
                                    value="{{ $user->name }}" />
                            </div>
    
                            <div>
                                <x-input-label for="status" :value="__('Status')" />
                                <x-input-select name="status" x-model="status" required>
                                    <x-select-option value="Active" :selected="$user->status === 'Active'">Active</x-select-option>
                                    <x-select-option value="Inactive" :selected="$user->status === 'Inactive'">Inactive</x-select-option>
                                </x-input-select>
                            </div>
                            
                            <!-- Assign Role -->
                            <div class="mb-4">
                                <x-input-label for="role_id" class="block text-sm font-medium text-gray-700">Assign Role</x-input-label>
                                <select name="role_id" id="role_id" class="w-full px-4 py-2 border rounded">
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}" @if ($user->role_id == $role->id) selected @endif>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="flex gap-3 justify-end mt-4">
                            <a href="{{ route('users.index') }}">
                                <x-secondary-button type="button">
                                    Cancel
                                </x-button>
                            </a>
                            <x-primary-button type="submit">Update user</x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-lobby-layout>
