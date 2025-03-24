@extends('layouts.app')

@section('content')
<h2>Resolve Complaint #{{ $customerComplaint->id }}</h2>
<h4>Sales Order: #{{ $customerComplaint->salesOrder->id }}</h4>

<form action="{{ route('customer_complaints.resolve', $customerComplaint->id) }}" method="POST">
    @csrf
    @method('PUT')

    <table class="table">
        <thead>
            <tr>
                <th>Product</th>
                <th>Type</th>
                <th>Quantity</th>
                <th>Description</th>
                <th>Resolution</th>
                <th>Additional Info</th>
            </tr>
        </thead>
        <tbody>
            @foreach($customerComplaint->details as $detail)
            <tr>
                <td>{{ $detail->product->name }}</td>
                <td>{{ $detail->type }}</td>
                <td>{{ $detail->quantity }}</td>
                <td>{{ $detail->description }}</td>
                <td>
                    <select name="details[{{ $detail->id }}][action]" class="form-control" required>
                        <option value="">Select Resolution</option>
                        <option value="Resend">Resend</option>
                        <option value="ReturnForInspection">Return for Inspection</option>
                        <option value="Refund">Refund</option>
                        <option value="SellAtDifferentPrice">Sell at Different Price</option>
                    </select>
                </td>
                <td>
                    <input type="text" name="details[{{ $detail->id }}][additional_info]" class="form-control" placeholder="Notes or price (if applicable)">
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <button type="submit" class="btn btn-primary">Submit Resolutions</button>
</form>
@endsection
