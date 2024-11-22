@extends('layouts.app')

@section('title', 'Create New Customer')

@section('content')
<h1>Create New Customer</h1>
<form action="{{ route('customers.store') }}" method="POST">
    @csrf
    <div class="form-group">
        <label for="name">Customer Name</label>
        <input type="text" name="name" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" name="email" class="form-control">
    </div>
    <div class="form-group">
        <label for="phone_number">Phone Number</label>
        <input type="text" name="phone_number" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="address">Address</label>
        <input name="address" class="form-control">
    </div>
    <div class="form-group">
        <label for="status">Status</label>
        <select name="status" class="form-control">
            <option value="Active">Active</option>
            <option value="Inactive">Inactive</option>
        </select>
    </div>
    <div class="form-group">
        <label for="notes">Note</label>
        <textarea name="notes" class="form-control"></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Create Customer</button>
</form>
@endsection