@php
    $layout = session('layout');

    $date = request('date') ?? now()->format('Y-m-d');
@endphp
<x-dynamic-component :component="'layouts.' . $layout">
    <div class="py-12">
        <div class="max-w-7xl my-10 mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-white">
                    <h3 class="text-2xl font-bold text-2xl text-xl dark:text-white">Rangkuman Items</h3>
                    <div class="flex justify-end">
                        <form action="{{ route('summaries.show', 'items.summary') }}" method="GET">
                            <div class="form-group mr-4">
                                <x-input.input-basic type="date" name="date" value="{{ $date }}" required></x-crud.input-basic>
                            </div>
                            <div class="form-group mr-4">
                                <x-primary-button class="ml-4">Filter</x-primary-button>
                            </div>
                        </form>
                    </div>
                    <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>





                    @php 
                        $data = [];
                        foreach($items as $item){
                            foreach($item->inventories as $inventory){
                                if(!isset($data[$inventory->space_id])){
                                    $data[$inventory->space_id] = [
                                        'space_name' => $inventory->space->name,
                                        'inventory_count' => 0,
                                        'cost_total' => 0,
                                    ];
                                }

                                $data[$inventory->space_id]['inventory_count']++;
                                $data[$inventory->space_id]['cost_total'] += $inventory->cost_per_unit * $inventory->balance;
                            }
                        }

                        $data = collect($data);
                    @endphp
                    <!-- Rangkuman -->
                    Rangkuman Total
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-div.box-show title="Total Items">
                            <table class="table-auto w-full mt-4">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-2">Keterangan</th>
                                        <th class="px-4 py-2">Nilai</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="border px-4 py-2">Inventory Count</td>
                                        <td class="border px-4 py-2">{{ $data->sum('inventory_count') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="border px-4 py-2">Inventory Value</td>
                                        <td class="border px-4 py-2">{{ number_format($data->sum('cost_total'), 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="border px-4 py-2">Total</td>
                                        <td class="border px-4 py-2">{{ $items->count() }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </x-div.box-show>
                    </div>


                    <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>
                    <!-- Rangkuman Space-->
                    Rangkuman Space
                    <div class="grid grid-cols-3 md:grid-cols-3 gap-6">
                        @foreach($data as $space_id => $space_data)
                            <x-div.box-show title="Space: {{ $space_data['space_name'] }}">
                                <table class="table-auto w-full mt-4">
                                    <thead>
                                        <tr>
                                            <th class="px-4 py-2">Keterangan</th>
                                            <th class="px-4 py-2">Nilai</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="border px-4 py-2">Inventory Count</td>
                                            <td class="border px-4 py-2">{{ $space_data['inventory_count'] }}</td>
                                        </tr>
                                        <tr>
                                            <td class="border px-4 py-2">Inventory Value</td>
                                            <td class="border px-4 py-2">{{ number_format($space_data['cost_total'], 2) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </x-div.box-show>
                        @endforeach
                    </div>
                    




                    <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>
                    <!-- Back Button -->
                    <div class="flex mt-8">
                        <x-secondary-button>
                            <a href="{{ route('summaries.index') }}">Back to Report</a>
                        </x-secondary-button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</x-dynamic-component>
