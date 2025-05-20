@php
    $layout = session('layout');

    $date = request('date') ?? now()->format('Y-m-d');
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
                    <div class="flex justify-end border-solid border-2 dark:border-gray-700">
                        <form action="{{ route('supplies.summary') }}" method="GET">
                            <div class="grid grid-cols-2 sm:grid-cols-2">
                                <div class="form-group mr-4">
                                    <x-input.input-basic type="date" name="date" value="{{ $date }}" required></x-input.input-basic>
                                </div>
                                <div class="form-group mr-4">
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
                        
                        foreach($txs_per_space as $id => $txs){
                            $txs_per_date = $txs->groupBy('sent_time');

                            $space_supply = 0;

                            $space_supply_per_date = collect();

                            foreach($txs_per_date as $date => $txs){
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
                                        $per_date_change[$detail->model_type] += $detail->quantity * $detail->cost_per_unit;
                                    }
                                }
                                
                                $per_date['change'] += array_sum($per_date_change);
                                $per_date['balance'] += $per_date['change'];
                                $space_supply = $per_date['balance'];
                                $per_date = array_merge($per_date, $per_date_change);
                                $space_supply_per_date->put($date, $per_date);
                            }

                            $spaces_data->put($id, $space_supply_per_date);
                        }
                    @endphp



                    <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>
                    <!-- Rangkuman Mutasi Transaksi   -->
                    <table class="table-auto w-full mt-4">
                        <thead>
                            <tr>
                                <th class="px-4 py-2">Date</th>
                                <th class="px-4 py-2">Purchase</th>
                                <th class="px-4 py-2">Return</th>
                                <th class="px-4 py-2">Opname Found</th>
                                <th class="px-4 py-2">Move</th>
                                <th class="px-4 py-2">Opname Loss</th>
                                <th class="px-4 py-2">Sales</th>
                                <th class="px-4 py-2">Damage</th>
                                <th class="px-4 py-2">Undefined</th>
                                <th class="px-4 py-2">Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($spaces_data[$space_id] as $sent_time => $per_date)
                                <tr>
                                    <td class="border px-4 py-2">{{ $sent_time }}</td>
                                    <td class="border px-4 py-2">{{ number_format($per_date['PO'], 2) }}</td>
                                    <td class="border px-4 py-2">{{ number_format($per_date['RTR'], 2) }}</td>
                                    <td class="border px-4 py-2">{{ number_format($per_date['FND'], 2) }}</td>
                                    <td class="border px-4 py-2">{{ number_format($per_date['MV'], 2) }}</td>
                                    <td class="border px-4 py-2">{{ number_format($per_date['LOSS'], 2) }}</td>
                                    <td class="border px-4 py-2">{{ number_format($per_date['SO'], 2) }}</td>
                                    <td class="border px-4 py-2">{{ number_format($per_date['DMG'], 2) }}</td>
                                    <td class="border px-4 py-2">{{ number_format($per_date['UNDF'], 2) }}</td>
                                    <td class="border px-4 py-2">{{ number_format($per_date['balance'], 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>




                    
                    <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>
                    <!-- Rangkuman Space-->
                    Rangkuman Space
                    <div class="grid grid-cols-2 md:grid-cols-2 gap-6">
                        <table class="table-auto w-full mt-4">
                            <thead>
                                <tr>
                                    <th class="px-4 py-2">Id</th>
                                    <th class="px-4 py-2">Space Name</th>
                                    <th class="px-4 py-2">Inventory Count</th>
                                    <th class="px-4 py-2">Inventory Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($spaces_data as $id => $data)
                                    <tr>
                                        <td class="border px-4 py-2">{{ $id }}</td>
                                        <td class="border px-4 py-2">{{ $spaces_per_id[$id][0]->name }}</td>
                                        <td class="border px-4 py-2">{{ 0 }}</td>
                                        <td class="border px-4 py-2">{{ number_format($data->sum('change'), 2) }}</td>
                                    </tr>
                                @endforeach
                                
                                <tr>
                                    <td class="border px-4 py-2">Total</td>
                                    <td class="border px-4 py-2">{{ $spaces_data->count() }} Spaces</td>
                                    <td class="border px-4 py-2">{{ 0 }}</td>
                                    <td class="border px-4 py-2">{{ number_format($spaces_data->sum(function ($data) { return $data->sum('change'); }), 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
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
