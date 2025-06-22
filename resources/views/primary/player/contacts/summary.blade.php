@php
    use Carbon\Carbon;

    $layout = session('layout');

    $end_date = request('end_date') ?? now()->format('Y-m-d');
    
    $start_date = Carbon::parse($end_date)->startOfMonth()->format('Y-m-d');
    if(request()->has('start_date')) {
        $start_date = request('start_date');    
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
                    <h3 class="text-3xl font-bold dark:text-white">Rangkuman Contacts</h3>


                    <!-- Modals  -->
                    <div id="react-contacts-modal" data-id="" data-start_date="" data-end_date="" data-account_data></div>

                    <!-- <x-primary-button onclick="show_account_modal(1290)">Open modal 1290</x-primary-button> -->

                    
                    <div class="flex justify-between items-center m-4 border-t dark:border-gray-700">    
                        <form action="{{ route('contacts.summary') }}" method="GET" id="summary-form">
                            <input type="hidden" name="space_id" value="{{ $space_id }}">

                            <div class="grid grid-cols-4 border-solid">
                                <x-div.box-input label="Tipe Laporan" class="m-4">
                                    <select name="summary_type" id="summary_type">
                                        <option value="">-- Tipe Laporan --</option>
                                        @foreach($data->summary_types as $key => $value)
                                            <option value="{{ $key }}" {{ $key == $summary_type ? 'selected' : '' }}>{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </x-div.box-input>
                                <x-div.box-input label="Filter" class="m-4">
                                    <x-primary-button class="ml-4">Filter</x-primary-button>
                                </x-div.box-input>
                            </div>
                        </form>

                        <x-div.box-input label="Export" class="m-4">
                            <x-secondary-button id="export_pdf_btn">PDF</x-secondary-button>
                            <x-secondary-button id="export_excel_btn">Excel</x-secondary-button>
                        </x-div.box-input>
                    </div>

                    <!-- Filter -->
                    <h3 class="text-2xl font-bold text-2xl text-xl dark:text-white">Filter</h3>
                    <!-- export import  -->


                    
                    <!-- Rangkuman -->
                    <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>
                    <div id="summary-result">
                        <x-table.table-table id="summary-table">
                            <x-table.table-thead id="summary-thead"></x-table.table-thead>
                            <x-table.table-tbody id="summary-tbody"></x-table.table-tbody>
                        </x-table.table-table>
                        <br>
                        <x-table.table-table id="summary-table-2">
                            <x-table.table-thead id="summary-thead-2"></x-table.table-thead>
                            <x-table.table-tbody id="summary-tbody-2"></x-table.table-tbody>
                        </x-table.table-table>
                        <br>
                        <x-table.table-table id="summary-table-3">
                            <x-table.table-thead id="summary-thead-3"></x-table.table-thead>
                            <x-table.table-tbody id="summary-tbody-3"></x-table.table-tbody>
                        </x-table.table-table>

                        <div id="summary-footer"></div>
                    </div>
                    @switch($summary_type)
                        @case('balance_sheet')
                            @include('primary.contacts.contacts.partials.balance_sheet')
                        @break
                        @case('cashflow')
                            @include('primary.contacts.contacts.partials.cashflow')
                        @break
                        @case('profit_loss')
                            @include('primary.contacts.contacts.partials.profit_loss')
                        @break
                    @endswitch


                    <div name="modals">
                        @include('primary.player.contacts.show')
                    </div>


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
    $(document).ready(function() {
        // exim
        $('#export_pdf_btn').on('click', function(e) {
            e.preventDefault();
            exim.exportTableToPDF('summary-table', $('#summary_type').val(), $('#summary-title').text())
        });

        $('#export_excel_btn').on('click', function(e) {
            e.preventDefault();
            exim.exportTableToExcel('summary-table', $('#summary-title').text() + '.xlsx')
        });
    });



    // form
    $('#summary_type').on('change', function () {
        $('#summary-form').submit();
    });


    $('#summary-form').on('submit', function (e) {
        e.preventDefault();

        show_summary();
    });


    function show_summary(query = ''){
        let form = $('#summary-form');
        let formUrl = form.attr('action');
        let formData = form.serialize();
        formData += `&query=summary&byProvince=1` + query;

        
        $.ajax({
            url: formUrl,
            method: 'GET',
            data: formData,
            success: function (response) {
                console.log('ajax:', response);
                
                print_province_tabel(response.byProvince);
                
                if(response.byRegency != null){
                    let response_html = '<pre>' + JSON.stringify(response.byRegency, null, 2) + '</pre>';
                    // $('#summary-footer').html(response_html);
                    print_regency_tabel(response);
                }

                if(response.data != null){
                    print_contact_tabel(response);
                }
            },
            error: function (xhr) {
                $('#summary-result').html(xhr.responseText);
                console.log(xhr.responseText);
            }
        });
    }


    function show_regency(province = ''){
        show_summary(`&province=${province}` + `&byRegency=1`, province);
    }

    function show_regency_detail(province = '', regency = ''){
        show_summary(`&province=${province}&regency=${regency}&address=${regency}` + `&byRegency=1`);
    }


    function print_province_tabel(data){
        // Set the table header
        $('#summary-thead').html(`
            <tr>
                <th>No</th>
                <th>Province</th>
                <th>Total</th>
                <th>Regency</th>
                <th>Detail</th>
                <th>Actions</th>
            </tr>
        `);

        // Generate rows
        let rows = '';
        data.forEach((item, index) => {
            let province = item.province || '';

            rows += `
                <tr>
                    <td>${index + 1}</td>
                    <td>${item.province || 'null'}</td>
                    <td>${item.total}</td>
                    <td>${item.regency || ''}</td>
                    <td>
                        <div id="detail-${item.province}"></div>
                    </td>
                    <td>
                        <a href="javascript:void(0)" onclick="show_regency('${province}');">Detail</a>
                    </td>
                </tr>
            `;
        });

        // Fill the tbody
        $('#summary-tbody').html(rows);
    }

    function print_regency_tabel(data){
        let list = data.byRegency;
        let province = data.province || '';

        // Set the table header
        $('#summary-thead-2').html(`
            <tr>
                <th>No</th>
                <th>Regency</th>
                <th>Total</th>
                <th>Detail</th>
                <th>Actions</th>
            </tr>
        `);

        // Generate rows
        let rows = '';
        list.forEach((item, index) => {
            let regency = item.regency || '';
            
            rows += `
                <tr>
                    <td>${index + 1}</td>
                    <td>${item.regency || 'null'}</td>
                    <td>${item.total}</td>
                    <td>
                    </td>
                    <td>
                        <a href="javascript:void(0)" onclick="show_regency_detail('${province}', '${regency}');">Detail</a>
                    </td>
                </tr>
            `;
        });

        // Fill the tbody
        $('#summary-tbody-2').html(rows);
    }

    function print_contact_tabel(data){
        let list = data.data;

        // Set the table header
        $('#summary-thead-3').html(`
            <tr>
                <th>No</th>
                <th>ID</th>
                <th>Nama</th>
                <th>Address Detail</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Notes</th>
                <th>Actions</th>
            </tr>
        `);

        // Generate rows
        let rows = '';
        list.forEach((item, index) => {
            rows += `
                <tr>
                    <td>${index + 1}</td>
                    <td>${item.id || 'null'}</td>
                    <td>${item.name || 'null'}</td>
                    <td>${item.address_detail || 'null'}</td>
                    <td>${item.email || 'null'}</td>
                    <td>${item.phone_number || 'null'}</td>
                    <td>${item.notes || ''}</td>
                    <td>
                        <a href="javascript:void(0)" onclick="showjs(decodeURIComponent('${encodeURIComponent(JSON.stringify(item))}'));">Detail</a>
                    </td>
                </tr>
            `;
        });

        // Fill the tbody
        $('#summary-tbody-3').html(rows);
    }

    function showjs(param){
        let data = JSON.parse(param);
        // console.log(param);

        let trigger = 'show_modal_js';
        let html = '<pre>' + JSON.stringify(data, null, 2) + '<br><br>';
        $('#dataform_' + trigger).html(html);

        window.dispatchEvent(new CustomEvent('open-' + trigger));
    }


    function show_money(data, decimals = 2) {
        return new Intl.NumberFormat('id-ID', { 
            maximumFractionDigits: decimals
        }).format(data);
    }


    function show_account_modal(acc){
        if(acc.id == null){
            return;
        }

        const container = document.getElementById('react-account-modal');
        
        container.setAttribute('data-id', acc.id);
        container.setAttribute('data-start_date', $('#start_date').val());
        container.setAttribute('data-end_date', $('#end_date').val());
        container.setAttribute('data-account_data', JSON.stringify(acc));

        console.log(acc);

        let summary_type = $('#summary_type').val();
        if(summary_type == 'balance_sheet'){
            container.setAttribute('data-start_date', '');
        }

        window.dispatchEvent(new CustomEvent('showAccountModal'));
    }
</script>

@vite('resources/js/app.jsx')
