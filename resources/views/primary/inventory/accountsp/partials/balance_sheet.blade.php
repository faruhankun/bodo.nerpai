@php
    // dd($data->balance_sheet, $data->account);

    $data->balance_sheet['liabilities_equities'] = $data->balance_sheet['totalLiabilities'] + $data->balance_sheet['totalEquities'];
    $struct = [
        'assets' => [
            'name' => 'Aset',
            'list' => [1, 2, 3, 4, 5, 6, 7],
        ],
        'passive' => [
            'name' => 'Liabilitas dan Modal',
            'list' => [8, 9, 10, 11],
        ],
    ];

    $pnl = array(
        'type_id' => 11,
        'code' => 'pnl',
        'name' => 'Laba Rugi periode ini',
        'balance' => $data->balance_sheet['pnl']['laba_bersih'],
        'details' => [],
    );
    $data->balance_sheet['equities']->push($pnl);
    $data->balance_sheet['passive']->push($pnl);



    function format_number($number){
        return $number < 0 ? '(' . number_format(abs($number), 2) . ')' : number_format($number, 2);
    }
@endphp


<!-- Rangkuman Neraca   -->
<h1 class="text-3xl font-bold dark:text-white">Laporan Neraca</h1>
<br>

<x-table.table-table>
    <x-table.table-thead>
        <x-table.table-tr></x-table.table-tr>
    </x-table.table-thead>
    @foreach($struct as $key => $module)
        <x-table.table-tbody>
            <x-table.table-tr>
                <x-table.table-th class="text-left text-lg">{{ ucfirst($module['name']) }}</x-table.table-th>
                <x-table.table-th class="text-right text-lg"></x-table.table-th>
            </x-table.table-tr>



            @php
                $total_struct = 0;
                $acc_per_type = $data->balance_sheet[$key]->groupBy('type_id');
                $acc_per_type = $acc_per_type->sortKeys();
            @endphp

            @foreach($acc_per_type as $type_id => $accs)
                @php
                    $accs_type = ($accs->first())['type'];
                    $total_per_type = $accs->sum('balance');
                    $total_struct += $total_per_type;
                @endphp
                <x-table.table-tr>
                    <x-table.table-td class="text-md font-bold" style="padding-left: 30px;">{{ ucfirst($accs_type->name) }}</x-table.table-td>
                    <x-table.table-td></x-table.table-td>
                </x-table.table-tr>

                @foreach($accs as $acc)
                    <x-table.table-tr class="border-t border-gray-300 dark:border-gray-700 p-8" 
                        onclick="show_details({{ collect($acc['details']) }})">
                        <x-table.table-td style="padding-left: 45px;">
                            {{ $acc['code'] }}   {{ $acc['name'] }}
                        </x-table.table-td>
                        <x-table.table-td class="text-right" style="padding-right: 10px;">
                            <a onclick="show_details({{ collect($acc['details']) }})" class="text-primary">
                                {{ format_number($acc['balance']) }}
                            </a>
                        </x-table.table-td>
                    </x-table.table-tr>
                @endforeach


                <x-table.table-tr>
                    <x-table.table-td class="text-md font-bold" style="padding-left: 30px;">Total {{ ucfirst($accs_type->name) }}</x-table.table-td>
                    <x-table.table-td class="text-right text-md font-bold" style="padding-right: 10px;">{{ format_number($total_per_type) }}</x-table.table-td>
                </x-table.table-tr>


                <x-table.table-tr>
                    <x-table.table-td></x-table.table-td>
                </x-table.table-tr>
            @endforeach


            <x-table.table-tr>
                <x-table.table-td class="text-lg font-bold" style="padding-left: 30px;">Total {{ ucfirst($module['name']) }}</x-table.table-td>
                <x-table.table-td class="text-lg text-right font-bold" style="padding-right: 10px;">{{ format_number($total_struct) }}</x-table.table-td>
            </x-table.table-tr>

            <x-table.table-tr>
                <x-table.table-td></x-table.table-td>
            </x-table.table-tr>
        </x-table.table-tbody>
    @endforeach
</x-table.table-table>
<br>
