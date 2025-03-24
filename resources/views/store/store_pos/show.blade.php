@php
    $layout = session('layout');

    $payment_methods = $payment_methods;
    if($store_pos->payment_method) {
        $payment_method = $payment_methods->where('id', $store_pos->payment_method)->first();
    }
@endphp

<x-dynamic-component :component="'layouts.' . $layout">
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-white">
                    <h1 class="text-2xl font-bold mb-6">Store POS Details: {{ $store_pos->number }}</h1>
                    <div class="mb-3 mt-1 flex-grow border-t border-gray-500 dark:border-gray-700"></div>

                    <h3 class="text-lg font-bold my-3">Store POS Details</h3>
                    <div class="grid grid-cols-3 sm:grid-cols-3 gap-6 mb-6">
                        <x-div-box-show title="Store POS Number">{{ $store_pos->number ?? 'N/A' }}</x-div-box-show>
                        <x-div-box-show title="Store Customer">{{ $store_pos->store_customer->customer->id ?? 'N/A' }} : {{ $store_pos->store_customer->customer->name ?? 'N/A' }}</x-div-box-show>
                        <x-div-box-show title="Store POS Date">{{ $store_pos->date?->format('Y-m-d') ?? 'N/A' }}</x-div-box-show>
                        
                        <x-div-box-show title="Store Cashier">{{ $store_pos->store_employee?->employee->companyuser->user->name ?? 'N/A' }}</x-div-box-show>
                        <x-div-box-show title="Payment Method">{{ $payment_method?->name ?? 'N/A' }}</x-div-box-show>
                        <x-div-box-show title="Total Amount">Rp{{ number_format($store_pos->total_amount ?? 0, 2) }}</x-div-box-show>

                        <x-div-box-show title="Status">
                            <p
                                class="text-lg font-medium inline-block bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">
                                {{ $store_pos->status ?? 'N/A' }}
                            </p>
                        </x-div-box-show>
                        <x-div-box-show title="Notes">{{ $store_pos->notes ?? 'N/A' }}</x-div-box-show>
                    </div>
                    <div class="mb-3 mt-1 flex-grow border-t border-gray-500 dark:border-gray-700"></div>


                    <h3 class="text-lg font-bold mt-6">Products</h3>
                    <x-table-table id="search-table">
                        <x-table-thead>
                            <tr>
                                <x-table-th>#</x-table-th>
                                <x-table-th>Produk</x-table-th>
                                <x-table-th>Harga Barang</x-table-th>
                                <x-table-th>Qty</x-table-th>
                                <x-table-th>Discount (%)</x-table-th>
                                <x-table-th>Subtotal</x-table-th>
                                <x-table-th>Notes</x-table-th>
                                <x-table-th>Action</x-table-th>
                            </tr>
                        </x-table-thead>
                        <x-table-tbody>
                            @foreach ($store_pos->store_pos_products as $index => $store_product)
                                <x-table-tr>
                                    <x-table-td>{{ $index + 1 }}</x-table-td>
                                    <x-table-td>{{ $store_product->id }} : {{ $store_product->store_product->product?->name ?? 'N/A' }}</x-table-td>
                                    <x-table-td>{{ $store_product->store_product->store_price }}</x-table-td>
                                    <x-table-td>{{ $store_product->quantity }}</x-table-td>
                                    <x-table-td>{{ $store_product->discount ?? '0' }}%</x-table-td>
                                    <x-table-td>{{ $store_product->subtotal }}</x-table-td>
                                    <x-table-td>{{ $store_product->notes ?? 'N/A' }}</x-table-td>
                                    <x-table-td>
                                        <div class="flex items-center space-x-2">
                                        </div>
                                    </x-table-td>
                                </x-table-tr>
                            @endforeach
                        </x-table-tbody>
                    </x-table-table>
                    <div class="mb-3 mt-1 flex-grow border-t border-gray-500 dark:border-gray-700"></div>

                    
                    <!-- Action Section -->
                    <h3 class="text-lg font-bold my-3">Actions</h3>
                    @if ($store_pos->status == 'PAID')
                    <div class="mb-4 flex justify">
                        <div class="m-4">
                            <a href="{{ route('store_pos.print', $store_pos->id) }}" 
                                target="_blank" class="bg-green-500 text-white px-4 py-2 rounded-lg">
                                🖨️ Cetak Struk
                            </a>
                        </div>
                        <div class="m-4">
                            <a href="https://wa.me/{{ $store_pos->store_customer?->customer->phone_number }}?text={{ urlencode('Halo, ini struk transaksi Anda. Total: Rp' . $store_pos->total_amount) }}"
                                target="_blank" class="bg-blue-500 text-white px-4 py-2 rounded-lg">
                                📩 Kirim WhatsApp
                            </a>
                        </div>
                    </div>
                    @endif

                    <div class="my-6 flex-grow border-t border-gray-500 dark:border-gray-700"></div>

                    <!-- Back Button -->
                    <x-secondary-button>
                        <a href="{{ route('store_pos.index') }}">Back to List</a>
                    </x-secondary-button>
                </div>
            </div>
        </div>
    </div>
</x-dynamic-component>

