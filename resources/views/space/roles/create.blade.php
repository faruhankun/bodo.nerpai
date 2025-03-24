<x-lobby-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Role') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white bg-white dark:bg-gray-800 dark:border-gray-700 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-white">
                <h3 class="text-lg dark:text-white font-bold">Tambah Role Access</h3>
                <p class="text-sm dark:text-gray-200 mb-4">Atur role dan permission setiap fitur</p>

                    <form action="{{ route('roles.store') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <x-input-label for="name" class="block">Nama Role</x-input-label>
                            <x-text-input type="text" name="name" id="name" class="mt-1 block w-full px-4 py-2" required placeholder="Tambah Role"></x-text-input>
                        </div>

                        <div class="mb-4">
                            <h4 class="text-md font-semibold dark:text-gray-200 mb-3">Permissions</h4>
                            @foreach ($groupedPermissions as $group => $permissions)
                                <div class="mb-4 p-2 py-3 bg-gray-200 dark:text-gray-200 dark:bg-gray-900 rounded rounded-md shadow-md">
                                    <h5 class="text-md font-bold text-gray-600 dark:text-gray-300 uppercase font-bold">{{ $group }}</h5>
                                    <div class="grid grid-cols-2 gap-2 mt-2">
                                        @foreach ($permissions as $permission)
                                            <label class="flex items-center space-x-2">
                                                <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" class="form-checkbox text-blue-500 rounded">
                                                <span>{{ $permission->name }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <x-primary-button>Simpan</x-primary-button>
                        <a href="{{ route('roles.index') }}">
                            <x-secondary-button type="button">
                                Cancel
                            </x-button>
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-lobby-layout>
