<x-store-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Store') }}
        </h2>
    </x-slot>

    <div class="py-16 ">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white border-b border-gray-500 dark:bg-gray-800 dark:border-gray-700 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-white">
                    {{ __("Anda masuk ke halaman toko: ") }} {{ session('company_store_name') }} 
                </div>
            </div>
        </div>
    </div>
</x-store-layout>
