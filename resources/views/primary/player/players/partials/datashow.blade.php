@php
    $tx_as_receiver = $data->transactions_as_receiver ?? null;
    $txs_details = $tx_as_receiver->map(fn($tx) => $tx->details)->flatten(1) ?? null;
@endphp



@if(isset($get_page_show) && $get_page_show == 'show')
    <h1 class="text-2xl font-bold mb-6">Journal: {{ $data->number }} in {{ $data?->space?->name ?? '$space-name' }}</h1>
        <div class="mb-3 mt-1 flex-grow border-t border-gray-300 dark:border-gray-700"></div>
@endif



<div class="grid grid-cols-3 sm:grid-cols-3 gap-6">
        <!-- <x-div-box-show title="Number">{{ $data->number }}</x-div-box-show> -->
        <x-div-box-show title="Date Created">{{ optional($data->created_at)?->format('Y-m-d') ?? '??' }}</x-div-box-show>

        <x-div-box-show title="Name">
            {{ $data->name ?? 'N/A' }}
        </x-div-box-show>
        <x-div-box-show title="Space">
            Space: {{ $data?->space?->name ?? 'N/A' }}
        </x-div-box-show>


        <x-div-box-show title="Kontak">
            Email: {{ $data?->email ?? '-' }} <br>
            Phone: {{ $data?->phone_number ?? '-' }}
        </x-div-box-show>
        <x-div-box-show title="Status">
            {{ $data->status }}
        </x-div-box-show>
        <x-div-box-show title="Notes">
            Notes: {{ $data->notes ?? '-' }}
        </x-div-box-show>
    </div>
    <br>
    <div class="mb-3 mt-1 flex-grow border-t border-gray-300 dark:border-gray-700"></div>



    <h3 class="text-lg font-bold my-3">Transactions as Receiver</h3>
    @if($tx_as_receiver)
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
                @foreach ($tx_as_receiver as $child)
                    <x-table.table-tr>
                        <x-table.table-td>{{ $child->model_type ?? 'Type' }} : 
                            <a href="{{ route('trades.show', ['trade' => $child->id]) }}" 
                                class="text-blue-500 hover:underline"
                                target="_blank">
                                {{ $child->number }}
                            </a>
                        </x-table.table-td>
                        <x-table.table-td>{{ $child->space?->name ?? 'N/A' }}</x-table.table-td>
                        <x-table.table-td>{{ $child?->sent_time->format('Y-m-d') }}</x-table.table-td>
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





    <h3 class="text-lg font-bold my-3">Related Items</h3>
    @if($txs_details)
    <div class="overflow-x-auto">
        <x-table.table-table id="journal-entry-details">
            <x-table.table-thead>
                <tr>
                    <x-table.table-th>Item</x-table.table-th>
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
                        <x-table.table-td>{{ $detail->detail?->sku ?? 'sku' }} : {{ $detail->detail?->name ?? 'N/A' }}</x-table.table-td>
                        <x-table.table-td>{{ $detail->model_type ?? 'type' }}</x-table.table-td>
                        <x-table.table-td>{{ number_format($detail->quantity, 0) }}</x-table.table-td>
                        <x-table.table-td class="py-4">{{ number_format($detail->price) }}</x-table.table-td>
                        <x-table.table-td class="py-4">{{ number_format($detail->discount * 100) }}</x-table.table-td>
                        <x-table.table-td>{{ number_format($detail->price * $detail->discount) }}</x-table.table-td>
                        <x-table.table-td>{{ number_format($detail->quantity * $detail->price * (1 - $detail->discount)) }}</x-table.table-td>
                        <x-table.table-td>{{ $detail->notes ?? 'N/A' }}</x-table.table-td>
                    </x-table.table-tr>
                @endforeach
                <x-table.table-tr>
                    <x-table.table-th colspan="6" class="font-bold">Total</x-table.table-th>
                    <x-table.table-th colspan="2" class="font-bold">{{ number_format($data->total) }}</x-table.table-th>
                </x-table.table-tr>
            </x-table.table-tbody>
        </x-table.table-table>
    </div>
    @endif

    <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>






    <h3 class="text-lg font-bold my-3">Actions</h3>

    <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>
