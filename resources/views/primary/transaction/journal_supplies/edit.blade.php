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
                                        <x-table-th>Account</x-table-th>
                                        <x-table-th>Debit</x-table-th>
                                        <x-table-th>Credit</x-table-th>
                                        <x-table-th>Notes</x-table-th>
                                        <x-table-th>Action</x-table-th>
                                    </tr>
                                </x-table-thead>
                                <x-table-tbody id="journal-detail-list">
                                    @foreach ($journal->details as $index => $detail)
                                        <tr class="detail-row">
                                            <x-table-td class="mb-2">{{ $index + 1 }}</x-table-td>
                                            <x-table-td>
                                                <x-input-select
                                                    name="details[{{ $index }}][detail_id]"
                                                    class="account-select my-3" required>
                                                    <option value="">Select Account</option>
                                                    @foreach ($inventories as $account)
                                                        <option value="{{ $account->id }}"
                                                            {{ $detail->detail_id == $account->id ? 'selected' : '' }}>
                                                            {{ $account->name }}
                                                        </option>
                                                    @endforeach
                                                </x-input-select>
                                            </x-table-td>
                                            <x-table-td>
                                                <x-input-input type="number"
                                                    name="details[{{ $index }}][debit]"
                                                    class="debit-input"
                                                    value="{{ old('details.' . $index . '.debit', $detail->debit) }}"
                                                    required min="0">
                                                </x-input-input>
                                            </x-table-td>
                                            <x-table-td>
                                                <x-input-input type="number"
                                                    name="details[{{ $index }}][credit]"
                                                    class="credit-input"
                                                    value="{{ old('details.' . $index . '.credit', $detail->credit) }}"
                                                    required min="0">
                                                </x-input-input>
                                            </x-table-td>
                                            <x-table-td>
                                                <x-input-input type="text"
                                                    name="details[{{ $index }}][notes]"
                                                    class="notes-input"
                                                    value="{{ old('details.' . $index . '.notes', $detail->notes) }}">
                                                </x-input-input>
                                            </x-table-td>
                                            <x-table-td>
                                                <button type="button"
                                                    class="bg-red-500 text-sm text-white px-4 py-1 rounded-md hover:bg-red-700 remove-detail">Remove</button>
                                            </x-table-td>
                                        </tr>
                                    @endforeach
                                </x-table-tbody>
                            </x-table-table>

                            <div class="mb-4">
                                <x-button2 type="button" id="add-detail" class="mr-3 m-4">Add Journal
                                    Detail</x-button2>
                            </div>


                            <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>


                            <!-- <div class="flex justify-end space-x-4">
                                <p class="text-lg font-semibold text-end"><strong>Total Debit:</strong> Rp <span
                                        id="total-debit">0</span></p>
                                <p class="text-lg font-semibold text-end"><strong>Total Credit:</strong> Rp <span
                                        id="total-credit">0</span></p>
                            </div>
                            <p class="text-lg font-semibold text-end text-red-600"><span
                                        id="debit-credit"></span></p> -->

                                        
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

    <!-- Journal Detail Row Script -->
    <script>
        $(document).ready(function() {
            // Journal
            let journal = {!! json_encode($journal) !!};
            
            let sentTime = '{{ $journal->sent_time->format('Y-m-d') }}';
            $("#edit_sent_time").val(sentTime);
            $("#edit_handler_notes").val(journal.handler_notes);


            // Details
            let detailIndex = {{ $journal->details->count() }};

            // Add Journal Detail row
            $("#add-detail").click(function() {
                detailIndex++;
                let newRow =
                    `<tr class="detail-row">
                        <x-table-td class="mb-2">${detailIndex}</x-table-td>
                        <x-table-td>
                            <x-input-select name="details[${detailIndex}][detail_id]" class="account-select my-3" required>
                                <option value="">Select Account</option>
                                @foreach ($inventories as $account)
                                    <option value="{{ $account->id }}">{{ $account->name }}</option>
                                @endforeach
                            </x-input-select>
                        </x-table-td>
                        <x-table-td>
                            <x-input-input type="number" name="details[${detailIndex}][debit]" class="debit-input" value="0" required min="0">
                            </x-input-input>
                        </x-table-td>
                        <x-table-td>
                            <x-input-input type="number" name="details[${detailIndex}][credit]" class="credit-input" value="0" required min="0">
                            </x-input-input>
                        </x-table-td>
                        <x-table-td>
                            <x-input-input type="text" name="details[${detailIndex}][notes]" class="notes-input"></x-input-input>
                        </x-table-td>
                        <x-table-td>
                            <button type="button" class="bg-red-500 text-sm text-white px-4 py-1 rounded-md hover:bg-red-700 remove-detail">Remove</button>
                        </x-table-td>
                    </tr>`;
                $("#journal-detail-list").append(newRow);
            });

            // Remove Journal Detail row
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
            // updateTotals();
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
