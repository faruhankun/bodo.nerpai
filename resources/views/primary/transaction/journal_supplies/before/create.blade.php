@php
    $space_id = session('space_id') ?? null;
    $layout = session('layout');
@endphp
<x-dynamic-component :component="'layouts.' . $layout">
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-white">
                    <h3 class="text-2xl dark:text-white font-bold">Add Journal</h3>
                    <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>

                    <form action="{{ route('journal_supplies.store') }}" method="POST" onsubmit="return validateForm()">
                        @csrf

                        @include('primary.transaction.journal_supplies.partials.dataform', ['form' => ['id' => 'Create Journal', 'mode' => 'create']])

                        <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>


                        <div class="m-4 flex justify-end space-x-4">
                            <a href="{{ route('journal_supplies.index') }}">
                                <x-secondary-button type="button">Cancel</x-secondary-button>
                            </a>
                            <x-primary-button>Create Journal</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-dynamic-component>
