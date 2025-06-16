@php
    use Carbon\Carbon;

    $layout = session('layout');

    $real_start_date = request('start_date') ?? null;
    $end_date = request('end_date') ?? now()->format('Y-m-d');
    

    if(request()->has('start_date')) {
        $start_date = request('start_date');    
    } else {
        $start_date = Carbon::parse($end_date)->startOfMonth()->format('Y-m-d');
    }


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
                    <h3 class="text-3xl font-bold dark:text-white">Rangkuman Accounts</h3>


                    <!-- Modals  -->
                    @include('primary.inventory.accountsp.partials.summary-show')

                    <div id="react-account-modal" data-id="" data-start_date="{{ $start_date }}" data-end_date="{{ $end_date }}"></div>

                    <x-primary-button onclick="show_account_modal(1290)">Open modal 1290</x-primary-button>

                    
                    <div class="flex justify-between items-center m-4 border-t dark:border-gray-700">    
                        <form action="{{ route('accountsp.summary') }}" method="GET">
                            <div class="grid grid-cols-4 border-solid">
                                <x-div.box-input label="Tipe Laporan" class="m-4">
                                    <select name="summary_type" id="summary_type">
                                        <option value="">-- Tipe Laporan --</option>
                                        @foreach($data->summary_types as $key => $value)
                                            <option value="{{ $key }}" {{ $key == $summary_type ? 'selected' : '' }}>{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </x-div.box-input>
                                <x-div.box-input label="Start Date" class="m-4" id="start_date">
                                    <x-input.input-basic type="date" name="start_date" value="{{ $start_date }}"></x-input.input-basic>
                                </x-div.box-input>
                                <x-div.box-input label="End Date" class="m-4" id="end_date">
                                    <x-input.input-basic type="date" name="end_date" value="{{ $end_date }}" required></x-input.input-basic>
                                </x-div.box-input>
                                <x-div.box-input label="Filter" class="m-4">
                                    <x-primary-button class="ml-4">Filter</x-primary-button>
                                </x-div.box-input>
                            </div>
                        </form>

                        <!-- @if(!is_null($summary_type))
                            <x-div.box-input label="Export" class="m-4">
                                <x-secondary-button class="ml-4" id="exportVisibleBtn">Export</x-secondary-button>
                            </x-div.box-input>
                        @endif -->
                    </div>

                    <!-- Filter -->
                    <h3 class="text-2xl font-bold text-2xl text-xl dark:text-white">Filter</h3>
                    <!-- export import  -->


                    
                    <!-- Rangkuman -->
                    <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>
                    @switch($summary_type)
                        @case('balance_sheet')
                            @include('primary.inventory.accountsp.partials.balance_sheet')
                        @break
                        @case('cashflow')
                            @include('primary.inventory.accountsp.partials.cashflow')
                        @break
                        @case('profit_loss')
                            @include('primary.inventory.accountsp.partials.profit_loss')
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
        if (!datatable) {
            alert("Table not found.");
            return;
        }

        let csv = exim.simpleDTtoCSV(datatable);

        // Download
        let filename = "export-summary-{{ $summary_type }}-{{ $real_start_date }}-{{ $end_date }}.csv";
        exim.exportCSV(csv.join("\n"), filename);
    });


    function show_money(data, decimals = 2) {
        return new Intl.NumberFormat('id-ID', { 
            maximumFractionDigits: decimals
        }).format(data);
    }


    function show_account_modal(id){
        const container = document.getElementById('react-account-modal');
        container.setAttribute('data-id', id);
        container.setAttribute('data-start_date', $('#start_date').val());
        container.setAttribute('data-end_date', $('#end_date').val());

        window.dispatchEvent(new CustomEvent('showAccountModal'));
    }


    function show_details(data){
        console.log(data);


        // fill the table
        let detail_table = $('#search-table');
        detail_table.find('tbody').html('');

        const datatable = window.datatableInstances['search-table'];
        if (!datatable) {
            alert("Table not found.");
            return;
        }

        let balance = 0;
        data.forEach((detail) => {
            balance += detail.debit - detail.credit;

            // datatable.rows.add([
            //     detail.tx.sent_time.split('T')[0],
            //     detail.tx.number,
            //     detail.tx.sender_notes ?? 'N/A',
            //     detail.notes ?? 'N/A',
            //     show_money(detail.debit),
            //     show_money(detail.credit),
            //     show_money(balance),
            // ]);

            let row = `
                <tr>
                    <td>${(detail.tx.sent_time).split('T')[0]}</td>
                    <td>
                        <a href="/journal_accounts/${detail.tx.id}" target="_blank" class="text-blue-600 hover:text-red-600">
                            ${detail.tx.number}
                        </a>
                    </td>
                    <td>${detail.tx.sender_notes ?? 'N/A'}</td>
                    <td>${detail.notes ?? 'N/A'}</td>
                    <td>${show_money(detail.debit)}</td>
                    <td>${show_money(detail.credit)}</td>
                    <td>${show_money(balance)}</td>
                </tr>
            `;
            detail_table.find('tbody').append(row);
        });


        let form = document.getElementById('editDataForm');
        form.action = `/accountsp/summary`;


        // Dispatch event ke Alpine.js untuk membuka modal
        window.dispatchEvent(new CustomEvent('edit-modal-js'));
    }

    $('#summary_type').on('change', function () {
        $('#start_date').show();
        
        if (this.value == 'balance_sheet') {
            $('#start_date').hide();
        }
    });
</script>

@vite('resources/js/app.jsx')
