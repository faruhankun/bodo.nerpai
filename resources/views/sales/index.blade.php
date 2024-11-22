@extends('layouts.app')

@section('content')
    <h1>Sales Orders</h1>
    <a href="{{ route('sales.create') }}" class="btn btn-primary mb-3">Add New Sale</a>

	<form method="GET" action="{{ route('sales.index') }}">
		<select name="status" class="form-control mb-3">
			<option value="">All Statuses</option>
			<option value="Planned" {{ request('status') == 'Planned' ? 'selected' : '' }}>Planned</option>
			<option value="Unpaid" {{ request('status') == 'Unpaid' ? 'selected' : '' }}>Unpaid</option>
			<option value="Pending Shipment" {{ request('status') == 'Pending Shipment' ? 'selected' : '' }}>Pending Shipment</option>
			<option value="In Transit" {{ request('status') == 'In Transit' ? 'selected' : '' }}>In Transit</option>
			<option value="Received - Pending Verification" {{ request('status') == 'Received - Pending Verification' ? 'selected' : '' }}>Received - Pending Verification</option>
			<option value="Customer Complaint" {{ request('status') == 'Customer Complaint' ? 'selected' : '' }}>Customer Complaint</option>
			<option value="Completed" {{ request('status') == 'Completed' ? 'selected' : '' }}>Completed</option>
		</select>
		<button type="submit" class="btn btn-primary mb-3">Filter</button>
	</form>

    <table class="table table-bordered">
        <thead class="thead-dark">
            <tr>
                <th>Customer</th>
                <th>Sale Date</th>
                <th>Warehouse</th>
                <th>Total Amount</th>
				<th>Products</th>
				<th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sales as $sale)
                <tr>
					<td>{{ $sale->customer->name ?? "" }}</td>
                    <td>{{ $sale->sale_date }}</td>
                    <td>{{ $sale->warehouse->name }}</td>
                    <td>${{ $sale->total_amount }}</td>
					<td>
						<ul>
							@foreach ($sale->products as $product)
								<li>
									{{ $product->name }} - {{ $product->pivot->quantity }} pcs @ {{ $product->pivot->price }} ({{ $product->pivot->note ?? 'No Note' }})
								</li>
							@endforeach
						</ul>
					</td>
					<td>{{ $sale->status }}</td>
                    <td>
						<a href="{{ route('sales.show', $sale->id) }}" class="btn btn-info">Show</a>
                        <a href="{{ route('sales.edit', $sale->id) }}" class="btn btn-warning">Edit</a>
						@if ($sale->status == 'Planned')
							<form action="{{ route('sales.destroy', $sale->id) }}" method="POST" style="display:inline;">
								@csrf
								@method('DELETE')
								<button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
							</form>
						@endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
