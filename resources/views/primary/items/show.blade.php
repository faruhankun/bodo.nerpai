@php
    $layout = session('layout') ?? 'lobby';
    $space_role = session('space_role') ?? null;

    $space_id = get_space_id(request());
    $space = \App\Models\Primary\Space::findOrFail($space_id);
    $spaces = $space->spaceAndChildren();

    $supplies = $data->inventories
                    ->whereIn('space_id', $spaces->pluck('id')->toArray());

    $user = auth()->user();
    $space_role = session('space_role') ?? null;

    $allow_cost = $user->can('space.supplies.cost', 'web') || $space_role == 'owner';


    $txs_details = $data->transaction_details;
@endphp

<x-dynamic-component :component="'layouts.' . $layout">
    <div class="py-12">
        <div class=" sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-white">
                    <h1 class="text-2xl font-bold mb-6">Details: {{ $data->sku }} : {{ $data->name }}</h1>
                    <div class="mb-3 mt-1 flex-grow border-t border-gray-300 dark:border-gray-700"></div>

                    @include('primary.items.partials.datashow')


                    @include('primary.transaction.trades.showjs')

                    <!-- Action Section -->
                    <div class="flex justify-end space-x-4">
                        <x-secondary-button>
                            <a href="{{ route('items.index') }}">Back to List</a>
                        </x-secondary-button>

                        @if($space_role == 'admin' || $space_role == 'owner')
                            <a href="{{ route('items.edit', $data->id) }}">
                                <x-primary-button type="button">Edit Item</x-primary-button>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-dynamic-component>


<script>
    function showjs_tx(tx_id) {
        const trigger = 'show_modal_js';


        // ajax get data show
        $.ajax({
            url: "/api/trades/" + tx_id,
            type: "GET",
            data: {
                'page_show': 'show'
            },
            success: function(data) {
                let page_show = data.page_show ?? 'null ??';
                $('#datashow_'+trigger).html(page_show);

                let modal_edit_link = '/trades/' + tx_id + '/edit';
                $('#modal_edit_link').attr('href', modal_edit_link);

                window.dispatchEvent(new CustomEvent('open-' + trigger));
            }
        });        
    }
</script>
