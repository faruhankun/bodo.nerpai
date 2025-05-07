@php
    $space_id = session('space_id') ?? null;
    $player = auth()->user()->player ?? null;
    $layout = session('layout');

    $create_type = $param['type'] ?? null;

    if(is_null($create_type)){
        abort(404);
    }

    if($create_type == 'po'){
        $header = 'Purchase Order';
        $route_back = route('trades.po');
    } else if($create_type == 'so'){
        $header = 'Sales Order';
        $route_back = route('trades.so');
    }
@endphp

<x-dynamic-component :component="'layouts.' . $layout">
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-white">
                    <h3 class="text-2xl dark:text-white font-bold">Add {{ $header }}</h3>
                    <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>

                    <form action="{{ route('trades.store') }}" method="POST" onsubmit="return validateForm()">
                        @csrf

                        <input type="hidden" name="space_id" value="{{ $space_id }}">

                        <input type="hidden" name="model_type" value="{{ $create_type }}">

                        @include('primary.transaction.trades.partials.dataform', [
                            'players' => $players,
                            'spaces' => $spaces,
                            'form' => ['id' => 'Create Trades', 'mode' => 'create', 'type' => $create_type]
                            ])

                        <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>

                        <div class="m-4 flex justify-end space-x-4">
                            <a href="{{ $route_back }}">
                                <x-secondary-button type="button">Cancel</x-secondary-button>
                            </a>
                            <x-primary-button>Create Trades</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-dynamic-component>

<script>
    $(document).ready(function() {
        // editform
        $('.edit_readonly').prop('readonly', true);

        let create_type = "{{ $create_type }}";
        let player = @json($player);

        if(create_type == 'po'){
            $('#create_receiver_id').val(player.id);
            $('#create_receiver_id').prop('disabled', true);
            $('#create_receiver_id_hidden').val(player.id);

            $('#create_input_id').prop('disabled', true);
        } else if(create_type == 'so'){
            $('#create_sender_id').val(player.id);
            $('#create_sender_id').prop('disabled', true);
            $('#create_sender_id_hidden').val(player.id);

            $('#create_output_id').prop('disabled', true);
        }
    });
</script>
