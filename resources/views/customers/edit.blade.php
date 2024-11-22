@extends('layouts.app')

@section('title', 'Edit Customer')

@section('content')
    <h1>Edit Customer</h1>
    <form action="{{ route('customers.update', $customer->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="name">Customer Name</label>
            <input type="text" name="name" class="form-control" value="{{ $customer->name }}" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" class="form-control" value="{{ $customer->email }}" required>
        </div>
        <div class="form-group">
            <label for="phone_number">Phone Number</label>
            <input type="text" name="phone_number" class="form-control" value="{{ $customer->phone_number }}" required>
        </div>
        <div class="form-group">
            <label for="address">Address</label>
            <input type="text" name="address" class="form-control" value="{{ $customer->address }}">
        </div>
        <div class="form-group">
            <label for="status">Status</label>
            <select name="status" class="form-control">
                <option value="Active" {{ $customer->status == 'Active' ? 'selected' : '' }}>Active</option>
                <option value="Inactive" {{ $customer->status == 'Inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
        <div class="form-group">
            <label for="notes">Notes</label>
            <textarea name="notes" class="form-control" rows="3">{{ $customer->notes }}</textarea>
        </div>
        <button type="submit" class="btn btn-primary">Update Customer</button>
        <a href="{{ route('customers.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
@endsection