<x-company-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-white"></div>
                <h3 class="text-lg font-bold dark:text-white mb-4">Log for Customer Complaints #{{ $sales->id }}</h3>
                <form action="{{ route('customer_complaints.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="sales_order_id" value="{{ $sales->id }}">

                    <div id="complaint-products">
                        <div class="product-complaint form-group">
                            <label>Product</label>
                            <select name="details[0][product_id]" class="form-control" required>
                                @foreach($productsWithReceivedQuantities as $product)
                                    <option value="{{ $product->id }}">
                                        {{ $product->name }} (Received: {{ $product->received_quantity }})
                                    </option>
                                @endforeach
                            </select>

                            <label>Complaint Type</label>
                            <select name="details[0][type]" class="form-control" required>
                                <option value="Damaged">Damaged</option>
                                <option value="Missing">Missing</option>
                                <option value="Excess">Excess</option>
                            </select>

                            <label>Quantity (if applicable)</label>
                            <input type="number" name="details[0][quantity]" class="form-control" min="1">

                            <label>Description</label>
                            <textarea name="details[0][description]" class="form-control"></textarea>
                        </div>
                    </div>

                    <button type="button" id="add-product" class="btn btn-secondary">Add Another Product</button>

                    <div class="form-group">
                        <label for="description">Complaint Description (if any)</label>
                        <textarea name="description" class="form-control"></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Submit Complaint</button>
                </form>

                <script>
                    let productIndex = 1;

                    document.getElementById('add-product').addEventListener('click', () => {
                        const container = document.getElementById('complaint-products');
                        const newProduct = container.firstElementChild.cloneNode(true);

                        Array.from(newProduct.querySelectorAll('input, select, textarea')).forEach(input => {
                            input.name = input.name.replace(/\d+/, productIndex);
                            input.value = '';
                        });

                        container.appendChild(newProduct);
                        productIndex++;
                    });
                </script>
            </div>
        </div>
    </div>
    </div>
</x-company-layout>