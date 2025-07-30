@php
    use Carbon\Carbon;

    $layout = session('layout');

    $real_start_date = request('start_date') ?? null;
    $end_date = request('end_date') ?? now()->format('Y-m-d');
    $start_date = request('start_date') ?? Carbon::parse($end_date)->startOfMonth()->format('Y-m-d');

    $summary_type = request('summary_type') ?? null;

    $space_id = session('space_id') ?? null;
    if(is_null($space_id)){
        abort(403);
    }
@endphp
<x-dynamic-component :component="'layouts.' . $layout">
    <div class="py-12">
        <div class="max-w-7xl my-10 mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-white">
                    <h3 class="text-3xl font-bold dark:text-white">Rangkuman Mutasi Stok</h3>
                    <div class="flex justify-between items-center m-4 border-solid border-2 dark:border-gray-700">
                        <form action="{{ route('supplies.summary') }}" method="GET">
                            <div class="grid grid-cols-4 border-solid border-2">
                                <x-div.box-input label="summary_type" class="m-4">
                                    <select name="summary_type" id="summary_type">
                                        @foreach($data->summary_types as $key => $value)
                                            <option value="{{ $key }}" {{ $key == $summary_type ? 'selected' : '' }}>{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </x-div.box-input>
                                <x-div.box-input label="Start Date" class="m-4">
                                    <x-input.input-basic type="date" name="start_date" value="{{ $start_date }}"></x-input.input-basic>
                                </x-div.box-input>
                                <x-div.box-input label="End Date" class="m-4">
                                    <x-input.input-basic type="date" name="end_date" value="{{ $end_date }}" required></x-input.input-basic>
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
                            // Transaction;
                            $spaces_per_id = $spaces->groupBy('id');
                            $txs_per_space = $txs->groupBy('space_id');
                            $spaces_data = $data->spaces_data;                        
                            $items_data = $data->items_data;
                        }
                    @endphp

                    @switch($summary_type)
                        @case('stockflow')
                            @include('primary.inventory.supplies.partials.summary-txs')
                        @break
                        @case('stockflow_items')
                            @include('primary.inventory.supplies.partials.summary-items')
                        @break
                    @endswitch

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
