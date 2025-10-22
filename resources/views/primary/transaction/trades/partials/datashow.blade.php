@php
    $list_files = $data->files ?? [];
    foreach($list_files as $index => $file){
        $list_files[$index]['number'] = $data->number ?? 'number?';
    }
    
    $list_tx_data = [
        $data->id => [
            'number' => $data->number,
            'date' => optional($data->sent_time)->format('Y-m-d'),
        ],
    ];

    // cek children
    $children = $data->children->sortBy('sent_time');
    foreach($children as $child){
        $list_tx_data[$child->id] = [
            'number' => $child->number,
            'date' => optional($child->sent_time)->format('Y-m-d'),
        ];

        $child_files = $child->files ?? [];
        foreach($child_files as $index => $file){
            $child_files[$index]['number'] = $child->number ?? 'number?';
        }

        $list_files = array_merge($list_files, $child_files);
    }



    $tx_relations = $data->relations ?? collect();
    $tx_relation = $data->relation ?? null;
    if($tx_relation){
        $tx_relations = $tx_relations->push($tx_relation);
    }



    $request = request();
    $space_id = get_space_id($request, false);
    $space_role = session('space_role') ?? null;
    $allow_update = ($data->space_id == $space_id) ? ($space_role == 'admin' || $space_role == 'owner') : false;
    $allow_duplicate = $allow_update ?? false;
@endphp


@if(isset($get_page_show) && $get_page_show == 'show')
    <h1 class="text-2xl font-bold mb-6">Journal: {{ $data->number }} in {{ $data?->space?->name ?? '$space-name' }}</h1>
        <div class="mb-3 mt-1 flex-grow border-t border-gray-300 dark:border-gray-700"></div>
@endif



    <div class="grid grid-cols-3 sm:grid-cols-3 gap-6">
        <x-div.box-show title="Transaksi Induk">
            @if($data->parent)
                space: {{ $data?->parent?->space?->name ?? 'space-name' }} <br>
                number: {{ $data->parent?->model_type ?? 'Type' }} : 
                    <a href="{{ route('trades.show', ['trade' => $data->parent->id]) }}" 
                        class="text-blue-500 hover:underline"
                        target="_blank">
                        {{ $data->parent?->number ?? 'parent-number?' }}
                    </a>
                    <br>
                date: {{ optional($data?->parent?->sent_time)?->format('Y-m-d') ?? 'parent-date' }}
            @endif
        </x-div.box-show>
        <x-div.box-show title="Details">
            space: {{ $data?->space?->name ?? 'space-name' }} <br>
            date: {{ optional($data->sent_time)?->format('Y-m-d') ?? '??' }} <br>
            status: {{ $data->status ?? 'status?' }}
        </x-div.box-show>
        <x-div.box-show title="Total Amount">
            subtotal: Rp{{ number_format($data->total_details, 2) }} <br>
            total:Rp{{ number_format($data->total, 2) }}
        </x-div.box-show>


        <x-div.box-show title="Contributor">
            Created By: {{ $data->sender?->name ?? 'N/A' }}<br>
            Updated By: {{ $data?->handler?->name ?? 'N/A' }}
        </x-div.box-show>
        <x-div.box-show title="Receiver">
            Received Date: {{ optional($data->received_time)?->format('Y-m-d') ?? 'not yet' }} <br>
            Receiver: {{ $data?->receiver?->name ?? 'N/A' }} <br>
            Notes: {{ $data?->receiver_notes ?? 'N/A' }}
        </x-div.box-show>
        <x-div.box-show title="Notes">
            Sender: {{ $data->sender_notes ?? '-' }}<br>
            Handler: {{ $data->handler_notes ?? '-' }}<br>
            Receiver: {{ $data->receiver_notes ?? '-' }}
        </x-div.box-show>


        <!-- <x-div.box-show title="Number">{{ $data->number }}</x-div.box-show> -->
    </div>
    <br>
    <div class="mb-3 mt-1 flex-grow border-t border-gray-300 dark:border-gray-700"></div>



    <!-- Journal Entry Details Section -->
    <h3 class="text-lg font-bold my-3">TX Details</h3>
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
                @foreach ($data->details as $detail)
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
    <div class="my-6 flex-grow border-t border-gray-500 dark:border-gray-700"></div>



    <h3 class="text-lg font-bold my-3">TX Anak</h3>
    @if($tx_related && $tx_related->count() > 0)
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
                @foreach ($tx_related as $child)
                    <x-table.table-tr>
                        <x-table.table-td>{{ $child?->sent_time->format('Y-m-d') }}</x-table.table-td>
                        <x-table.table-td>{{ $child->space?->name ?? 'N/A' }}</x-table.table-td>
                        <x-table.table-td>{{ $child->model_type ?? 'Type' }} : 
                            <a href="{{ route('trades.show', ['trade' => $child->id]) }}" 
                                class="text-blue-500 hover:underline"
                                target="_blank">
                                {{ $child->number }}
                            </a>
                        </x-table.table-td>
                        <x-table.table-td>{{ $child->sender?->name ?? 'sender' }} <br> {{ $child->handler?->name ?? 'handler' }}</x-table.table-td>
                        <x-table.table-td>{{ $child->status ?? 'status' }}</x-table.table-td>
                        <x-table.table-td>{{ number_format($child->total, 2) }}</x-table.table-td>
                        <x-table.table-td>{{ $child->notes ?? 'notes' }}</x-table.table-td>
                        <x-table.table-td class="flex justify-center items-center gap-2">
                            @if(!isset($get_page_show) || $get_page_show != 'show')
                                @if($child->model_type == 'TRD')
                                    <x-buttons.button-showjs onclick='showjs_tx({{ $child }})'></x-buttons.button-showjs>
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
                        <x-table.table-td>{{ $relation?->sent_time->format('Y-m-d') }}</x-table.table-td>
                        <x-table.table-td>{{ $relation->space?->name ?? 'N/A' }}</x-table.table-td>
                        <x-table.table-td>{{ $relation->model_type ?? 'Type' }} : 
                            {{ $relation->number }}
                        </x-table.table-td>
                        <x-table.table-td>{{ $relation->sender?->name ?? 'sender' }} <br> {{ $relation->handler?->name ?? 'handler' }}</x-table.table-td>
                        <x-table.table-td>{{ $relation->status ?? 'status' }}</x-table.table-td>
                        <x-table.table-td>{{ number_format($relation->total, 2) }}</x-table.table-td>
                        <x-table.table-td>{{ $relation->notes ?? 'notes' }}</x-table.table-td>
                        <x-table.table-td class="flex justify-center items-center gap-2">
                            @if(!isset($get_page_show) || $get_page_show != 'show')
                                <!-- <x-buttons.button-showjs onclick='showjs_tx({{ $relation }})'></x-buttons.button-showjs> -->
                            @endif
                        </x-table.table-td>
                    </x-table.table-tr>
                @endforeach
            </x-table.table-tbody>
        </x-table.table-table>
    </div>
    @endif
    <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>


    
    <h3 class="text-lg font-bold my-3">Documents</h3>
    <div class="grid grid-cols-2 sm:grid-cols-2 gap-6 w-full mb-4">
        <x-div.box-show title="File Terkait">
            @if(!empty($list_files))
                <table class="min-w-full border border-gray-300 text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border px-2 py-1 text-left">#</th>
                            <th class="border px-2 py-1 text-left">TX Number</th>
                            <th class="border px-2 py-1 text-left">Nama File</th>
                            <th class="border px-2 py-1 text-left">Ukuran</th>
                            <th class="border px-2 py-1 text-left">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($list_files as $index => $file)
                            <tr>
                                <td class="border px-2 py-1">{{ $index + 1 }}</td>
                                <td class="border px-2 py-1">{{ $file['number'] ?? 'number?' }}</td>
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



    <h3 class="text-lg font-bold my-3">Detail Lain</h3>
    <div class="grid grid-cols-3 sm:grid-cols-3 gap-6 w-full mb-4">
        <x-div.box-show title="Tags">
            {{ implode(', ', $data->tags ?? []) ?? 'tags' }}
        </x-div.box-show>

        <x-div.box-show title="Links">
            {!! collect($data->links ?? [])
                    ->map(fn($item) => json_encode($item, JSON_UNESCAPED_UNICODE))
                    ->implode('<br>') !!}
        </x-div.box-show>

        <x-div.box-show title="Files">
        </x-div.box-show>
    </div>
    <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>



    <h3 class="text-lg font-bold my-3">Actions</h3>
        <div class="grid grid-cols-2 sm:grid-cols-2 gap-6 w-full mb-4">
            <div class="flex gap-3 mt-8">
                <x-secondary-button type="button">
                    <a href="{{ route('trades.invoice', 
                                    ['id' => $data->id, 'invoice_type' => 'invoice_formal']) }}" target="_blank" class="btn btn-primary">
                        Invoice Formal
                    </a>
                </x-secondary-button>

                <!-- jika punya children atau dia punya induk, tampilkan ini -->
                @if($data->children->isNotEmpty() || $data->parent_id != null)
                    @php
                        $parent_id = $data->parent_id ?? $data->id;
                    @endphp
                    <x-secondary-button type="button">
                        <a href="{{ route('trades.invoice', 
                                        ['id' => $parent_id, 'invoice_type' => 'invoice_induk']) }}" target="_blank" class="btn btn-primary">
                            Invoice Induk
                        </a>
                    </x-secondary-button>
                @endif
            </div>

            <div class="flex gap-3 justify-end mt-8">
                @if($allow_duplicate)
                    <x-buttons.button-duplicate :route="route('trades.duplicate', $data->id)">Duplicate</x-buttons.button-duplicate>
                @endif
            </div>
        </div>
    <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>
