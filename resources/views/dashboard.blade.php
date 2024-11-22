@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <h1>Dashboard</h1>
    <p>Welcome to your business management app. Use the links below to navigate to different sections:</p>

    <ul class="list-group">
        <li class="list-group-item">
            <a href="{{ route('purchases.index') }}">Manage Purchases</a>
        </li>
        <li class="list-group-item">
            <a href="{{ route('suppliers.index') }}">Manage Suppliers</a>
        </li>
        <li class="list-group-item">
            <a href="{{ route('customers.index') }}">Manage Customers</a>
        </li>
        <li class="list-group-item">
            <a href="{{ route('products.index') }}">Manage Products</a>
        </li>
        <li class="list-group-item">
            <a href="{{ route('warehouses.index') }}">Manage Warehouses</a>
        </li>
        <li class="list-group-item">
            <a href="{{ route('inventory.index') }}">Manage Inventory</a>
        </li>
		<li class="list-group-item">
            <a href="{{ route('sales.index') }}">Manage Sales</a>
        </li>
    </ul>
@endsection
