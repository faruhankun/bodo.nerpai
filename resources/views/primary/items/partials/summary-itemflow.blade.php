<div>
    <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>
    <!-- Rangkuman Items -->
    <h3 class="text-2xl font-bold text-2xl text-xl dark:text-white">Rangkuman Flow Items</h3>
    <x-table.table-table id="search-table">
        <x-table.table-thead>
            <tr>
                <x-table.table-th>SKU</x-table.table-th>
                <x-table.table-th>Nama</x-table.table-th>
                @if($list_model_types)
                    @foreach ($list_model_types as $model_type)
                        <x-table.table-th>{{ $model_type['name'] ?? ($model_type['id'] ?? 'id') }}</x-table.table-th>
                        <x-table.table-th>{{ $model_type['name'] ?? ($model_type['id'] ?? 'id') }} Value</x-table.table-th>
                    @endforeach
                @endif
                <x-table.table-th>Actions</x-table.table-th>
            </tr>
        </x-table.table-thead>
        <x-table.table-tbody>
            @if(isset($itemflow[$space_id]))
                @foreach ($itemflow[$space_id] as $key =>$value)
                    <x-table.table-tr>
                        <x-table.table-td>{{ $value['item']->sku ?? '-' }}</x-table.table-td>
                        <x-table.table-td>{{ $value['item']->name ?? '-' }}</x-table.table-td>
                        @if($list_model_types)
                            @foreach ($list_model_types as $model_type)
                                <x-table.table-td>{{ $value[$model_type['id']]['quantity'] ?? '0' }}</x-table.table-td>
                                <x-table.table-td>{{ $value[$model_type['id']]['subtotal'] ?? '0' }}</x-table.table-td>
                            @endforeach
                        @endif
                        <x-table.table-td>
                        </x-table.table-td>
                    </x-table.table-tr>
                @endforeach
            @endif
        </x-table.table-tbody>
    </x-table.table-table>
</div>