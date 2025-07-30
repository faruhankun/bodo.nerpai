@props([
    'header' => 'Index',
    'model' => 'index',
    'table_id' => 'indexTable',
    'thead' => [],
    'tbody' => [],
])

@php
    $layout = $layout ?? session('layout');
@endphp
<x-dynamic-component :component="'layouts.' . $layout">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __($header) }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class=" sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-white">
                    <h3 class="text-lg font-bold dark:text-white">Manage {{ $header }}</h3>
                    <p class="text-sm dark:text-gray-200 mb-6">Create, edit, and manage your {{ $model }} listings.</p>
                    <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>

                    {{ $panel ?? '' }}

                    <div class="grid grid-cols-2 sm:grid-cols-2 gap-6 w-full mb-4">
                        <div class="form-group flex justify-left">
                            <div class="form-group flex gap-4">
                                {{ $buttons ?? '' }}
                            </div>
                        </div>
                        <div class="form-group flex justify-end">
                            <div class="form-group flex gap-4">
                                {{ $filters ?? '' }}
                            </div>
                        </div>
                    </div>

                    <x-table.table-table id="{{ $table_id }}" class="cell-border">
                        <x-table.table-thead>
                            <tr>
                                @foreach($thead as $th)
                                    <x-table.table-th>{{ $th }}</x-table.table-th>
                                @endforeach
                            </tr>
                        </x-table.table-thead>

                        <x-table.table-tbody>
                            @foreach($tbody as $tr)
                                <tr>
                                    @foreach($tr as $td)
                                        <x-table.table-td>{{ $td }}</x-table.table-td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </x-table.table-tbody>
                    </x-table.table-table>


                    <!-- Modals -->
                    <div
                        class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 mb-4">
                        <div class="flex flex-col md:flex-row items-center space-x-3">
                            {{ $modals ?? '' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-dynamic-component>
