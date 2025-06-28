<x-lobby-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Permission') }}
        </h2>
    </x-slot>

    <div class="py-16">
        <div class=" sm:px-6 lg:px-8">
            <div class="bg-white border-b border-gray-500 dark:bg-gray-800 dark:border-gray-700 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-white">
                    <h3 class="text-lg font-medium leading-tight">Buat Permission Baru</h3>

                    <form action="{{ route('permissions.store') }}" method="POST" class="mt-4">
                        @csrf
                        <div class="mb-4">
                            <x-input-label for="name" >Nama Permission</x-input-label>
                            <x-text-input type="text" name="name" id="name" class="mt-1 block w-full" required></x-text-input>
                        </div>

                        <x-primary-button>Simpan</x-primary-button>
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
