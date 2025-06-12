@php
    // dd($data->balance_sheet, $data->account);

    $data->balance_sheet['liabilities_equities'] = $data->balance_sheet['totalLiabilities'] + $data->balance_sheet['totalEquities'];
    $struct = [
        'assets' => 'totalAssets',
        'liabilities' => 'totalLiabilities',
        'equities' => 'totalEquities',
        'liabilities_equities' => null,
    ];

    $pnl = array(
        'code' => 'pnl',
        'name' => 'Laba Rugi periode ini',
        'balance' => $data->balance_sheet['pnl']['laba_bersih'],
        'details' => [],
    );
    $data->balance_sheet['equities']->push($pnl);
@endphp


<!-- Rangkuman Neraca   -->
<h3 class="text-2xl font-bold text-2xl text-xl dark:text-white">Laporan Neraca</h3>
<br>
<x-table.table-table>
    @foreach($struct as $key => $value)
        <x-table.table-thead>
            <tr>
                <x-table.table-th></x-table.table-th>
                <x-table.table-th colspan="2"></x-table.table-th>
                <x-table.table-th class="text-right text-lg">{{ ucfirst($key) }}</x-table.table-th>
            </tr>
        </x-table.table-thead>
        <x-table.table-tbody>
            @if($value)
                @foreach($data->balance_sheet[$key] as $acc)
                    <tr>
                        <x-table.table-td class="text-left ml-16">
                            {{ $acc['code'] }} - {{ $acc['name'] }}
                        </x-table.table-td>
                        <x-table.table-td></x-table.table-td>
                        <x-table.table-td></x-table.table-td>
                        <x-table.table-td class="text-right mr-16">
                            <a onclick="show_details({{ collect($acc['details']) }})" class="text-blue-600 hover:text-blue-800">
                                Rp{{ number_format($acc['balance'], 2) }}
                            </a>
                        </x-table.table-td>
                    </tr>
                @endforeach
                <tr>
                    <x-table.table-th class="text-left">{{ ucfirst($value) }}</x-table.table-th>
                    <x-table.table-th></x-table.table-th>
                    <x-table.table-th></x-table.table-th>
                    <x-table.table-th class="text-right">
                        Rp{{ number_format($data->balance_sheet[$value] ?? 0, 2) }}
                    </x-table.table-th>
                </tr>
            @else
                <tr>
                    <x-table.table-th class="text-left">{{ ucfirst($key) }}</x-table.table-th>
                    <x-table.table-th></x-table.table-th>
                    <x-table.table-th></x-table.table-th>
                    <x-table.table-th class="text-right">
                        Rp{{ number_format($data->balance_sheet[$key] ?? 0, 2) }}
                    </x-table.table-th>
                </tr>
            @endif
            <tr>
                <x-table.table-td class="text-right h-8" colspan="3"></x-table.table-td>
            </tr>
        </x-table.table-tbody>
    @endforeach
</x-table.table-table>
<br>
