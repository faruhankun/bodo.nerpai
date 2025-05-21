<x-company-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-white">
                    <h1 class="text-2xl font-bold mb-6">Piutang Customer Details : {{ $customer->name }}</h1>
                    <div class="mb-3 mt-1 flex-grow border-t border-gray-300 dark:border-gray-700"></div>

                    <h3 class="text-lg font-bold my-3">General Informations</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-6">
                        <x-div-box-show title="Name">{{ $customer->name }}</x-div-box-show>
                        <x-div-box-show title="Status">{{ ucfirst($customer->status) }}</x-div-box-show>
                        <x-div-box-show title="Entity">{{ $customer?->entity_type ?? 'N/A' }} : {{ $customer->entity?->name ?? 'N/A' }}</x-div-box-show>
                        <x-div-box-show title="Admin Notes">{{ $customer->notes ?? 'N/A' }}</x-div-box-show>
                    </div>
                    <div class="mb-3 mt-1 flex-grow border-t border-gray-300 dark:border-gray-700"></div>
                    
                    <!-- Invoice Section -->
                    <h3 class="text-lg font-bold my-3">Receivables</h3>
                    <div class="overflow-x-auto">
                        @php 
                            $unpaid_receivables = $customer->receivables->where('status', '!=', 'paid');
                            $unconfirmed_receivables = $customer->receivables->where('status', 'unconfirmed');
                        @endphp
                        <x-table.table-table id="search-table">
                            <x-table.table-thead>
                                <tr class>
                                    <x-table.table-th>Id</x-table.table-th>
                                    <x-table.table-th>Invoice Number</x-table.table-th>
                                    <x-table.table-th>Date</x-table.table-th>
                                    <x-table.table-th>Due Date</x-table.table-th>
                                    <x-table.table-th>Total Amount</x-table.table-th>
                                    <x-table.table-th>Amount Paid</x-table.table-th>
                                    <x-table.table-th>AR Status</x-table.table-th>
                                    <x-table.table-th>AR Notes</x-table.table-th>
                                    <x-table.table-th>Actions</x-table.table-th>
                                </tr>
                            </x-table.table-thead>
                            <x-table.table-tbody>
                                @foreach ($unpaid_receivables as $receivable)
                                    <x-table.table-tr>
                                        <x-table.table-td>{{ $receivable->id }}</x-table.table-td>
                                        <x-table.table-td>{{ $receivable->invoice?->number }}</x-table.table-td>
                                        <x-table.table-td>{{ $receivable->invoice?->date?->format('Y-m-d') ?? 'N/A' }}</x-table.table-td>
                                        <x-table.table-td>{{ $receivable->invoice->due_date?->format('Y-m-d') ?? 'N/A' }}</x-table.table-td>
                                        <x-table.table-td>Rp{{ number_format($receivable->total_amount, 2) }}</x-table.table-td>
                                        <x-table.table-td>Rp{{ number_format($receivable->balance, 2) }}</x-table.table-td>
                                        <x-table.table-td>{{ $receivable->status }}</x-table.table-td>
                                        <x-table.table-td>{{ $receivable->notes }}</x-table.table-td>
                                        <x-table.table-td>
                                            <div class="flex space-x-2">
                                                <x-button-show :route="route('sale_invoices.show', $receivable->invoice->id)" />
                                            </div>
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
                        @if ($unconfirmed_receivables->count() > 0)
                        <div class="flex justify mt-4">
                            <form action="{{ route('receivables.action', ['id' => $customer->id, 'action' => 'PAYMENT_REQUEST']) }}" method="POST">
                                @csrf
                                @method('POST')
                                <x-primary-button type="submit">Buat Payment untuk Receivables Unpaid</x-primary-button>
                            </form>
                        </div>
                        @elseif ($customer->status == 'SO_REQUEST')
                        <div class="flex justify-end m-4">
                        <form action="{{ route('receivables.action', ['id' => $customer->id, 'action' => 'SO_CONFIRMED']) }}" method="POST">
                                @csrf
                                @method('POST')
                                <x-primary-button type="submit">Input Invoice untuk Customer</x-primary-button>
                            </form>
                        </div>
                        @endif

                        @if ($customer->status == 'SO_CONFIRMED')
                        <div class="flex justify mt-4">
                            <form action="{{ route('receivables.action', ['id' => $customer->id, 'action' => 'SO_DP_CONFIRMED']) }}" method="POST">
                                @csrf
                                @method('POST')
                                <x-primary-button type="submit">Konfirmasi Pembayaran/DP Lunas dari Customer</x-primary-button>
                            </form>
                        </div>
                        @endif

                        @if ($customer->status == 'SO_DP_CONFIRMED')
                        <div class="flex justify mt-4">
                            <form action="{{ route('receivables.action', ['id' => $customer->id, 'action' => 'SO_OUTBOUND_REQUEST']) }}" method="POST">
                                @csrf
                                @method('POST')
                                <x-primary-button type="submit">Request Outbound Gudang</x-primary-button>
                            </form>
                        </div>
                        @endif

                        @if ($customer->status == 'SO_OUTBOUND_REQUEST')
                        <div class="flex justify mt-4">
                            <form action="{{ route('receivables.action', ['id' => $customer->id, 'action' => 'SO_PAYMENT_COMPLETION']) }}" method="POST">
                                @csrf
                                @method('POST')
                                <x-primary-button type="submit">Koordinasi Pembayaran Pelunasan</x-primary-button>
                            </form>
                        </div>
                        @endif

                        @if ($customer->status == 'SO_PAYMENT_COMPLETION')
                        <div class="flex justify mt-4">
                            <form action="{{ route('receivables.action', ['id' => $customer->id, 'action' => 'SO_COMPLETED']) }}" method="POST">
                                @csrf
                                @method('POST')
                                <x-primary-button type="submit">Completed Piutang Customer Order</x-primary-button>
                            </form>
                        </div>
                        @endif
                    </div>

                    <div class="my-6 flex-grow border-t border-gray-500 dark:border-gray-700"></div>



                    <div class="flex justify-end space-x-4">
                        <x-secondary-button>
                            <a href="{{ route('receivables.index') }}">Back to List</a>
                        </x-secondary-button>
                        @if($customer->status == 'SO_OFFER' || $customer->status == 'SO_REQUEST')
                        <x-button href="{{route('receivables.edit', $customer->id)}}" text="Edit Piutang Customer"
                            class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 dark:bg-green-700 dark:hover:bg-green-800">Edit Piutang Customer</x-button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
</x-company-layout>