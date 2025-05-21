<x-company-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-white">
                    <h1 class="text-2xl font-bold mb-6">Sale Invoice Details : {{ $sale_invoice->number }}</h1>
                    <div class="mb-3 mt-1 flex-grow border-t border-gray-300 dark:border-gray-700"></div>

                    <h3 class="text-lg font-bold my-3">General Informations</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-6">
                        <x-div-box-show title="Invoice Number">{{ $sale_invoice->number }}</x-div-box-show>
                        <x-div-box-show title="Sale Number">{{ $sale_invoice->sale->number }}</x-div-box-show>
                        <x-div-box-show title="Date">{{ $sale_invoice->date?->format('Y-m-d') }}</x-div-box-show>
                        <x-div-box-show title="Due Date">{{ $sale_invoice->due_date?->format('Y-m-d') }}</x-div-box-show>
                        <x-div-box-show title="Status">
                            <p
                                class="text-lg font-medium inline-block bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">
                                {{ $sale_invoice->status }}
                            </p>
                        </x-div-box-show>
                        <x-div-box-show title="Admin Notes">{{ $sale_invoice->notes ?? 'N/A' }}</x-div-box-show>
                    </div>
                    <div class="mb-3 mt-1 flex-grow border-t border-gray-300 dark:border-gray-700"></div>

                    <!-- Invoice Section -->
                    @php 
                        $invoice_items = [
                            'cost_products' => 'HPP Produk', 
                            'vat_input' => 'PPN Masukan',
                            'cost_packing' => 'Biaya Packing',
                            'cost_insurance' => 'Biaya Asuransi',
                            'cost_freight' => 'Biaya Kiriman',
                        ];
                    @endphp
                    <h3 class="text-lg font-bold my-3">Invoices</h3>
                    <div class="overflow-x-auto">
                        <x-table.table-table>
                            <x-table.table-thead>
                                <x-table.table-tr>
                                    <x-table.table-th>No</x-table.table-th>
                                    <x-table.table-th>Name</x-table.table-th>
                                    <x-table.table-th class="text-right">Subtotal</x-table.table-th>
                                </x-table.table-tr>
                            </x-table.table-thead>
                            <x-table.table-tbody>
                                @foreach ($invoice_items as $key => $item)
                                    <x-table.table-tr>
                                        <x-table.table-td>#</x-table.table-td>
                                        <x-table.table-td>{{ $item }}</x-table.table-td>
                                        <x-table.table-td class="text-right">Rp{{ number_format($sale_invoice->$key, 2) }}</x-table.table-td>
                                    </x-table.table-tr>
                                @endforeach
                                <x-table.table-tr>
                                    <x-table.table-th colspan="2" class="font-bold text-xl">Total</x-table.table-th>
                                    <x-table.table-th class="text-right font-bold text-xl">Rp{{ number_format($sale_invoice->total_amount, 2) }}</x-table.table-th>
                                </x-table.table-tr>
                            </x-table.table-tbody>
                        </x-table.table-table>
                    </div>
                    <div class="my-6 flex-grow border-t border-gray-500 dark:border-gray-700"></div>

                    <!-- Action Section -->
                    <h3 class="text-lg font-bold my-3">Actions</h3>
                    <div>
                        @if ($sale_invoice->status == 'unconfirmed')
                        <div class="flex justify mt-4">
                            <form action="{{ route('sale_invoices.action', ['id' => $sale_invoice->id, 'action' => 'unpaid']) }}" method="POST">
                                @csrf
                                @method('POST')
                                <x-primary-button type="submit">Invoice Confirmed, Masukkan AP dan GL</x-primary-button>
                            </form>
                        </div>
                        @endif
                    </div>

                    <div class="my-6 flex-grow border-t border-gray-500 dark:border-gray-700"></div>

                    <div class="flex justify-end space-x-4">
                        <a href="{{ url()->previous() }}">
                            <x-secondary-button type="button">Cancel</x-secondary-button>
                        </a>
                        @if($sale_invoice->status == 'unconfirmed')
                        <x-button href="{{route('sale_invoices.edit', $sale_invoice->id)}}" text="Edit Sale Invoice"
                            class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 dark:bg-green-700 dark:hover:bg-green-800">Edit Sale Invoice</x-button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
</x-company-layout>