@php
    // dd($data->profit_loss, $data->account);

    $struct = [
        'pendapatan' => 'total_pendapatan',
        'beban_pokok' => 'total_beban_pokok',
        'laba_kotor' => null,
        'biaya_operasional' => 'total_biaya_operasional',
        'laba_operasional' => null,
        'pendapatan_lainnya' => 'total_pendapatan_lainnya',
        'beban_lainnya' => 'total_beban_lainnya',
        'laba_bersih' => null,
    ];
@endphp


<!-- Rangkuman Laba Rugi   -->
<h3 class="text-2xl font-bold text-2xl text-xl dark:text-white">Laporan Laba Rugi</h3>
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
                @foreach($data->profit_loss[$key] as $acc)
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
                        Rp{{ number_format($data->profit_loss[$value] ?? 0, 2) }}
                    </x-table.table-th>
                </tr>
            @else
                <tr>
                    <x-table.table-th class="text-left">{{ ucfirst($key) }}</x-table.table-th>
                    <x-table.table-th></x-table.table-th>
                    <x-table.table-th></x-table.table-th>
                    <x-table.table-th class="text-right">
                        Rp{{ number_format($data->profit_loss[$key] ?? 0, 2) }}
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
