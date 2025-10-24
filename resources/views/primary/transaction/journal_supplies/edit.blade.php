@php
    $layout = session('layout');

    $request = request();

    $space_id = get_space_id($request);
    $space_parent_id = session('space_parent_id') ?? null;
    if(is_null($space_id)){
        abort(403);
    }

    $player = session('player_id') ? \App\Models\Primary\Player::findOrFail(session('player_id')) : Auth::user()->player;

    $spaces_dest = $player?->spaces->where('id', '!=', $space_id) ?? [];
    $output_journal = $journal?->output;

    $spaces_origin = $player?->spaces->where('id', '!=', $space_id) ?? [];
    $input_journal = $journal?->input;

    $data_space_id = $journal->space_id;
    if(!$data_space_id){
        abort(404, 'Space id not found');
    }
@endphp


<x-dynamic-component :component="'layouts.' . $layout">
    <div class="py-12">
        <div class=" sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-white">
                    <h3 class="text-2xl dark:text-white font-bold">Edit Journal: {{ $journal->number }} in {{ $journal->space?->name ?? 'space-not-found' }}</h3>
                    <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>

                    <form action="{{ route('journal_supplies.update', $journal->id) }}" method="POST" onsubmit="return validateForm()">
                        @csrf
                        @method('PUT')


                        @include('primary.transaction.journal_supplies.partials.dataform', ['form' => ['id' => 'Edit Journal', 'mode' => 'edit'], 'data' => $journal])



                        <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>

                        <div class="container">
                            <!-- Journal Details Table -->
                            <x-table.table-table id="journalDetailTable">
                                <x-table.table-thead>
                                    <tr>
                                        <x-table.table-th>No</x-table.table-th>
                                        <x-table.table-th>Supply</x-table.table-th>
                                        <x-table.table-th>Qty</x-table.table-th>
                                        <x-table.table-th>Type</x-table.table-th>
                                        <x-table.table-th>Cost/Unit</x-table.table-th>
                                        <x-table.table-th>Notes</x-table.table-th>
                                        <x-table.table-th>Action</x-table.table-th>
                                    </tr>
                                </x-table.table-thead>
                                <x-table.table-tbody id="journal-detail-list">
                                </x-table.table-tbody>
                            </x-table.table-table>

                            <div class="mb-4">
                                <x-button2 type="button" id="add-detail" class="mr-3 m-4">Add Journal
                                    Detail</x-button2>
                            </div>

                                        
                            <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>
                        </div>



                        <!-- detail tambahan -->
                        <div class="grid grid-cols-3 sm:grid-cols-3 gap-6">
                            <x-div.box-input for="relation_id" title="Transaksi Terkait" label="Transaksi Terkait">
                                <select name="relation_id" id="edit_relation_id" class="w-full px-4 py-2 border rounded">
                                    <option value="">-- Select Trades --</option>
                                </select>
                                <label id="edit_relation_data" class="text-xs text-gray-500"></label>
                            </x-div.box-input>

                            <div class="form-group mb-4">
                                <x-input-label for="tags">Tags</x-input-label>
                                <x-input-textarea name="tags" id="edit_tags" class="w-full" placeholder="Optional Tags"></x-input-textarea>
                            </div>

                            <div class="form-group mb-4">
                                <x-input-label for="links">Links</x-input-label>
                                <x-input-textarea name="links" id="edit_links" class="w-full" placeholder="Optional Links"></x-input-textarea>
                            </div>

                            <!-- <div class="form-group mb-4">
                                <x-input-label for="notes">Notes</x-input-label>
                                <x-input-textarea name="notes" id="edit_notes" class="w-full" placeholder="Optional notes"></x-input-textarea>
                            </div> -->
                        </div>



                        <div class="m-4 flex justify-end space-x-4">
                            <a href="{{ route('journal_supplies.index') }}">
                                <x-secondary-button type="button">Cancel</x-secondary-button>
                            </a>
                            <x-primary-button>Update Journal</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Journal
            let journal = {!! json_encode($journal) !!};
            let journal_details = {!! json_encode($journal->details) !!};

            let sentTime = '{{ $journal->sent_time->format('Y-m-d') }}';
            $("#edit_sent_time").val(sentTime);
            $("#edit_handler_notes").val(journal.handler_notes);

            // Details
            let detailIndex = {{ $journal->details->count() }};
            journal_details.forEach(appendDetailRow);
            
            // setTimeout(() => {
            //     $('.inventory-select').each(function() {
            //         initInventorySelect($(this));
            //     });
            // }, 0);
        });
    </script>
    
    <!-- Rows -->
    <script>
        let detailIndex = 0;

        function formatNumberNoTrailingZeros(num, precision = 2) {
            return new Intl.NumberFormat('en-US', {
                useGrouping: false,
                maximumFractionDigits: precision
            }).format(num);
        }

        function renderDetailRow(detail = {}) {
            const rowIndex = detailIndex++;
            const selectedId = detail.detail_id || '';
            const quantity = detail.quantity || 0;
            const selectedModel = detail.model_type || '';
            const cost_per_unit = detail.cost_per_unit || 0;
            const notes = detail.notes || '';

            const model_types = @json($model_types);
            const modelTypeOptions = model_types.map(model_type => {
                const selected = model_type.id === selectedModel ? 'selected' : '';
                return `<option value="${model_type.id}" ${selected}>${model_type.name}</option>`;
            }).join('');

            return `
                <tr class="detail-row">
                    <td class="mb-2">${rowIndex + 1}</td>
                    <td>
                        <select name="details[${rowIndex}][detail_id]" class="inventory-select w-20" required>
                            <option value="">Select Inventory</option>
                        </select>
                    </td>
                    <td>
                        <input type="text" name="details[${rowIndex}][quantity]" class="quantity-input w-20" value="${formatNumberNoTrailingZeros(quantity)}">
                    </td>
                    <td>
                        <select name="details[${rowIndex}][model_type]" class="model_type-select type-select my-3" required>
                            <option value="">Select Type</option>
                            ${modelTypeOptions}
                        </select>
                    </td>
                    <td>
                        <input type="text" size="5" name="details[${rowIndex}][cost_per_unit]" class="cost_per_unit-input w-25" value="${formatNumberNoTrailingZeros(cost_per_unit)}" default="0" min="0">
                    </td>
                    <td>
                        <input type="text" name="details[${rowIndex}][notes]" class="notes-input" value="${notes}">
                    </td>
                    <td>
                        <button type="button" class="bg-red-500 text-sm text-white px-4 py-1 rounded-md hover:bg-red-700 remove-detail">Remove</button>
                    </td>
                </tr>
            `;
        }

        function initInventorySelect($element, selectedData = null) {
            $element.select2({
                placeholder: 'Search & Select Supply',
                width: '100%',
                height: '100%',
                padding: '20px',
                ajax: {
                    url: '/supplies/search',
                    dataType: 'json',
                    paginate: true,
                    data: function(params) {
                        return {
                            q: params.term,
                            space_id: '{{ $data_space_id }}',
                            page: params.page || 1
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data.map(item => ({
                                id: item.id,
                                text: item.text, // Ubah sesuai nama field yang kamu punya
                                cost_per_unit: item.cost_per_unit
                            }))
                        };
                    },
                    cache: true
                }
            });

            if (selectedData) {
                // Delay agar select2 benar-benar ter-attach
                const option = new Option(selectedData.text, selectedData.id, true, true);
                $element.append(option).trigger('change');
            }

            // Event setelah pilih inventory
            $element.on('select2:select', function(e) {
                const selected = e.params.data;
                const $row = $(this).closest('tr');
                $row.find('.cost_per_unit-input').val(selected.cost_per_unit || 0);
            });
        }

        function appendDetailRow(detail) {
            const $row = $(renderDetailRow(detail));
            $("#journal-detail-list").append($row);

            const $select = $row.find('.inventory-select');
            const selectedData = detail.detail_id && detail.detail ? {
                id: detail.detail_id,
                text: `${detail.detail.sku} - ${detail.detail.name} qty: ${detail.detail.balance} : ${detail.detail.notes}`,
            } : null;

            setTimeout(() => {
                initInventorySelect($select, selectedData);
            }, 0);
        }
    </script>

    <script>
        $(document).ready(function() { 
            // Add Journal Detail row
            $("#add-detail").click(function() {
                let newRow = renderDetailRow();
                const $row = $(newRow);

                $("#journal-detail-list").append($row);

                initInventorySelect($row.find('.inventory-select'));
            });

            // Remove Journal Detail row
            $(document).on("click", ".remove-detail", function() {
                $(this).closest("tr").remove();
            });
        });

        function validateForm() {
            // Your validation logic here
            let isValid = true;

            // cek apakah setiap baris, debit/creditnya tidak sama 0
            let totalDebit = 0;
            $(".debit-input").each(function() {
                if ($(this).val() == 0) {
                    if($(this).closest("tr").find(".credit-input").val() == 0){
                        alert("Debit and Credit must not be zero!");

                        isValid = false;
                        return false;
                    }
                }

                totalDebit += parseFloat($(this).val()) || 0;
            });

            // if(totalDebit == 0){
            //     alert("Debit and Credit must not be zero!");

            //     isValid = false;
            // }

            return isValid; // Return true if the form is valid, false otherwise
        }
    </script>

</x-dynamic-component>



<!-- fill data -->
<script>
    $(document).ready(function() {
        let journal = {!! json_encode($journal) !!};

        if(journal.relation){
            const option = new Option(journal.relation.number + ' : ' + journal.relation?.receiver?.name, journal.relation.id, true, true);
            $("#edit_relation_id").append(option).trigger('change');
            $('#edit_relation_data').html(journal.relation.number + ' : ' + journal.relation?.receiver?.name + ' (' + journal.relation.status + ' : ' + journal.relation.sent_time.split('T')[0] + ')');
        }
    });
</script>



<!-- edit relation trade -->
<script>
    $(document).ready(function() {
        let space_parent_id = '{{ $space_parent_id }}';

        $('#edit_relation_id').select2({
            placeholder: 'Search & Select Trade',
            width: '100%',
            ajax: {
                url: '/trades/data',
                dataType: 'json',
                paginate: true,
                delay: 500,
                data: function(params) {
                    return {
                        q: params.term,
                        return_type: 'json',
                        space: 'true',
                        space_id: '{{ $space_id }}',
                        page: params.page || 1,
                        model_type_select: 'search',
                        limit: 10,
                        orderby: 'sent_time',
                        orderdir: 'desc',
                        space_parent_id: space_parent_id,
                    };
                },
                processResults: function(result) {
                    console.log(result);
                    return {
                        results: result.map(item => ({
                            id: item.id,
                            text: (item.number || item.id) + ' : ' + item?.receiver?.name + ' (' + item.status + ' : ' + item.sent_time.split('T')[0] + ')',
                        }))
                    }
                },
                cache: true
            }
        });

        $('#edit_relation_id').on('select2:select', function(e) {
            const selected = e.params.data;
            console.log(selected);
            
            $('#edit_relation_data').html(selected.text);
        });
    });
</script>
