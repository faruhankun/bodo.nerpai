@php
    $space_id = session('space_id') ?? null;
    $layout = session('layout');
@endphp
<x-dynamic-component :component="'layouts.' . $layout">
    <div class="py-12">
        <div class=" sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-white">
                    <h3 class="text-2xl dark:text-white font-bold">Add Journal Entry</h3>
                    <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>

                    <form action="{{ route('journal_accounts.store') }}" method="POST" onsubmit="return validateForm()">
                        @csrf

                        <input type="hidden" name="space_id" value="{{ $space_id }}">

                        <div class="grid grid-cols-2 sm:grid-cols-2 gap-6 mb-6">
                            <div class="form-group">
                                <x-input-label for="store_cashier">Maintainer</x-input-label>
                                <input type="text" name="store_cashier" id="store_cashier"
                                    class="bg-gray-100 w-full px-4 py-2 border rounded-md focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:text-white"
                                    value="{{ auth()->user()->player->name ?? 'N/A' }}" required readonly disabled>
                            </div>

                            <div class="form-group">
                                <x-input-label for="date">Transaction Date</x-input-label>
                                <input type="date" name="date"
                                    class="bg-gray-100 w-full px-4 py-2 border rounded-md focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:text-white"
                                    required value="{{ date('Y-m-d') }}">
                            </div>

                            <div class="form-group col-span-2">
                                <x-input-label for="description">Description</x-input-label>
                                <x-input-textarea name="description" class="form-control"></x-input-textarea>
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
                                    <!-- Journal Entry Details rows will be dynamically added -->
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
                            <x-primary-button>Create Journal Entry</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Journal Entry Detail Row Script -->
    <script>
        $(document).ready(function() {
            let detailIndex = 0;

            // Add Journal Entry Detail row
            $("#add-detail").click(function() {
                detailIndex++;
                let newRow =
                    `<tr class="detail-row">
                        <x-table.table-td class="mb-2">${detailIndex}</x-table.table-td>
                        <x-table.table-td>
                            <x-input-select name="journal_entry_details[${detailIndex}][account_id]" class="account-select my-3" required>
                                <option value="">Select Account</option>
                                <!-- Assuming these account options exist in the server-side -->
                                @foreach ($accountsp as $account)
                                    <option value="{{ $account->id }}">{{ $account->name }}</option>
                                @endforeach
                            </x-input-select>
                        </x-table.table-td>
                        <x-table.table-td>
                            <x-input-input type="number" name="journal_entry_details[${detailIndex}][debit]" class="debit-input" value="0" required min="0">
                            </x-input-input>
                        </x-table.table-td>
                        <x-table.table-td>
                            <x-input-input type="number" name="journal_entry_details[${detailIndex}][credit]" class="credit-input" value="0" required min="0">
                            </x-input-input>
                        </x-table.table-td>
                        <x-table.table-td>
                            <x-input-input type="text" name="journal_entry_details[${detailIndex}][notes]" class="notes-input"></x-input-input>
                        </x-table.table-td>
                        <x-table.table-td>
                            <button type="button" class="bg-red-500 text-sm text-white px-4 py-1 rounded-md hover:bg-red-700 remove-detail">Remove</button>
                        </x-table.table-td>
                    </tr>`;
                $("#journal-detail-list").append(newRow);
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

            // Initial call to add a journal entry detail row
            $("#add-detail").trigger("click");
        });

        function validateForm() {
            // Your validation logic here
            let isValid = true;

            // cek apakah setiap baris, debit/creditnya tidak sama 0
            let totalDebit = 0;
            $(".debit-input").each(function() {
                if ($(this).val() == 0) {
                    if ($(this).closest("tr").find(".credit-input").val() == 0) {
                        alert("Debit and Credit must not be zero!");

                        isValid = false;
                        return false;
                    }
                }

                totalDebit += parseFloat($(this).val()) || 0;
            });

            if ($("#total-debit").text() != $("#total-credit").text()) {
                alert("Total Debit and Credit must be equal!");

                isValid = false;
            }

            if (totalDebit == 0) {
                alert("Debit and Credit must not be zero!");

                isValid = false;
            }

            return isValid; // Return true if the form is valid, false otherwise
        }
    </script>
</x-dynamic-component>
