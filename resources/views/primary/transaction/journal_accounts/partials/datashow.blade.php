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
@endphp


@if(isset($get_page_show) && $get_page_show == 'show')
    <h1 class="text-2xl font-bold mb-6">Journal: {{ $data->number }} in {{ $data?->space?->name ?? '$space-name' }}</h1>
        <div class="mb-3 mt-1 flex-grow border-t border-gray-300 dark:border-gray-700"></div>
@endif



<div class="grid grid-cols-3 sm:grid-cols-3 gap-6">
        <x-div.box-show title="Transaksi Induk">
            @if($data->parent)
                space: {{ $data?->parent?->space?->name ?? 'space-name' }} <br>
                number: {{ $data?->parent?->number ?? 'parent-number' }} <br>
                date: {{ optional($data?->parent?->sent_time)?->format('Y-m-d') ?? 'parent-date' }}
            @endif
        </x-div.box-show>
        <x-div.box-show title="Details">
            space: {{ $data?->space?->name ?? 'space-name' }} <br>
            date: {{ optional($data->sent_time)?->format('Y-m-d') ?? '??' }} <br>
        </x-div.box-show>



        <x-div.box-show title="Contributor">
            Created By: {{ $data->sender?->name ?? 'N/A' }}<br>
            Updated By: {{ $data?->handler?->name ?? 'N/A' }}
        </x-div.box-show>
        <x-div.box-show title="Receiver">
            Receiver: {{ $data?->receiver?->name ?? 'N/A' }} <br>
            Notes: {{ $data?->receiver_notes ?? 'N/A' }}
        </x-div.box-show>
        <x-div.box-show title="Notes">
            Sender: {{ $data->sender_notes ?? '-' }}<br>
            Handler: {{ $data->handler_notes ?? '-' }}<br>
            Receiver: {{ $data->receiver_notes ?? '-' }}
        </x-div.box-show>



        <x-div.box-show title="Tags">
            {{ implode(', ', $data->tags ?? []) ?? 'tags' }}
        </x-div.box-show>


        <!-- <x-div.box-show title="Number">{{ $data->number }}</x-div.box-show> -->
    </div>
    <br>
    <div class="mb-3 mt-1 flex-grow border-t border-gray-300 dark:border-gray-700"></div>



    <!-- Journal Entry Details Section -->
    <h3 class="text-lg font-bold my-3">Journal Entry Details</h3>
    <div class="overflow-x-auto">
        <x-table.table-table id="journal-entry-details">
            <x-table.table-thead>
                <tr>
                    <x-table.table-th>Account</x-table.table-th>
                    <x-table.table-th>Debit</x-table.table-th>
                    <x-table.table-th>Credit</x-table.table-th>
                    <x-table.table-th>Notes</x-table.table-th>
                </tr>
            </x-table.table-thead>
            <x-table.table-tbody>
                @foreach ($data->details as $detail)
                    <x-table.table-tr>
                        <x-table.table-td>
                            {{ $detail->detail?->code ?? 'code' }} : {{ $detail->detail?->name ?? 'name' }}
                        </x-table.table-td>
                        <x-table.table-td
                            class="py-4">Rp{{ number_format($detail->debit, 2) }}</x-table.table-td>
                        <x-table.table-td>Rp{{ number_format($detail->credit, 2) }}</x-table.table-td>
                        <x-table.table-td>{{ $detail->notes ?? 'N/A' }}</x-table.table-td>
                    </x-table.table-tr>
                @endforeach
                
                <x-table.table-tr>
                    <x-table.table-th class="font-bold">Total</x-table.table-th>
                    <x-table.table-th class="font-bold">{{ number_format($data->total) }}</x-table.table-th>
                    <x-table.table-th colspan="2" class="font-bold">{{ number_format($data->total) }}</x-table.table-th>
                </x-table.table-tr>
            </x-table.table-tbody>
        </x-table.table-table>
    </div>
    <div class="my-6 flex-grow border-t border-gray-500 dark:border-gray-700"></div>


    
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
    
    <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>
