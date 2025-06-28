@php
    $result = $result ?? [];
    $layout = session('layout');
@endphp

<x-dynamic-component :component="'layouts.' . $layout">
    <div class="py-12">
        <div class=" sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-bold dark:text-white">Migrate Client</h3>
                
                    <form action="{{ route('migrate_client.store') }}" method="POST">
                        @csrf
                        <x-input-label for="code">Code</x-input-label>
                        <x-input-select name="code" id="code">
                            <x-select-option value="haebot_core">haebot_core</x-select-option>
                            <x-select-option value="haebot_1">haebot_1</x-select-option>
                        </x-input-select>

                        <x-input-label for="query">Query</x-input-label>
                        <x-input-select name="query" id="query">
                            <x-select-option value="supplier">supplier</x-select-option>
                            <x-select-option value="product">product</x-select-option>
                            <x-select-option value="customer">customer</x-select-option>
                            <x-select-option value="order">order</x-select-option>
                            <x-select-option value="shipment">shipment</x-select-option>
                        </x-input-select>

                        <x-primary-button type="submit" class="btn btn-primary">Migrate</x-primary-button>
                    </form>
                    
                    <div class="mt-4">
                        <div id="result">
                            @if($result)
                                @if($result && is_array($result))
                                    @foreach($result as $key => $value)
                                        {{ $key }}: {{ json_encode($value) }}<br>
                                    @endforeach
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


</x-dynamic-component>
