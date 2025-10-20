@php
    $tx_relations = $data->relations ?? collect();
    $tx_relation = $data->relation ?? null;
    if($tx_relation){
        $tx_relations = $tx_relations->push($tx_relation);
    }
@endphp


<div class="grid grid-cols-3 sm:grid-cols-3 gap-6">
        <!-- <x-div-box-show title="Number">{{ $data->number }}</x-div-box-show> -->
        <!-- <x-div-box-show title="Date">{{ optional($data?->sent_time)?->format('Y-m-d') ?? '??' }}</x-div-box-show> -->
        <!-- <x-div-box-show title="Source">
            {{ $data->input_type ?? 'N/A' }} : {{ $data->input?->number ?? 'N/A' }}
        </x-div-box-show> -->

        <x-div-box-show title="Contributor">
            Created By: {{ $data->sender?->name ?? 'N/A' }}<br>
            Updated By: {{ $data?->handler?->name ?? 'N/A' }}
        </x-div-box-show>
        <x-div-box-show title="Notes">
            Sender: {{ $data->sender_notes ?? '-' }}<br>
            Handler: {{ $data->handler_notes ?? '-' }}
        </x-div-box-show>
        
        <x-div-box-show title="Space Transaksi ini">
            Space: {{ $data?->space?->name ?? 'N/A' }}
        </x-div-box-show>
        <x-div-box-show title="Total Amount">Rp{{ number_format($data->total, 2) }}</x-div-box-show>
        <x-div-box-show title="TX Terkait">
            TX: {{ $data->input?->number ?? '-' }} <br>
            Space: {{ $data?->input?->space?->name ?? 'N/A' }}
        </x-div-box-show>
        <!-- <x-div-box-show title="TX Tujuan">
            TX: {{ $data->output?->number ?? '-' }} <br>
            Space: {{ $data?->output?->space?->name ?? 'N/A' }}
        </x-div-box-show> -->
    </div>
    <br>
    <div class="mb-3 mt-1 flex-grow border-t border-gray-300 dark:border-gray-700"></div>



    <!-- Journal Entry Details Section -->
    <h3 class="text-lg font-bold my-3">TX Details</h3>
    <div class="overflow-x-auto">
        <x-table.table-table id="journal-entry-details">
            <x-table.table-thead>
                <tr>
                    <x-table.table-th>Inventory</x-table.table-th>
                    <x-table.table-th>Quantity</x-table.table-th>
                    <x-table.table-th>Type</x-table.table-th>
                    <x-table.table-th>Cost/Unit</x-table.table-th>
                    <x-table.table-th>Subtotal</x-table.table-th>
                    <x-table.table-th>Notes</x-table.table-th>
                </tr>
            </x-table.table-thead>
            <x-table.table-tbody>
                @foreach ($data->details as $detail)
                    <x-table.table-tr>
                        <x-table.table-td>{{ $detail->detail?->sku ?? '?' }} : {{ $detail->detail?->name ?? 'N/A' }}</x-table.table-td>
                        <x-table.table-td>{{ number_format($detail->quantity, 0) }}</x-table.table-td>
                        <x-table.table-td>{{ $detail->model_type ?? 'N/A' }}</x-table.table-td>
                        <x-table.table-td class="py-4">{{ number_format($detail->cost_per_unit) }}</x-table.table-td>
                        <x-table.table-td>{{ number_format($detail->quantity * $detail->cost_per_unit) }}</x-table.table-td>
                        <x-table.table-td>{{ $detail->notes ?? 'N/A' }}</x-table.table-td>
                    </x-table.table-tr>
                @endforeach
            </x-table.table-tbody>
        </x-table.table-table>
    </div>
    <div class="my-6 flex-grow border-t border-gray-500 dark:border-gray-700"></div>



    <h3 class="text-lg font-bold my-3">TX Related</h3>
    @if($tx_related)
    <div class="overflow-x-auto">
        <x-table.table-table id="journal-outputs">
            <x-table.table-thead>
                <tr>
                    <x-table.table-th>Number</x-table.table-th>
                    <x-table.table-th>Space</x-table.table-th>
                    <x-table.table-th>Date</x-table.table-th>
                    <x-table.table-th>Contributor</x-table.table-th>
                    <x-table.table-th>Total</x-table.table-th>
                    <x-table.table-th>Notes</x-table.table-th>
                    <x-table.table-th>Actions</x-table.table-th>
                </tr>
            </x-table.table-thead>
            <x-table.table-tbody>
                @foreach ($data->outputs as $child)
                    <x-table.table-tr>
                        <x-table.table-td>{{ $child->number }}</x-table.table-td>
                        <x-table.table-td>{{ $child->space?->name ?? 'N/A' }}</x-table.table-td>
                        <x-table.table-td>{{ $child?->sent_time }}</x-table.table-td>
                        <x-table.table-td>{{ $child->sender?->name ?? 'N/A' }} <br> {{ $child->handler?->name ?? 'N/A' }}</x-table.table-td>
                        <x-table.table-td>{{ number_format($child->total, 2) }}</x-table.table-td>
                        <x-table.table-td>{{ $child->notes ?? 'N/A' }}</x-table.table-td>
                        <x-table.table-td class="flex justify-center items-center gap-2">
                            
                        </x-table.table-td>
                    </x-table.table-tr>
                @endforeach
            </x-table.table-tbody>
        </x-table.table-table>
    </div>
    @endif
    <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>



    <h3 class="text-lg font-bold my-3">TX Terkait</h3>
    @if(isset($tx_relations) && $tx_relations->count() > 0)
    <div class="overflow-x-auto">
        <x-table.table-table id="journal-outputs">
            <x-table.table-thead>
                <tr>
                    <x-table.table-th>Date</x-table.table-th>
                    <x-table.table-th>Space</x-table.table-th>
                    <x-table.table-th>Number</x-table.table-th>
                    <x-table.table-th>Contributor</x-table.table-th>
                    <x-table.table-th>Status</x-table.table-th>
                    <x-table.table-th>Total</x-table.table-th>
                    <x-table.table-th>Notes</x-table.table-th>
                    <x-table.table-th>Actions</x-table.table-th>
                </tr>
            </x-table.table-thead>
            <x-table.table-tbody>
                @foreach ($tx_relations as $relation)
                    <x-table.table-tr>
                        <x-table.table-td>{{ $relation?->sent_time?->format('Y-m-d') }}</x-table.table-td>
                        <x-table.table-td>{{ $relation->space?->name ?? 'N/A' }}</x-table.table-td>
                        <x-table.table-td>{{ $relation->model_type ?? 'Type' }} : 
                            <a href="{{ route('trades.show', ['trade' => $relation->id]) }}" 
                                class="text-blue-500 hover:underline"
                                target="_blank">
                                {{ $relation->number }}
                            </a>
                        </x-table.table-td>
                        <x-table.table-td>{{ $relation->sender?->name ?? 'sender' }} <br> {{ $relation->handler?->name ?? 'handler' }}</x-table.table-td>
                        <x-table.table-td>{{ $relation->status ?? 'status' }}</x-table.table-td>
                        <x-table.table-td>{{ number_format($relation->total, 2) }}</x-table.table-td>
                        <x-table.table-td>{{ $relation->notes ?? 'notes' }}</x-table.table-td>
                        <x-table.table-td class="flex justify-center items-center gap-2">
                            @if(!isset($get_page_show) || $get_page_show != 'show')
                                @if($relation->model_type == 'TRD')
                                    <x-buttons.button-showjs onclick='showjs_tx({{ $relation }})'></x-buttons.button-showjs>
                                @endif
                            @endif
                        </x-table.table-td>
                    </x-table.table-tr>
                @endforeach
            </x-table.table-tbody>
        </x-table.table-table>
    </div>
    @endif
    <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>
