<x-company-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Role') }}
        </h2>
    </x-slot>

    <div class="py-16">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white border-b border-gray-500 dark:bg-gray-800 dark:border-gray-700 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-white">
                    <h3 class="text-lg font-medium leading-tight">Role: {{ $role->name }}</h3>

                    <h4 class="mt-4 font-medium">Permissions:</h4>
                    <ul class="mt-2">
                        @foreach($role->permissions as $permission)
                            <li>{{ $permission->name }}</li>
                        @endforeach
                    </ul>

                    <a href="{{ route('roles.index') }}">
                        <x-secondary-button type="button">
                            Back to List
                        </x-button>
                    </a>
                    <a href="{{ route('roles.edit', $role->id) }}" class="mt-4 inline-block text-blue-500 hover:text-blue-700">Edit Role</a>
                </div>
            </div>
        </div>
    </div>
</x-company-layout>
