
@php 
    $payment_methods = $payment_methods;
    if($payment->payment_method) {
        $payment_method = $payment_methods->where('id', $payment->payment_method)->first();
    }
@endphp

<x-company-layout>
    <div class="py-12">
        <div class=" sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-white">
                    <h1 class="text-2xl font-bold mb-6">Payment Details : {{ $payment->number }}</h1>
                    <div class="mb-3 mt-1 flex-grow border-t border-gray-300 dark:border-gray-700"></div>

                    <h3 class="text-lg font-bold my-3">General Informations</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-6">
                        <x-div-box-show title="Number">{{ $payment->number }}</x-div-box-show>
                        <x-div-box-show title="Date">{{ $payment->date?->format('Y-m-d') }}</x-div-box-show>
                        <x-div-box-show title="Type">{{ $payment->type }}</x-div-box-show>
                        <x-div-box-show title="Payment Method">{{ $payment_method?->name ?? 'N/A' }}</x-div-box-show>
                        <x-div-box-show title="Total Amount">Rp{{ number_format($payment->total_amount, 2) }}</x-div-box-show>
                        <x-div-box-show title="Source">{{ $payment?->source_type ?? 'N/A' }} : 
                                                        {{ $payment->source?->name ?? $payment->source?->number ?? 'N/A' }}</x-div-box-show>
                        <x-div-box-show title="Status">{{ ucfirst($payment->status) }}</x-div-box-show>
                        <x-div-box-show title="Notes">{{ $payment->notes ?? 'N/A' }}</x-div-box-show>
                    </div>
                    <div class="mb-3 mt-1 flex-grow border-t border-gray-300 dark:border-gray-700"></div>
                    
                    <!-- Payment List Section -->
                    <h3 class="text-lg font-bold my-3">Payment List</h3>
                    <div class="overflow-x-auto">
                        <x-table.table-table id="search-table">
                            <x-table.table-thead>
                                <tr class>
                                    <x-table.table-th>Id</x-table.table-th>
                                    <x-table.table-th>Invoice</x-table.table-th>
                                    <x-table.table-th>Date</x-table.table-th>
                                    <x-table.table-th>Due Date</x-table.table-th>
                                    <x-table.table-th>Total Amount</x-table.table-th>
                                    <x-table.table-th>Amount Paid</x-table.table-th>
                                    <x-table.table-th>Status</x-table.table-th>
                                    <x-table.table-th>Notes</x-table.table-th>
                                    <x-table.table-th>Actions</x-table.table-th>
                                </tr>
                            </x-table.table-thead>
                            <x-table.table-tbody>
                                @foreach ($payment->payment_details as $detail)
                                    <x-table.table-tr>
                                        <x-table.table-td>{{ $detail->id }}</x-table.table-td>
                                        <x-table.table-td>{{ $detail->invoice_type }} : {{ $detail->invoice?->number }}</x-table.table-td>
                                        <x-table.table-td>{{ $detail->invoice?->date?->format('Y-m-d') ?? 'N/A' }}</x-table.table-td>
                                        <x-table.table-td>{{ $detail->invoice?->due_date?->format('Y-m-d') ?? 'N/A' }}</x-table.table-td>
                                        <x-table.table-td>Rp{{ number_format($detail->amount, 2) }}</x-table.table-td>
                                        <x-table.table-td>Rp{{ number_format($detail->balance, 2) }}</x-table.table-td>
                                        <x-table.table-td>{{ $detail->invoice?->status }}</x-table.table-td>
                                        <x-table.table-td>{{ $detail->invoice?->notes }}</x-table.table-td>
                                        <x-table.table-td>
                                            <!-- <div class="flex space-x-2">
                                                <x-button-show :route="route('purchase_invoices.show', $detail->invoice?->id)" />
                                            </div> -->
                                        </x-table.table-td>
                                    </x-table.table-tr>
                                @endforeach
                            </x-table.table-tbody>
                        </x-table.table-table>
                    </div>
                    <div class="my-6 flex-grow border-t border-gray-500 dark:border-gray-700"></div>



                    <!-- Action Section -->
                    <h3 class="text-lg font-bold my-3">Actions</h3>
                    <div>
                        @if ($payment->status == 'PYM_PENDING')
                        <div class="flex justify mt-4">
                            <form action="{{ route('payments.action', ['id' => $payment->id, 'action' => 'PAYMENT_PROCESS']) }}" method="POST">
                                @csrf
                                @method('POST')
                                <x-primary-button type="submit">Konfirmasi Process Payment </x-primary-button>
                            </form>
                        </div>
                        @elseif ($payment->status == 'PYM_PROCESS')
                            <form action="{{ route('payments.action', ['id' => $payment->id, 'action' => 'PAYMENT_PAID']) }}" method="POST">
                                @csrf
                                @method('POST')

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
                                    <div class="form-group">
                                        <div class="border rounded-lg p-4 mt-4">
                                            <h2 class="text-lg font-bold mb-2">Metode Pembayaran</h2>
                                            <x-input-select name="payment_method" id="payment-method" class="form-select w-full px-2 py-1" required>
                                                <option value="">-- Select Payment Method --</option>
                                                @foreach ($payment_methods as $method)
                                                    <option value="{{ $method->id }}">{{ $method->name }}</option>
                                                @endforeach
                                            </x-input-select>

                                            @if ($payment->source->account)
                                                <div>
                                                    <br>
                                                    <label for="account" class="block font-bold">Account: {{ $payment->source->account->name }} </label>
                                                </div>
                                            @endif

                                            <div>
                                                <br>
                                                <label for="payment_amount" class="block font-bold">Jumlah Tagihan: Rp{{ number_format($payment->total_amount, 2) }}</label> 
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="border rounded-lg p-4 mt-4" id="payment-details">
                                            <h2 class="text-lg font-bold mb-2">Detail Pembayaran</h2>

                                            <!-- Upload Bukti Transfer -->
                                            <div id="transfer-proof">
                                                <label for="payment-proof" class="block font-bold">Upload Bukti Transfer</label>
                                                <input type="file" id="payment-proof" name="payment_proof" class="form-input w-full px-2 py-1">
                                            </div>    
                                        </div>
                                    </div>
                                </div>

                                <div class="flex justify-end mt-4">
                                    <x-primary-button type="submit">Konfirmasi Pembayaran</x-primary-button>
                                </div>
                            </form>
                        @endif
                    </div>

                    <div class="my-6 flex-grow border-t border-gray-500 dark:border-gray-700"></div>



                    <div class="flex justify-end space-x-4">
                        <x-secondary-button>
                            <a href="{{ route('payments.index') }}">Back to List</a>
                        </x-secondary-button>
                        @if($payment->status == 'PENDING')
                        <x-button href="{{route('payments.edit', $payment->id)}}" text="Edit Payment"
                            class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 dark:bg-green-700 dark:hover:bg-green-800">Edit Payment</x-button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
</x-company-layout>