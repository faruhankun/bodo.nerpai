@php
    $layout = session('layout');

    $start_date = request('start_date') ?? now()->startOfMonth()->format('Y-m-d');
    $end_date = request('end_date') ?? now()->format('Y-m-d');
    $summary_type = request('summary_type') ?? null;

    $space_id = session('space_id') ?? null;
    if(is_null($space_id)){
        abort(403);
    }
@endphp
<x-dynamic-component :component="'layouts.' . $layout">
    <div class="py-12">
        <div class="max-w-7xl my-10 mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-white">
                    <h3 class="text-2xl font-bold text-2xl text-xl dark:text-white">Rangkuman Items</h3>
                    <div class="flex justify-end items-center m-4 border-solid border-2 dark:border-gray-700">
                        <form action="{{ route('supplies.summary') }}" method="GET">
                            <div class="grid grid-cols-4">
                                <x-div.box-input label="summary_type" class="m-4">
                                    <select name="summary_type" id="summary_type">
                                        @foreach($data->summary_types as $key => $value)
                                            <option value="{{ $key }}" {{ $key == $summary_type ? 'selected' : '' }}>{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </x-div.box-input>
                                <x-div.box-input label="Start Date" class="m-4">
                                    <x-input.input-basic type="date" name="start_date" value="{{ $start_date }}"></x-input.input-basic>
                                </x-div.box-input>
                                <x-div.box-input label="End Date" class="m-4">
                                    <x-input.input-basic type="date" name="end_date" value="{{ $end_date }}" required></x-input.input-basic>
                                </x-div.box-input>
                                <div class="form-group m-4">
                                    <x-primary-button class="ml-4">Filter</x-primary-button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>
                    @php
                        // Transaction;
                        $spaces_per_id = $spaces->groupBy('id');
                        $txs_per_space = $txs->groupBy('space_id');
                        $spaces_data = collect();
                        
                        $items_data = collect();

                        foreach($txs_per_space as $id => $txs){
                            $txs_per_date = $txs->groupBy('sent_time');

                            $space_supply = 0;

                            $space_supply_per_date = collect();
                            $items_per_space = collect();
                            $items = [];

                            foreach($txs_per_date as $end_date => $txs){
                                $per_date_change = [
                                    'PO' => 0,
                                    'SO' => 0,
                                    'FND' => 0,
                                    'LOSS' => 0,
                                    'RTR' => 0,
                                    'DMG' => 0,
                                    'MV' => 0,
                                    'UNDF' => 0,
                                ];
                                
                                $per_date = [
                                    'change' => 0,
                                    'balance' => $space_supply,
                                ];

                                foreach($txs as $tx){
                                    foreach($tx->details as $detail){
                                        // tx
                                        $per_date_change[$detail->model_type] += $detail->quantity * $detail->cost_per_unit;

                                        // item
                                        $item = $data->items_list[$detail->detail->item_id] ?? null;
                                        if(!isset($items[$item->id])){
                                            $items[$item->id] = [
                                                'item' => $item, 
                                                'in' => 0, 
                                                'out' => 0,
                                                'in_subtotal' => 0,
                                                'out_subtotal' => 0,
                                                'omzet' => 0,
                                                'margin' => 0,
                                            ];
                                        }

                                        $items[$item->id]['in'] += $detail->debit;
                                        $items[$item->id]['out'] += $detail->credit;
                                        $items[$item->id]['in_subtotal'] += $detail->debit * $detail->cost_per_unit;
                                        $items[$item->id]['out_subtotal'] += $detail->credit * $detail->cost_per_unit;
                                        $items[$item->id]['omzet'] += $detail->credit * $item->price;
                                        $items[$item->id]['margin'] += $items[$item->id]['omzet'] - $items[$item->id]['out_subtotal'];
                                    }
                                }

                                $per_date['change'] += array_sum($per_date_change);
                                $per_date['balance'] += $per_date['change'];
                                $space_supply = $per_date['balance'];

                                $per_date = array_merge($per_date, $per_date_change);
                                $space_supply_per_date->put($end_date, $per_date);
                            }

                            $spaces_data->put($id, $space_supply_per_date);
                            $items_data->put($id, $items);

                        }
                    @endphp

                    @switch($summary_type)
                        @case('txs')
                            @include('primary.inventory.supplies.partials.summary-txs')
                        @break
                        @case('items')
                            @include('primary.inventory.supplies.partials.summary-items')
                        @break
                    @endswitch

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
