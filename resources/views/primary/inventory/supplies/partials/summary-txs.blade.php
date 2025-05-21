<div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>
<!-- Rangkuman Mutasi Transaksi   -->
<h3 class="text-2xl font-bold text-2xl text-xl dark:text-white">Rangkuman Mutasi Transaksi</h3>
<x-table.table-table id="search-table">
    <x-table.table-thead>
        <tr>
            <x-table.table-th>Date</x-table.table-th>
            <x-table.table-th>Purchase</x-table.table-th>
            <x-table.table-th>Return</x-table.table-th>
            <x-table.table-th>Opname Found</x-table.table-th>
            <x-table.table-th>Move</x-table.table-th>
            <x-table.table-th>Opname Loss</x-table.table-th>
            <x-table.table-th>Sales</x-table.table-th>
            <x-table.table-th>Damage</x-table.table-th>
            <x-table.table-th>Undefined</x-table.table-th>
            <x-table.table-th>Balance</x-table.table-th>
        </tr>
    </x-table.table-thead>
    <x-table.table-tbody>
        @if(isset($spaces_data[$space_id]))
            @foreach($spaces_data[$space_id] as $sent_time => $per_date)
                <x-table.table-tr>
                    <x-table.table-td>{{ $sent_time }}</x-table.table-td>
                    <x-table.table-td>{{ number_format($per_date['PO'], 2) }}</x-table.table-td>
                    <x-table.table-td>{{ number_format($per_date['RTR'], 2) }}</x-table.table-td>
                    <x-table.table-td>{{ number_format($per_date['FND'], 2) }}</x-table.table-td>
                    <x-table.table-td>{{ number_format($per_date['MV'], 2) }}</x-table.table-td>
                    <x-table.table-td>{{ number_format($per_date['LOSS'], 2) }}</x-table.table-td>
                    <x-table.table-td>{{ number_format($per_date['SO'], 2) }}</x-table.table-td>
                    <x-table.table-td>{{ number_format($per_date['DMG'], 2) }}</x-table.table-td>
                    <x-table.table-td>{{ number_format($per_date['UNDF'], 2) }}</x-table.table-td>
                    <x-table.table-td>{{ number_format($per_date['balance'], 2) }}</x-table.table-td>
                </x-table.table-tr>
            @endforeach
        @endif
    </x-table.table-tbody>
</x-table.table-table>



<div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>
<!-- Rangkuman Space-->
<h3 class="text-2xl font-bold text-2xl text-xl dark:text-white">Rangkuman Space</h3>
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
            @foreach($spaces_data as $id => $value)
                <tr>
                    <td class="border px-4 py-2">{{ $id }}</td>
                    <td class="border px-4 py-2">{{ $spaces_per_id[$id][0]->name }}</td>
                    <td class="border px-4 py-2">{{ 0 }}</td>
                    <td class="border px-4 py-2">{{ number_format($value->sum('change'), 2) }}</td>
                </tr>
            @endforeach
            
            <tr>
                <td class="border px-4 py-2">Total</td>
                <td class="border px-4 py-2">{{ $spaces_data->count() }} Spaces</td>
                <td class="border px-4 py-2">{{ 0 }}</td>
                <td class="border px-4 py-2">{{ number_format($spaces_data->sum(function ($value) { return $value->sum('change'); }), 2) }}</td>
            </tr>
        </tbody>
    </table>
</div>