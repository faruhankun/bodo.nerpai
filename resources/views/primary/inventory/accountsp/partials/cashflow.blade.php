@php
    $cashflow = $data->cashflow;

    function format_number($number){
        return $number < 0 ? '(' . number_format(abs($number), 2) . ')' : number_format($number, 2);
    }
@endphp


<!-- Rangkuman Cashflow   -->
<h1 class="text-3xl font-bold dark:text-white" id="summary-title">Laporan Arus Kas</h1>
<br>

<x-table.table-table id="summary-table">
    <x-table.table-thead>
        <x-table.table-tr>
            <x-table.table-th class="text-center">Key</x-table.table-th>
            <x-table.table-th class="text-center">Value</x-table.table-th>
        </x-table.table-tr>
    </x-table.table-thead>

    <x-table.table-tbody>
        @foreach($cashflow as $key => $module) 
            <x-table.table-tr>
                <x-table.table-td class="text-left text-lg">{{ ucfirst($key) }}</x-table.table-td>
                <x-table.table-td class="text-right text-lg">
                    @if(!is_array($module) && !is_object($module))
                        {{ format_number($module) }}
                    @endif
                </x-table.table-td>
            </x-table.table-tr>
        @endforeach
    </x-table.table-tbody>
</x-table.table-table>
<br>
