@php
    $space_id = session('space_id') ?? null;
    $layout = session('layout');

    if(is_null($layout)){
        abort(404);
    }

    $data = $tx;

    $tx_type = $tx->input_id == $space_id ? 'SO' : 'PO';

    if(is_null($tx_type)){
        abort(404);
    }

    if($tx_type == 'PO'){
        $header = 'Purchase Order';
        $route_back = route('trades.po');
    } else if($tx_type == 'SO'){
        $header = 'Sales Order';
        $route_back = route('trades.so');
    }

    $header = 'Transaction';
@endphp

<x-dynamic-component :component="'layouts.' . $layout">
<div class="py-12">
    <div class=" sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-white">
                <h3 class="text-2xl dark:text-white font-bold">Detail {{ $header }} : {{ $tx->number }}</h3>
                <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>

                <div class="grid grid-cols-3 sm:grid-cols-3 gap-6">
                    <x-div-box-show title="Number">{{ $data->number }}</x-div-box-show>
                    <x-div-box-show title="TX Model">{{ $data->model_type }}</x-div-box-show>
                    <x-div-box-show title="Status">{{ $data->status }}</x-div-box-show>

                    <x-div-box-show title="Total Amount">Rp{{ number_format($data->total, 2) }}</x-div-box-show>
                </div>

                
                <div class="mb-3 mt-3 flex-grow border-t border-gray-300 dark:border-gray-700">Sender</div>
                <div class="grid grid-cols-3 sm:grid-cols-3 gap-6">    
                    <x-div-box-show title="Sender">
                        {{ $data->sender_type ?? 'N/A' }} : {{ $data->sender?->name ?? 'N/A' }}
                    </x-div-box-show>
                    <x-div-box-show title="Sent Date">{{ optional($data->sent_date)?->format('Y-m-d') ?? '??' }}</x-div-box-show>
                    <x-div-box-show title="Sender Notes">{{ $data->sender_notes ?? 'N/A' }}</x-div-box-show>
                    <x-div-box-show title="Input">
                        {{ $data->input_type ?? 'N/A' }} : {{ $data?->input?->name ?? 'N/A' }}
                    </x-div-box-show>
                    <x-div-box-show title="Input Address">{{ $data->input_address ?? 'N/A' }}</x-div-box-show>
                </div>

                
                <div class="mb-3 mt-3 flex-grow border-t border-gray-300 dark:border-gray-700">Receiver</div>
                <div class="grid grid-cols-3 sm:grid-cols-3 gap-6">
                    <x-div-box-show title="Receiver">
                        {{ $data->receiver_type ?? 'N/A' }} : {{ $data->receiver?->name ?? 'N/A' }}
                    </x-div-box-show>
                    <x-div-box-show title="Received Date">{{ optional($data->received_date)?->format('Y-m-d') ?? '??' }}</x-div-box-show>
                    <x-div-box-show title="Receiver Notes">{{ $data->receiver_notes ?? 'N/A' }}</x-div-box-show>
                    <x-div-box-show title="Output">
                        {{ $data->output_type ?? 'N/A' }} : {{ $data?->output?->name ?? 'N/A' }}
                    </x-div-box-show>
                    <x-div-box-show title="Output Address">{{ $data->output_address ?? 'N/A' }}</x-div-box-show>
                </div>


                <div class="mb-3 mt-3 flex-grow border-t border-gray-300 dark:border-gray-700">Details</div>
                <div class="grid grid-cols-3 sm:grid-cols-3 gap-6">
                </div>
                <br>
                <div class="mb-3 mt-1 flex-grow border-t border-gray-300 dark:border-gray-700"></div>

                <!-- Back Button -->
                <div class="flex gap-3 justify-end">
                    <x-secondary-button>
                        <a href="{{ $route_back }}">Back to List</a>
                    </x-secondary-button>
                    <x-primary-button>
                        <a href="{{ route('trades.edit', $tx->id) }}">Edit {{ $header }} </a>
                    </x-primary-button>
                </div>
            </div>
        </div>
    </div>
</div>
</x-dynamic-component>