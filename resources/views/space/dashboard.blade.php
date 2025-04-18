@php
    $layout = $layout ?? session('layout');
@endphp
<x-dynamic-component :component="'layouts.space.' . $layout">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-16 ">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white border-b border-gray-500 dark:bg-gray-800 dark:border-gray-700 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-white">
                    <p class="text-3xl dark:text-gray-200 mb-6">
                        {{ __("Welcome in Space: =>") }} {{ session('space_name') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-dynamic-component>
