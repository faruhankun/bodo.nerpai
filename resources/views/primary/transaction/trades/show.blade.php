@php
    $layout = session('layout') ?? 'lobby';
    $space_role = session('space_role') ?? null;


    $data = $tx;
    //    dd($data);


    
    $request = request();
    $space_id = get_space_id($request);
    $space_role = session('space_role') ?? null;
    $allow_update = ($data->space_id == $space_id) ? ($space_role == 'admin' || $space_role == 'owner') : false;
@endphp

<x-dynamic-component :component="'layouts.' . $layout">
    <div class="py-12">
        <div class=" sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-white">
                    <h1 class="text-2xl font-bold mb-6">Journal: {{ $data->number }} in {{ $data?->space?->name ?? '$space-name' }}</h1>
                    <div class="mb-3 mt-1 flex-grow border-t border-gray-300 dark:border-gray-700"></div>

                    
                    @include('primary.transaction.trades.partials.datashow')


                    @include('primary.transaction.trades.showjs')



                    <!-- Action Section -->
                    <div class="flex justify-end space-x-4">
                        <x-secondary-button>
                            <a href="{{ route('trades.index') }}">Back to List</a>
                        </x-secondary-button>

                        @if(($space_role == 'admin' || $space_role == 'owner') && $allow_update)
                            <a href="{{ route('trades.edit', $data->id) }}">
                                <x-primary-button type="button">Edit Journal</x-primary-button>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-dynamic-component>


<!-- show js -->
<script>
    function showjs_tx(data) {
        console.log(data);

        const trigger = 'show_modal_js';
        const parsed = data;

        let route = 'trades';
        switch(parsed.model_type) {
            case 'TRD':
                route = 'trades';
                break;
            case 'JS':
                route = 'journal_supplies';
                break;
            default: ;
        }

        // ajax get data show
        $.ajax({
            url: "/api/" + route + "/" + parsed.id,
            type: "GET",
            data: {
                'page_show': 'show'
            },
            success: function(data) {
                let page_show = data.page_show ?? 'null ??';
                $('#datashow_'+trigger).html(page_show);

                let modal_edit_link = '/' + route + '/' + parsed.id + '/edit';
                $('#modal_edit_link').attr('href', modal_edit_link);

                window.dispatchEvent(new CustomEvent('open-' + trigger));
            }
        });        
    }
</script>
