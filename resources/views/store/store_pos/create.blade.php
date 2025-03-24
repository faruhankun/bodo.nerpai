<x-store-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-white">
                    <div class="p-6 text-gray-900 dark:text-white">
                        <h3 class="text-2xl dark:text-white font-bold">Add POS Sales</h3>
                        <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>

                        <form action="{{ route('store_pos.store') }}" method="POST">
                            @csrf

                            <div class="grid grid-cols-2 sm:grid-cols-2 gap-6 mb-6">                        
                                <div class="form-group">
                                    <x-input-label for="store_customer_id">Select Customer</x-input-label>
                                    <select name="store_customer_id" id="store_customer_id"
                                        class="bg-gray-100 w-full px-4 py-2 border rounded-md focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:text-white"
                                        >
                                        <option value="">-- Select Customer --</option>
                                        @foreach($store_customers as $store_customer)
                                            <option value="{{ $store_customer->id }}">{{ $store_customer->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <x-input-label for="date">POS Date</x-input-label>
                                    <input type="date" name="date"
                                        class="bg-gray-100 w-full px-4 py-2 border rounded-md focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:text-white"
                                        required value="{{ date('Y-m-d') }}">
                                </div>

                                <x-div-box-show title="Store Cashier">{{ $store_employee?->employee->companyuser->user->name ?? 'N/A' }}</x-div-box-show>

                                <div class="form-group">
                                    <x-input-label for="notes">Notes</x-input-label>
                                    <x-input-textarea name="notes" class="form-control"></x-input-textarea>
                                </div>
                            </div>

                            <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>

                            <div class="container">
                                <!-- Tabel Produk yang Dibeli -->
                                <x-table-table id="productTable">
                                    <x-table-thead>
                                        <tr>
                                            <x-table-th>No</x-table-th>
                                            <x-table-th>Produk</x-table-th>
                                            <x-table-th>Harga Barang</x-table-th>
                                            <x-table-th>Qty</x-table-th>
                                            <x-table-th>Discount (%)</x-table-th>
                                            <x-table-th>Subtotal</x-table-th>
                                            <x-table-th>Notes</x-table-th>
                                            <x-table-th>Action</x-table-th>
                                        </tr>
                                    </x-table-thead>
                                    <x-table-tbody id="item-list">
                                        
                                    </x-table-tbody>
                                </x-table-table>
                                <div class="mb-4">
                                    <x-button2 type="button" id="add-item" class="mr-3 m-4">Add Another Product</x-button2>
                                </div>

                                <!-- Total Berat & Total Amount -->
                                <div class="mt-4 text-right border-t border-gray-300 dark:border-gray-700">
                                    <p class="text-lg font-semibold"><strong>Total Weight:</strong> <span id="total-weight">0</span> kg</p>
                                    <p class="text-lg font-semibold"><strong>Total Amount:</strong> Rp <span id="total-amount">0</span></p>
                                </div>
                            </div>
                            <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>
                            
                            <!-- <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
                                <div class="form-group">
                                    <div class="border rounded-lg p-4 mt-4">
                                        <h2 class="text-lg font-bold mb-2">Metode Pembayaran</h2>
                                        <x-input-select name="payment_method" id="payment-method" class="form-select w-full px-2 py-1" required>
                                            <option value="">-- Select Payment Method --</option>
                                            <option value="CASH">💵 Cash</option>
                                            <option value="TF_BANK">🏦 Transfer Bank</option>
                                            <option value="QRIS">📱 QRIS</option>
                                            <option value="CARD">💳 Debit / Kredit</option>
                                        </x-input-select>

                                        <div>
                                            <br>
                                            <label for="payment_amount" class="block font-bold">Jumlah Pembayaran</label>
                                            <x-input-input type="number" name="payment_amount" id="payment_amount" class="form-input w-full px-2 py-1" min="0"></x-input-input>    
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="border rounded-lg p-4 mt-4" id="payment-details">
                                        <h2 class="text-lg font-bold mb-2">Detail Pembayaran</h2>

                                        <div id="cash-payment" class="hidden">
                                            <label for="amount-paid" class="block font-bold">Jumlah Uang</label>
                                            <input type="number" id="amount-paid" class="form-input w-full px-2 py-1" min="0">
                                            <label for="change" class="block font-bold mt-2">Kembalian</label>
                                            <input type="text" id="change" class="form-input w-full px-2 py-1 bg-gray-200" readonly>
                                        </div>

                                        <div id="transfer-proof" class="hidden">
                                            <label for="payment-proof" class="block font-bold">Upload Bukti Transfer</label>
                                            <input type="file" id="payment-proof" class="form-input w-full px-2 py-1">
                                        </div>    
                                    </div>
                                </div>
                            </div> -->

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

                                        <div>
                                            <br>
                                            <label for="payment_amount" class="block font-bold">Jumlah Tagihan: </label> 
                                            <x-input-input type="number" name="payment_amount" id="payment_amount" class="form-input w-full px-2 py-1" min="0"></x-input-input>
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

                            <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>


                            <div class="m-4 flex justify-end">
                                <a href="{{ route('store_pos.index') }}">
                                    <x-secondary-button type="button">Cancel</x-secondary-button>
                                </a>
                                <x-primary-button>Create POS Sales</x-primary-button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!-- Product -->    
<script>
    $(document).ready(function() {
        let productIndex = 0;

        $("#add-item").click(function() {
            productIndex++;
            let newRow = `
                <tr class="item-row">
                    <x-table-td class="mb-2">${productIndex}</x-table-td>
                    <x-table-td class="mb-4">
                        <x-input-select name="products[${productIndex}][product_id]" class="item-select" required>
                            <option value="">Select Product</option>
                            @foreach ($store_products as $store_product)
                                <option data-price="{{ $store_product->store_product->store_price ?? 0 }}"
                                        data-weight="{{ $store_product->store_product->product->weight ?? 0 }}"
                                        data-cost="{{ $store_product->cost_per_unit ?? 0 }}"
                                        data-location="{{ $store_product->store_location_id }}"
                                        value="{{ $store_product->store_product->id }}">{{ $store_product->store_product->product->name }}
                                                : {{ $store_product->quantity }}pcs</option>
                            @endforeach
                        </x-input-select>
                    </x-table-td>
                    <x-table-td>
                        <x-input-input type="number" size="10" name="products[${productIndex}][price]" class="item-price" required></x-input-input>
                    </x-table-td>
                    <x-table-td>
                        <x-input-input type="number" name="products[${productIndex}][quantity]" class="item-quantity" placeholder="1" min="1" required>
                        </x-input-input>
                    </x-table-td>
                    <x-table-td>
                        <x-input-input type="number" name="products[${productIndex}][discount]" class="item-discount" placeholder="0" value="0"></x-input-input>
                    </x-table-td>
                    <x-table-td>
                        <x-input-input type="number" name="products[${productIndex}][subtotal]" class="item-subtotal" readonly></x-input-input>
                    </x-table-td>
                    <x-table-td>
                        <x-input-input type="text" name="products[${productIndex}][notes]" class="item-notes"></x-input-input>
                    </x-table-td>
                    <x-table-td>
                        <button type="button"
                            class="bg-red-500 text-sm text-white px-4 py-1 rounded-md hover:bg-red-700 remove-item">
                            Remove
                        </button>
                    </x-table-td>
                    <input type="hidden" class="item-weight" value="0"> <!-- Weight per unit -->
                    <input type="hidden" class="item-subweight" value="0"> <!-- Total weight per row -->
                    <input type="hidden" name="products[${productIndex}][cost_per_unit]" class="item-cost" value="0"> <!-- Cost per unit -->
                    <input type="hidden" name="products[${productIndex}][total_cost]" class="item-subcost" value="0">
                    <input type="hidden" name="products[${productIndex}][store_location_id]" class="item-location">
                </tr>
            `;
            $("#item-list").append(newRow);
        });

        $(document).on("click", ".remove-item", function() {
            $(this).closest("tr").remove();
            updateRowNumbers();
            updateTotal();
        });

        function updateRowNumbers() {
            $("#item-list tr").each(function(index) {
                $(this).find("td:first").text(index + 1);
            });
        }

        // Event: Pilih Produk, Harga Otomatis Terisi
        $(document).on("change", ".item-select", function() {
            let price = $(this).find("option:selected").data("price");
            let row = $(this).closest("tr");
            let weight = $(this).find("option:selected").data("weight");
            let cost = $(this).find("option:selected").data("cost");
            let location = $(this).find("option:selected").data("location");

            row.find(".item-weight").val(weight);
            row.find(".item-price").val(price);
            row.find(".item-cost").val(cost);
            row.find(".item-location").val(location);
            
            updateSubtotal(row);
        });

        // Event: Update Subtotal saat Qty atau Discount berubah
        $(document).on("input", ".item-quantity, .item-price, .item-discount", function() {
            let row = $(this).closest("tr");
            updateSubtotal(row);
        });

        function updateSubtotal(row) {
            let qty = parseFloat(row.find(".item-quantity").val()) || 0;
            let price = parseFloat(row.find(".item-price").val()) || 0;
            let discount = parseFloat(row.find(".item-discount").val()) || 0;
            let weight = parseFloat(row.find(".item-weight").val()) || 0;
            let cost = parseFloat(row.find(".item-cost").val()) || 0;

            let subtotal = (qty * price) * (1 - discount / 100);
            let subweight = (qty * weight);
            let subcost = (qty * cost);
            
            row.find(".item-subtotal").val(subtotal.toFixed(2));
            row.find(".item-subweight").val((subweight).toFixed(2));
            row.find(".item-subcost").val((subcost).toFixed(2));

            updateTotal();
        }

        function updateTotal() {
            let totalAmount = 0;
            let totalWeight = 0; // Asumsi tiap produk punya berat, bisa tambahin kolom kalau mau

            // calculate total weight
            $(".item-subweight").each(function() {
                totalWeight += parseFloat($(this).val()) / 1000 || 0;
            });

            $(".item-subtotal").each(function() {
                totalAmount += parseFloat($(this).val()) || 0;
            });

            $("#total-amount").text(totalAmount.toFixed(2));
            $("#total-weight").text(totalWeight.toFixed(2));
        }

        // initial
        $("#add-item").trigger("click");
    });
</script>

<!-- Script untuk menghitung kembalian otomatis -->
<script>
    $(document).ready(function() {
        $("#payment-method").change(function() {
            let selectedMethod = $(this).val();
            
            // $("#cash-payment, #transfer-proof").addClass("hidden");

            // if (selectedMethod === "CASH") {
            //     $("#cash-payment").removeClass("hidden");
            // } else if (selectedMethod === "TF_BANK" || selectedMethod === "QRIS") {
            //     $("#transfer-proof").removeClass("hidden");
            //     $("#amount-paid").val("");
            //     $("#change").val("");
            // }

            $("#payment_amount").val(parseFloat($("#total-amount").text()) || 0);
        });

        // Hitung kembalian otomatis
        $("#amount-paid").on("input", function() {
            let totalAmount = parseFloat($("#total-amount").text()) || 0;
            let amountPaid = parseFloat($(this).val()) || 0;
            let change = amountPaid - totalAmount;
            $("#change").val(change >= 0 ? change.toFixed(2) : "0.00");
        });
    });
</script>

</x-store-layout>