@php
    use Carbon\Carbon;

    $layout = session('layout');

    $real_start_date = request('start_date') ?? null;
    $end_date = request('end_date') ?? now()->format('Y-m-d');
    $start_date = request('start_date') ?? null;

    $summary_type = request('summary_type') ?? null;
    $list_model_types = $list_model_types ?? [];


    $space_id = get_space_id(request());
@endphp
<x-dynamic-component :component="'layouts.' . $layout">
    <div class="py-12">
        <div class=" sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-white">
                    <h3 class="text-3xl font-bold dark:text-white">Rangkuman Mutasi Barang</h3>
                    <div class="flex justify-between items-center m-4 border-solid border-2 dark:border-gray-700">
                        <form action="{{ route('items.summary') }}" method="GET">
                            <div class="grid grid-cols-4 border-solid border-2">
                                <x-div.box-input label="summary_type" class="m-4">
                                    <select name="summary_type" id="summary_type">
                                        @foreach($data->summary_types as $key => $value)
                                            <option value="{{ $key }}" {{ $key == $summary_type ? 'selected' : '' }}>{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </x-div.box-input>
                                <x-div.box-input label="Start Date" class="m-4">
                                    <x-input.input-basic type="date" name="start_date" value="{{ $start_date }}" id="start_date"></x-input.input-basic>
                                </x-div.box-input>
                                <x-div.box-input label="End Date" class="m-4">
                                    <x-input.input-basic type="date" name="end_date" value="{{ $end_date }}" id="end_date" required></x-input.input-basic>
                                </x-div.box-input>
                                <x-div.box-input label="Filter" class="m-4">
                                    <x-primary-button class="ml-4">Filter</x-primary-button>
                                </x-div.box-input>
                            </div>
                        </form>
                        <x-div.box-input label="Export" class="m-4">
                            <x-secondary-button class="ml-4" id="exportVisibleBtn">Export</x-secondary-button>
                        </x-div.box-input>
                    </div>

                    <!-- Filter -->
                    <h3 class="text-2xl font-bold text-2xl text-xl dark:text-white">Filter</h3>
                    <!-- export import  -->

                    @php
                        if(!is_null($summary_type)){                      
                            $itemflow = $data->itemflow;

                            // dd($itemflow);
                        }
                    @endphp

                    @switch($summary_type)
                        @case('itemflow')
                            @include('primary.items.partials.summary-itemflow')
                        @break
                        @case('stockflow_items')

                        @break
                    @endswitch



                    <div id="react-supplies-modal" data-id="" data-start_date="" data-end_date="" data-account_data></div>


                    <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>
                    <!-- Back Button -->
                    <div class="flex mt-8">
                        <x-secondary-button>
                            <a href="{{ route('summaries.index') }}">Back to Report</a>
                        </x-secondary-button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-dynamic-component>


<script>
    $('#exportVisibleBtn').on('click', function () {
        const datatable = window.datatableInstances['search-table'];
        if (!datatable) return;

        let csv = exim.simpleDTtoCSV(datatable);

        // Download
        let filename = "export-summary-{{ $summary_type }}-{{ $real_start_date }}-{{ $end_date }}.csv";
        exim.exportCSV(csv.join("\n"), filename);

        delete window.datatableInstances['search-table'];
    });
</script>


<script>
    function show_tx_modal(acc){
        acc = acc;

        if(acc.id == null){
            return;
        }

        const container = document.getElementById('react-supplies-modal');
        
        const today = new Date();
        const year = today.getFullYear();
        const startDate = $('#start_date').val() ?? `${year}-01-01`;
        const endDate = $('#end_date').val() ?? today.toISOString().split('T')[0]; // format: YYYY-MM-DD

        container.setAttribute('data-id', acc.id);
        container.setAttribute('data-start_date', startDate);
        container.setAttribute('data-end_date', endDate);
        container.setAttribute('data-account_data', JSON.stringify(acc));

        console.log(acc);

        window.dispatchEvent(new CustomEvent('showSuppliesModal'));
    }
</script>

@vite('resources/js/app.jsx')
