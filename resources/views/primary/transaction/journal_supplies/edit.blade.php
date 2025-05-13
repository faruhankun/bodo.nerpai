@php
    $layout = session('layout');

    $space_id = session('space_id') ?? null;
    if(is_null($space_id)){
        abort(403);
    }

    $player = session('player_id') ? \App\Models\Primary\Player::findOrFail(session('player_id')) : Auth::user()->player;

@endphp


<x-dynamic-component :component="'layouts.' . $layout">
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-white">
                    <h3 class="text-2xl dark:text-white font-bold">Edit Journal: {{ $journal->number }}</h3>
                    <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>

                    <form action="{{ route('journal_supplies.update', $journal->id) }}" method="POST" onsubmit="return validateForm()">
                        @csrf
                        @method('PUT')

                        @include('primary.transaction.journal_supplies.partials.dataform', ['form' => ['id' => 'Edit Journal', 'mode' => 'edit'], 'data' => $journal])

                        <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>

                        <div class="container">
                            <!-- Journal Details Table -->
                            <x-table-table id="journalDetailTable">
                                <x-table-thead>
                                    <tr>
                                        <x-table-th>No</x-table-th>
                                        <x-table-th>Supply</x-table-th>
                                        <x-table-th>Qty</x-table-th>
                                        <x-table-th>Type</x-table-th>
                                        <x-table-th>Cost/Unit</x-table-th>
                                        <x-table-th>Notes</x-table-th>
                                        <x-table-th>Action</x-table-th>
                                    </tr>
                                </x-table-thead>
                                <x-table-tbody id="journal-detail-list">
                                </x-table-tbody>
                            </x-table-table>

                            <div class="mb-4">
                                <x-button2 type="button" id="add-detail" class="mr-3 m-4">Add Journal
                                    Detail</x-button2>
                            </div>

                                        
                            <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>
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
                        <select name="details[${rowIndex}][detail_id]" class="inventory-select w-full" required>
                            <option value="">Select Inventory</option>
                        </select>
                    </td>
                    <td>
                        <input type="number" name="details[${rowIndex}][quantity]" class="quantity-input w-20" value="${quantity}">
                    </td>
                    <td>
                        <select name="details[${rowIndex}][model_type]" class="model_type-select type-select my-3" required>
                            <option value="">Select Type</option>
                            ${modelTypeOptions}
                        </select>
                    </td>
                    <td>
                        <input type="number" size="5" name="details[${rowIndex}][cost_per_unit]" class="cost_per_unit-input w-25" value="${cost_per_unit}" default="0" min="0">
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
                minimumInputLength: 2,
                width: '100%',
                padding: '0px 12px',
                ajax: {
                    url: '/supplies/search',
                    dataType: 'json',
                    data: function(params) {
                        return {
                            q: params.term,
                            space: '{{ $space_id }}',
                            page: params.page || 1
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data.map(item => ({
                                id: item.id,
                                text: item.text // Ubah sesuai nama field yang kamu punya
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
        }

        function appendDetailRow(detail) {
            const $row = $(renderDetailRow(detail));
            $("#journal-detail-list").append($row);

            const $select = $row.find('.inventory-select');
            const selectedData = detail.detail_id && detail.detail ? {
                id: detail.detail_id,
                text: `${detail.detail_id} - ${detail.detail.sku} - ${detail.detail.name} qty: ${detail.detail.balance} : ${detail.detail.notes}`,
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
