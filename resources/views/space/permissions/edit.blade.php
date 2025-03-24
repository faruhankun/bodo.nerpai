<x-lobby-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Permission') }}
        </h2>
    </x-slot>

    <div class="py-16">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white bg-white dark:bg-gray-800 dark:border-gray-700 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-white">
                    <h3 class="text-lg fdark:text-white font-bold">Edit Permission</h3>
                    <p class="text-sm dark:text-gray-200 mb-4">{{ $permission->name }}</p>
                    <form action="{{ route('permissions.update', $permission->id) }}" method="POST" class="mt-4">
                        @csrf
                        @method('PUT')
                        <div class="mb-4">
                            <x-input-label for="name" class="block">Nama Permission</x-input-label>
                            <x-text-input type="text" name="name" id="name" value="{{ old('name', $permission->name) }}" class="mt-1 block w-full px-4 py-2" required></x-text-input>
                        </div>

                        <x-primary-button>Update</x-primary-button>
                        <a href="{{ route('permissions.index') }}">
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
