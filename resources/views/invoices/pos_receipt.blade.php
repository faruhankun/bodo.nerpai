<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pembelian</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border-bottom: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f8f8f8; }
    </style>
</head>
<body>
    <h2>Struk Pembelian</h2>
    <p><strong>Pelanggan:</strong> {{ $store_pos->store_customer?->customer->name ?? 'N/A' }}</p>
    <p><strong>Tanggal:</strong> {{ $store_pos->created_at->format('d-m-Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>Produk</th>
                <th>Qty</th>
                <th>Harga</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($store_pos->store_pos_products as $store_pos_product)
                <tr>
                    <td>{{ $store_pos_product->store_product?->product->name }}</td>
                    <td>{{ $store_pos_product->quantity }}</td>
                    <td>Rp{{ number_format($store_pos_product->price, 0, ',', '.') }}</td>
                    <td>Rp{{ number_format($store_pos_product->subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h3>Total: Rp{{ number_format($store_pos->total_amount, 0, ',', '.') }}</h3>
</body>
</html>
