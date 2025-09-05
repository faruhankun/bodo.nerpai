@if(isset($get_page_show) && $get_page_show == 'show')
    <h1 class="text-2xl font-bold mb-6">Journal: {{ $data->number }} in {{ $data?->space?->name ?? '$space-name' }}</h1>
        <div class="mb-3 mt-1 flex-grow border-t border-gray-300 dark:border-gray-700"></div>
@endif



    <div class="grid grid-cols-3 sm:grid-cols-3 gap-6">
        <x-div-box-show title="Code">{{ $data->code ?? 'N/A' }}</x-div-box-show>
        <x-div-box-show title="SKU">{{ $data->sku ?? 'N/A' }}</x-div-box-show>
        <x-div-box-show title="Name">{{ $data->name ?? 'N/A' }}</x-div-box-show>
        <x-div-box-show title="Price">{{ number_format($data->price ?? 0, 2) }}</x-div-box-show>

        @if($allow_cost)
            <x-div-box-show title="Cost">{{ $data->cost ?? 'N/A' }}</x-div-box-show>
        @endif

        <x-div-box-show title="Status">{{ $data->status }}</x-div-box-show>
        <x-div-box-show title="Notes">{{ $data->notes ?? 'N/A' }}</x-div-box-show>
    </div>
    <br>
    <div class="mb-3 mt-1 flex-grow border-t border-gray-300 dark:border-gray-700"></div>



    <x-div-box-show title="Description">{{ $data->description ?? 'description here!' }}</x-div-box-show>
    <div class="my-6 flex-grow border-t border-gray-500 dark:border-gray-700"></div>



    <!-- Supplies -->
    <h3 class="text-lg font-bold my-3">Supplies Details</h3>
    <div class="overflow-x-auto">
        <x-table.table-table id="inventories-details">
            <x-table.table-thead>
                <tr>
                    <x-table.table-th>SKU</x-table.table-th>
                    <x-table.table-th>Space</x-table.table-th>
                    <x-table.table-th>Qty</x-table.table-th>
                    <x-table.table-th>Cost/Unit</x-table.table-th>
                    <x-table.table-th>Notes</x-table.table-th>
                </tr>
            </x-table.table-thead>
            <x-table.table-tbody>
                @php 
                    $cost_total = 0;
                    $balance_total = 0;
                @endphp

                @foreach ($supplies as $supply)
                    @if($supply->balance == 0) @continue @endif
                    <x-table.table-tr>
                        <x-table.table-td>{{ $supply->sku ?? 'N/A' }}</x-table.table-td>
                        <x-table.table-td>{{ $supply->space_type ?? 'N/A' }} : {{ $supply->space?->name ?? 'N/A' }}</x-table.table-td>
                        <x-table.table-td>{{ intval($supply->balance) ?? 'N/A' }}</x-table.table-td>
                        <x-table.table-td class="py-4">Rp{{ $allow_cost ? number_format($supply->cost_per_unit, 2) : 'null' }}</x-table.table-td>
                        <x-table.table-td>{{ $detail->notes ?? 'N/A' }}</x-table.table-td>
                    </x-table.table-tr>

                    @php 
                        $balance_total += $supply->balance;
                        $cost_total += $supply->cost_per_unit * $supply->balance;
                    @endphp
                @endforeach


                <x-table.table-tr>
                    <x-table.table-th></x-table.table-th>
                    <x-table.table-th class="text-lg font-bold">Total</x-table.table-th>
                    <x-table.table-th class="text-lg">{{ $balance_total }}</x-table.table-th>
                    <x-table.table-th class="text-lg">Rp{{ $allow_cost ? number_format($cost_total) : 'null' }}</x-table.table-th>
                    <x-table.table-th></x-table.table-th>
                </x-table.table-tr>
            </x-table.table-tbody>
        </x-table.table-table>
    </div>
    <div class="my-6 flex-grow border-t border-gray-500 dark:border-gray-700"></div>




    <h3 class="text-lg font-bold my-3">Related Trades</h3>
    @if($txs_details)
    <div class="overflow-x-auto">
        <x-table.table-table id="search-table1">
            <x-table.table-thead>
                <tr>
                    <x-table.table-th>Date</x-table.table-th>
                    <x-table.table-th>Number</x-table.table-th>
                    <x-table.table-th>Type</x-table.table-th>
                    <x-table.table-th>Quantity</x-table.table-th>
                    <x-table.table-th>Price</x-table.table-th>
                    <x-table.table-th>Discount</x-table.table-th>
                    <x-table.table-th>Disc Value</x-table.table-th>
                    <x-table.table-th>Subtotal</x-table.table-th>
                    <x-table.table-th>Notes</x-table.table-th>
                </tr>
            </x-table.table-thead>
            <x-table.table-tbody>
                @foreach ($txs_details as $detail)
                    <x-table.table-tr>
                        <x-table.table-td>{{ $detail?->transaction?->sent_time->format('Y-m-d') ?? '-' }}</x-table.table-td>
                        <x-table.table-td class="flex justify-center items-center gap-2">
                            @if($detail?->transaction->model_type == 'TRD')    
                                <a href="javascript:void(0)" onclick='showjs_tx({{ $detail?->transaction_id }})'
                                    class="text-blue-600 hover:text-blue-800 hover:underline cursor-pointer">
                                    {{ $detail?->transaction?->number ?? 'N/A' }}
                                </a>
                            @endif
                        </x-table.table-td>
                        <x-table.table-td>{{ $detail->model_type ?? 'type' }}</x-table.table-td>
                        <x-table.table-td>{{ number_format($detail->quantity, 0) }}</x-table.table-td>
                        <x-table.table-td class="py-4">{{ number_format($detail->price) }}</x-table.table-td>
                        <x-table.table-td class="py-4">{{ number_format($detail->discount * 100) }}%</x-table.table-td>
                        <x-table.table-td>{{ number_format($detail->price * $detail->discount) }}</x-table.table-td>
                        <x-table.table-td>{{ number_format($detail->quantity * $detail->price * (1 - $detail->discount)) }}</x-table.table-td>
                        <x-table.table-td>{{ $detail->notes ?? 'N/A' }}</x-table.table-td>
                    </x-table.table-tr>
                @endforeach
            </x-table.table-tbody>
        </x-table.table-table>
    </div>
    @endif
    <div class="my-6 flex-grow border-t border-gray-500 dark:border-gray-700"></div>

    
    
    <h3 class="text-lg font-bold my-3">Documents</h3>
    <div class="grid grid-cols-2 sm:grid-cols-2 gap-6 w-full mb-4">
        <x-div.box-show title="File Terkait">
            @if(!empty($data->files))
                <table class="min-w-full border border-gray-300 text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border px-2 py-1 text-left">#</th>
                            <th class="border px-2 py-1 text-left">Nama File</th>
                            <th class="border px-2 py-1 text-left">Ukuran</th>
                            <th class="border px-2 py-1 text-left">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data->files as $index => $file)
                            <tr>
                                <td class="border px-2 py-1">{{ $index + 1 }}</td>
                                <td class="border px-2 py-1">
                                    <a href="{{ asset($file['path']) }}" target="_blank" class="text-blue-600 hover:underline">
                                        {{ $file['name'] }}
                                    </a>
                                </td>
                                <td class="border px-2 py-1">
                                    @if(!empty($file['size']))
                                        {{ number_format($file['size'] / 1024, 2) }} KB
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="border px-2 py-1">
                                    <a href="{{ asset($file['path']) }}" download class="text-green-600 hover:underline">
                                        Download
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-gray-500">Tidak ada file terkait.</p>
            @endif
        </x-div.box-show>
    </div>
    <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>



    <!-- Action Section -->
    <h3 class="text-lg font-bold my-3">Actions</h3>
    <div class="flex gap-3 mt-8">
        <x-secondary-button type="button" onclick="updateInventoryToChildren({{ $data->id }})">Update Inventory to Children</x-secondary-button>
    </div>
    <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>
