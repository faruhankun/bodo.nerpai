@extends('layouts.app')

@section('content')
    <h1>Create Sales Order</h1>

    <form action="{{ route('sales.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="customer_id">Select Customer</label>
            <select name="customer_id" id="customer_id" class="form-control" required>
                @foreach($customers as $customer)
                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="sale_date">Sale Date</label>
            <input type="date" name="sale_date" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="warehouse_id">Select Warehouse</label>
            <select name="warehouse_id" class="form-control" required>
                @foreach($warehouses as $warehouse)
                    <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                @endforeach
            </select>
        </div>

        <h3>Products</h3>
        <div id="product-section">
            <div class="form-group">
                <label for="products[0][product_id]">Product</label>
                <select name="products[0][product_id]" class="form-control" required>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }} - ${{ $product->price }}</option>
                    @endforeach
                </select>
                <label for="products[0][quantity]">Quantity</label>
                <input type="number" name="products[0][quantity]" class="form-control" required>
                <label for="products[0][price]">Price</label>
                <input type="number" step="0.01" name="products[0][price]" class="form-control" required>
                <label for="products[0][note]">Note</label>
                <textarea name="products[0][note]" class="form-control"></textarea>
            </div>
        </div>
        <button type="button" id="add-product" class="btn btn-secondary mb-3">Add Another Product</button>
		
		<br>
        <button type="submit" class="btn btn-primary">Create Sale</button>
        <a href="{{ route('sales.index') }}" class="btn btn-secondary">Cancel</a>
    </form>

    <script>
        let productIndex = 1;
        document.getElementById('add-product').addEventListener('click', function () {
            const productSection = document.getElementById('product-section');
            const newProduct = `
                <div class="form-group">
                    <label for="products[${productIndex}][product_id]">Product</label>
                    <select name="products[${productIndex}][product_id]" class="form-control" required>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }} - ${{ $product->price }}</option>
                        @endforeach
                    </select>
                    <label for="products[${productIndex}][quantity]">Quantity</label>
                    <input type="number" name="products[${productIndex}][quantity]" class="form-control" required>
                    <label for="products[${productIndex}][price]">Price</label>
                    <input type="number" step="0.01" name="products[${productIndex}][price]" class="form-control" required>
                    <label for="products[${productIndex}][note]">Note</label>
                    <textarea name="products[${productIndex}][note]" class="form-control"></textarea>
                </div>
            `;
            productSection.insertAdjacentHTML('beforeend', newProduct);
            productIndex++;
        });
    </script>
@endsection
