@php
    $layout = session('layout');

    $journal = $journal_entry;
    $space_id = $journal?->space_id ?? (session('space_id') ?? null);

@endphp
<x-dynamic-component :component="'layouts.' . $layout">
    <div class="py-12">
        <div class=" sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-white">
                    <h3 class="text-2xl dark:text-white font-bold">Edit Journal Entry: {{ $journal_entry->number }}</h3>
                    <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>

                    <form action="{{ route('journal_accounts.update', $journal_entry->id) }}" method="POST" onsubmit="return validateForm()">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-2 sm:grid-cols-2 gap-6 mb-6">
                            <div class="form-group">
                                <x-input-label for="store_cashier">Maintainer</x-input-label>
                                <input type="text" name="store_cashier" id="store_cashier"
                                    class="bg-gray-100 w-full px-4 py-2 border rounded-md focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:text-white"
                                    value="{{ $journal_entry->sender?->name ?? 'N/A' }}" required readonly disabled>
                            </div>

                            <div class="form-group">
                                <x-input-label for="date">Transaction Date</x-input-label>
                                <input type="date" name="sent_time"
                                    class="bg-gray-100 w-full px-4 py-2 border rounded-md focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:text-white"
                                    required value="{{ optional($journal_entry->sent_time)->format('Y-m-d') }}">
                            </div>

                            <div class="form-group col-span-2">
                                <x-input-label for="sender_notes">Description</x-input-label>
                                <x-input-textarea name="sender_notes" class="form-control"
                                    value="{{ $journal_entry->sender_notes }}">
                                </x-input-textarea>
                            </div>
                        </div>

                        <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>



                        <div class="container">
                            <!-- Journal Entry Details Table -->
                            <x-table.table-table id="journalDetailTable">
                                <x-table.table-thead>
                                    <tr>
                                        <x-table.table-th>No</x-table.table-th>
                                        <x-table.table-th>Account</x-table.table-th>
                                        <x-table.table-th>Debit</x-table.table-th>
                                        <x-table.table-th>Credit</x-table.table-th>
                                        <x-table.table-th>Notes</x-table.table-th>
                                        <x-table.table-th>Action</x-table.table-th>
                                    </tr>
                                </x-table.table-thead>
                                <x-table.table-tbody id="journal-detail-list">
                                    <!-- @foreach ($journal_entry->details as $index => $detail)
                                        <tr class="detail-row">
                                            <x-table.table-td class="mb-2">{{ $index + 1 }}</x-table.table-td>
                                            <x-table.table-td>
                                                <x-input-select
                                                    name="details[{{ $index }}][detail_id]"
                                                    class="account-select my-3" required>
                                                    <option value="">Select Account</option>
                                                    @foreach ($accountsp as $account)
                                                        <option value="{{ $account->id }}"
                                                            {{ $detail->detail_id == $account->id ? 'selected' : '' }}>
                                                            {{ $account->code }} - {{ $account->name }}
                                                        </option>
                                                    @endforeach
                                                </x-input-select>
                                            </x-table.table-td>
                                            <x-table.table-td>
                                                <x-input-input type="number"
                                                    name="details[{{ $index }}][debit]"
                                                    class="debit-input"
                                                    value="{{ old('details.' . $index . '.debit', $detail->debit) }}"
                                                    required min="0">
                                                </x-input-input>
                                            </x-table.table-td>
                                            <x-table.table-td>
                                                <x-input-input type="number"
                                                    name="details[{{ $index }}][credit]"
                                                    class="credit-input"
                                                    value="{{ old('details.' . $index . '.credit', $detail->credit) }}"
                                                    required min="0">
                                                </x-input-input>
                                            </x-table.table-td>
                                            <x-table.table-td>
                                                <x-input-input type="text"
                                                    name="details[{{ $index }}][notes]"
                                                    class="notes-input"
                                                    value="{{ old('details.' . $index . '.notes', $detail->notes) }}">
                                                </x-input-input>
                                            </x-table.table-td>
                                            <x-table.table-td>
                                                <button type="button"
                                                    class="bg-red-500 text-sm text-white px-4 py-1 rounded-md hover:bg-red-700 remove-detail">Remove</button>
                                            </x-table.table-td>
                                        </tr>
                                    @endforeach -->
                                </x-table.table-tbody>
                            </x-table.table-table>

                            <div class="mb-4">
                                <x-button2 type="button" id="add-detail" class="mr-3 m-4">Add Journal
                                    Detail</x-button2>
                            </div>


                            <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>
                            <div class="flex justify-end space-x-4">
                                <p class="text-lg font-semibold text-end"><strong>Total Debit:</strong> Rp <span
                                        id="total-debit">0</span></p>
                                <p class="text-lg font-semibold text-end"><strong>Total Credit:</strong> Rp <span
                                        id="total-credit">0</span></p>
                            </div>
                            <p class="text-lg font-semibold text-end text-red-600"><span
                                        id="debit-credit"></span></p>

                                        
                            <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>
                        </div>



                        <div class="m-4 flex justify-end space-x-4">
                            <a href="{{ route('journal_accounts.index') }}">
                                <x-secondary-button type="button">Cancel</x-secondary-button>
                            </a>
                            <x-primary-button>Update Journal Entry</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Journal Entry Detail Row Script -->
    <script>
        let detailIndex = 0;


        $(document).ready(function() {
            // Journal
            let journal = {!! json_encode($journal) !!};
            let journal_details = {!! json_encode($journal->details) !!};

            let sentTime = '{{ $journal->sent_time->format('Y-m-d') }}';
            $("#edit_sent_time").val(sentTime);
            $("#edit_handler_notes").val(journal.handler_notes);

            
            console.log("journal_details:", journal_details);

            // Details
            // let detailIndex = {{ $journal->details->count() }};
            journal_details.forEach(appendDetailRow);
        });


        function appendDetailRow(detail) {
            const $row = $(renderDetailRow(detail));
            $("#journal-detail-list").append($row);

            const $select = $row.find('.inventory-select');
            const selectedData = detail.detail_id && detail.detail ? {
                id: detail.detail_id,
                text: `${detail.detail.code} - ${detail.detail.name}`,
            } : null;

            console.log("selectedData:", selectedData);

            setTimeout(() => {
                initInventorySelect($select, selectedData);
            }, 0);
        }


        function initInventorySelect($element, selectedData = null) {
            $element.select2({
                placeholder: 'Search & Select Account',
                minimumInputLength: 2,
                width: '100%',
                height: '100%',
                padding: '20px',
                ajax: {
                    url: '/accountsp/data',
                    dataType: 'json',
                    paginate: true,
                    data: function(params) {
                        return {
                            q: params.term,
                            space_id: '{{ $space_id }}',
                            page: params.page || 1,
                            return: 'JSON'
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data.data.map(item => ({
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
                // $row.find('.cost_per_unit-input').val(selected.cost_per_unit || 0);
            });
        }


        function renderDetailRow(detail = {}) {
            const rowIndex = detailIndex++;
            const selectedId = detail.detail_id || '';
            // const quantity = detail.quantity || 0;
            // const selectedModel = detail.model_type || '';
            // const cost_per_unit = detail.cost_per_unit || 0;
            const debit = detail.debit || 0;
            const credit = detail.credit || 0;
            const notes = detail.notes || '';

            return `
                <tr class="detail-row">
                    <td class="mb-2">${rowIndex + 1}</td>
                    <td>
                        <select name="details[${rowIndex}][detail_id]" class="inventory-select w-20" required>
                            <option value="">Select Account</option>
                        </select>
                    </td>
                    <td>
                        <input type="text" size="5" name="details[${rowIndex}][debit]" class="debit-input w-25" value="${debit}" default="0" min="0">
                    </td>
                    <td>
                        <input type="text" size="5" name="details[${rowIndex}][credit]" class="credit-input w-25" value="${credit}" default="0" min="0">
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
    </script>
    
    
    <script>
        $(document).ready(function() {
            // let detailIndex = {{ $journal_entry->details->count() }};


            // Add Journal Detail row
            $("#add-detail").click(function() {
                let newRow = renderDetailRow();
                const $row = $(newRow);

                $("#journal-detail-list").append($row);

                initInventorySelect($row.find('.inventory-select'));
            });


            

            // Remove Journal Entry Detail row
            $(document).on("click", ".remove-detail", function() {
                $(this).closest("tr").remove();
                updateTotals();
            });

            // Update the total Debit and Credit
            $(document).on("input", ".debit-input", function() {
                $(this).closest("tr").find(".credit-input").val(0);
                updateTotals();
            });

            $(document).on("input", ".credit-input", function() {
                $(this).closest("tr").find(".debit-input").val(0);
                updateTotals();
            });

            function updateTotals() {
                let totalDebit = 0;
                let totalCredit = 0;

                $(".debit-input").each(function() {
                    totalDebit += parseFloat($(this).val()) || 0;
                });

                $(".credit-input").each(function() {
                    totalCredit += parseFloat($(this).val()) || 0;
                });

                $("#total-debit").text(totalDebit.toFixed(2).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,"));
                $("#total-credit").text(totalCredit.toFixed(2).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,"));

                if(totalDebit != totalCredit){
                    $("#debit-credit").text("Total Debit and Credit must be equal, diff: " + (totalDebit - totalCredit).toFixed(2).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,"));
                } else {
                    $("#debit-credit").text("");
                }
            }

            // Initialize totals with existing values
            updateTotals();
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

            if($("#total-debit").text() != $("#total-credit").text()){
                alert("Total Debit and Credit must be equal!");

                isValid = false;
            }

            if(totalDebit == 0){
                alert("Debit and Credit must not be zero!");

                isValid = false;
            }

            return isValid; // Return true if the form is valid, false otherwise
        }
    </script>
</x-dynamic-component>
