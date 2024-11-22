@extends('layouts.app')

@section('title', 'Customer List')

@section('content')
<h1>Customer List</h1>
<a href="{{ route('customers.create') }}" class="btn btn-primary mb-3">Add New Customer</a>
<table class="table table-bordered">
    <thead class="thead-dark">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone Number</th>
            <th>Address</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($customers as $customer)
        <tr>
            <td>{{ $customer->id }}</td>
            <td>{{ $customer->name }}</td>
            <td>{{ $customer->email }}</td>
            <td>{{ $customer->phone_number }}</td>
            <td>{{ $customer->address }}</td>
            <td>{{ $customer->status }}</td>
            <td>
                <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-warning">Edit</a>
                <form action="{{ route('customers.destroy', $customer->id) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection