@php
    $layout = session('layout') ?? 'lobby';
    $space_role = session('space_role') ?? null;


    $data = $journal;

    $tx_related = $data->children ?? [];
    if($data->input){
        $tx_related[] = $data->input;
    }
@endphp

<x-dynamic-component :component="'layouts.' . $layout">
    <div class="py-12">
        <div class=" sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-white">
                    <h1 class="text-2xl font-bold mb-6">Journal Entry: {{ $data->number }}</h1>
                    <div class="mb-3 mt-1 flex-grow border-t border-gray-300 dark:border-gray-700"></div>

                    @include('primary.transaction.journal_supplies.partials.datashow')

                    <!-- Action Section -->
                    <div class="flex justify-end space-x-4">
                        <x-secondary-button>
                            <a href="{{ route('journal_accounts.index') }}">Back to List</a>
                        </x-secondary-button>

                        @if($space_role == 'admin' || $space_role == 'owner')
                            <a target="_blank" href="{{ route('journal_supplies.edit', $data->id) }}">
                                <x-primary-button type="button">Edit Journal</x-primary-button>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-dynamic-component>
