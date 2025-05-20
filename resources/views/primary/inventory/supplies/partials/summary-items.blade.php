<div>
    <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>
    <!-- Rangkuman Items -->
    <h3 class="text-2xl font-bold text-2xl text-xl dark:text-white">Rangkuman Flow Items</h3>
    <x-table-table id="search-table">
        <x-table-thead>
            <tr>
                <x-table-th>SKU</x-table-th>
                <x-table-th>Nama</x-table-th>
                <x-table-th>In</x-table-th>
                <x-table-th>In Value</x-table-th>
                <x-table-th>Out</x-table-th>
                <x-table-th>Omzet</x-table-th>
                <x-table-th>Cost</x-table-th>
                <x-table-th>Margin</x-table-th>
                <x-table-th>Actions</x-table-th>
            </tr>
        </x-table-thead>
        <x-table-tbody>
            @if(isset($items_data[$space_id]))
                @foreach ($items_data[$space_id] as $key =>$value)
                    <x-table-tr>
                        <x-table-td>{{ $value['item']->sku ?? '-' }}</x-table-td>
                        <x-table-td>{{ $value['item']->name ?? '-' }}</x-table-td>
                        <x-table-td>{{ $value['in'] ?? 0 }}</x-table-td>
                        <x-table-td>{{ $value['in_subtotal'] ?? 0 }}</x-table-td>
                        <x-table-td>{{ $value['out'] ?? 0 }}</x-table-td>
                        <x-table-td>{{ $value['omzet'] ?? 0 }}</x-table-td>
                        <x-table-td>{{ $value['out_subtotal'] ?? 0 }}</x-table-td>
                        <x-table-td>{{ $value['margin'] ?? 0 }}</x-table-td>
                        <x-table-td>
                        </x-table-td>
                    </x-table-tr>
                @endforeach
            @endif
        </x-table-tbody>
    </x-table-table>
</div>