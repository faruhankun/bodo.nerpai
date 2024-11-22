@extends('layouts.app')

@section('content')
    <h1>Sales Order Details</h1>

    <div class="mb-4">
        <h3>General Information</h3>
        <p><strong>Customer Name:</strong> {{ $sale->customer->name }}</p>
        <p><strong>Sale Date:</strong> {{ $sale->sale_date }}</p>
        <p><strong>Warehouse:</strong> {{ $sale->warehouse->name }}</p>
        <p><strong>Status:</strong> {{ ucfirst($sale->status) }}</p>
        <p><strong>Customer Notes:</strong> {{ $sale->customer_notes ?? 'N/A' }}</p>
        <p><strong>Admin Notes:</strong> {{ $sale->admin_notes ?? 'N/A' }}</p>
    </div>

    <div class="mb-4">
        <h3>Products</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Note</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->products as $product)
                    <tr>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->pivot->quantity }}</td>
                        <td>${{ number_format($product->pivot->price, 2) }}</td>
                        <td>{{ $product->pivot->note ?? 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mb-4">
        <h3>Expedition Details</h3>
        <p><strong>Expedition:</strong> {{ $sale->expedition->name }}</p>
        <p><strong>Estimated Shipping Fee:</strong> ${{ number_format($sale->estimated_shipping_fee, 2) }}</p>
        <p><strong>Packing Fee:</strong> ${{ number_format($sale->packing_fee, 2) }}</p>
        <p><strong>Real Volume:</strong> {{ $sale->real_volume ?? 'N/A' }}</p>
        <p><strong>Real Weight:</strong> {{ $sale->real_weight ?? 'N/A' }}</p>
        <p><strong>Tracking Number:</strong> {{ $sale->tracking_number ?? 'N/A' }}</p>
    </div>

    <div class="mb-4">
        <h3>Complaint Details</h3>
        <p>{{ $sale->complaint_details ?? 'No complaints reported.' }}</p>
    </div>

    <a href="{{ route('sales.index') }}" class="btn btn-secondary">Back to Sales Orders</a>
    <a href="{{ route('sales.edit', $sale->id) }}" class="btn btn-primary">Edit Sales Order</a>
@endsection
