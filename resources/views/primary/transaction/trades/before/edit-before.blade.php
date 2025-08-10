@php
    $space_id = session('space_id') ?? null;
    $player = auth()->user()->player ?? null;
    $layout = session('layout');

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
@endphp

<x-dynamic-component :component="'layouts.' . $layout">
    <div class="py-12">
        <div class=" sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-white">
                    <h3 class="text-2xl dark:text-white font-bold">Update {{ $header }} : {{ $tx->number }}</h3>
                    <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>

                    <form action="{{ route('trades.update', $tx->id) }}" method="POST" onsubmit="return validateForm()">
                        @csrf
                        @method('PUT')

                        <input type="hidden" name="space_id" value="{{ $space_id }}">

                        <input type="hidden" name="model_type" value="{{ $tx_type }}">

                        @include('primary.transaction.trades.partials.dataform', [
                            'players' => $players,
                            'spaces' => $spaces,
                            'form' => ['id' => 'Edit Trades', 'mode' => 'edit', 'type' => $tx_type]
                            ])

                        <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>

                        <div class="m-4 flex justify-end space-x-4">
                            <a href="{{ $route_back }}">
                                <x-secondary-button type="button">Cancel</x-secondary-button>
                            </a>
                            <x-primary-button>Update Trades</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-dynamic-component>


<script>
    $(document).ready(function() {
        let tx = @json($tx);

        let tx_type = "{{ $tx_type }}";
        let player = @json($player);

        
        $('#edit_sender_id').val(tx.sender_id);
        $('#edit_sent_date').val(tx.sent_date);
        $('#edit_sender_notes').val(tx.sender_notes);
        $('#edit_input_id').val(tx.input_id);
        
        $('#edit_receiver_id').val(tx.receiver_id);
        $('#edit_received_date').val(tx.received_date);
        $('#edit_receiver_notes').val(tx.receiver_notes);
        $('#edit_output_id').val(tx.output_id);
        

        if(tx_type == 'PO'){
            $('#edit_receiver_id').prop('disabled', true);
            $('#edit_receiver_id_hidden').val(tx.receiver_id);
            
            $('#edit_input_id').prop('disabled', true);
        } else if(tx_type == 'SO'){
            $('#edit_sender_id').prop('disabled', true);
            $('#edit_sender_id_hidden').val(tx.sender_id);

            $('#edit_output_id').prop('disabled', true);
        }


    });
</script>
