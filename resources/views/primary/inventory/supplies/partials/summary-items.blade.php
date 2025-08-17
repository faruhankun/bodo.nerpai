<div>
    <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>
    <!-- Rangkuman Items -->
    <h3 class="text-2xl font-bold text-2xl text-xl dark:text-white">Rangkuman Flow Items</h3>
    <x-table.table-table id="search-table">
        <x-table.table-thead>
            <tr>
                <x-table.table-th>SKU</x-table.table-th>
                <x-table.table-th>Nama</x-table.table-th>
                <x-table.table-th>In</x-table.table-th>
                <x-table.table-th>In Value</x-table.table-th>
                <x-table.table-th>Out</x-table.table-th>
                <x-table.table-th>Stok Value</x-table.table-th>
                <x-table.table-th>Stok</x-table.table-th>
                <!-- <x-table.table-th>Cost</x-table.table-th>
                <x-table.table-th>Margin</x-table.table-th> -->
                <x-table.table-th>Actions</x-table.table-th>
            </tr>
        </x-table.table-thead>
        <x-table.table-tbody>
            @if(isset($items_data[$space_id]))
                @foreach ($items_data[$space_id] as $key =>$value)
                    <x-table.table-tr>
                        <x-table.table-td>{{ $value['item']->sku ?? '-' }}</x-table.table-td>
                        <x-table.table-td>{{ $value['item']->name ?? '-' }}</x-table.table-td>
                        <x-table.table-td>{{ $value['in'] ?? 0 }}</x-table.table-td>
                        <x-table.table-td>{{ $value['in_subtotal'] ?? 0 }}</x-table.table-td>
                        <x-table.table-td>{{ $value['out'] ?? 0 }}</x-table.table-td>
                        <x-table.table-td>{{ ($value['in_subtotal'] ?? 0) - ($value['out_subtotal'] ?? 0) }}</x-table.table-td>
                        <x-table.table-td>
                            <!-- <a href="javascript:void(0)" onclick='show_tx_modal({{ $value["item"] }})'
                                class="text-right font-bold text-md text-blue-600"> -->
                                {{ ($value['in'] ?? 0) - ($value['out'] ?? 0) }}
                            <!-- </a> -->
                        </x-table.table-td>
                        <!-- <x-table.table-td>{{ $value['out_subtotal'] ?? 0 }}</x-table.table-td>
                        <x-table.table-td>{{ $value['margin'] ?? 0 }}</x-table.table-td> -->
                        <x-table.table-td>
                        </x-table.table-td>
                    </x-table.table-tr>
                @endforeach
            @endif
        </x-table.table-tbody>
    </x-table.table-table>
</div>