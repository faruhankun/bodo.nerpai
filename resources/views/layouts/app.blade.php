<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
	<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
		<nav class="navbar navbar-expand-lg navbar-light bg-light">
			<div class="container">
				<a class="navbar-brand" href="{{ route('dashboard') }}">Dashboard</a>
				<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-icon"></span>
				</button>
				<div class="collapse navbar-collapse" id="navbarNav">
					<ul class="navbar-nav">
						<li class="nav-item">
							<a class="nav-link" href="{{ route('purchases.index') }}">Purchases</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="{{ route('suppliers.index') }}">Suppliers</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="{{ route('customers.index') }}">Customers</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="{{ route('products.index') }}">Products</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="{{ route('warehouses.index') }}">Warehouses</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="{{ route('inventory.index') }}">Inventory</a>
						</li>
						<li>
							<a class="nav-link" href="{{ route('sales.index') }}">Sales</a>
						</li>
						<li>
							<a class="nav-link" href="{{ route('inbound_requests.index') }}">Inbound Requests</a>
						</li>
						<li>
							<a class="nav-link" href="{{ route('outbound_requests.index') }}">Outbound Requests</a>
						</li>
					</ul>
				</div>
			</div>
		</nav>


        @yield('content')
    </div>
</body>
</html>

