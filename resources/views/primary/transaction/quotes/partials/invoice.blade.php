<!-- resources/views/data/invoice.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $data->code ?? 'TX-'.$data->id }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        table, th, td { border: 1px solid #ccc; padding: 6px; }
        th { background-color: #f2f2f2; }
        .header { text-align: center; margin-bottom: 20px; }
        .total { font-weight: bold; text-align: right; }
        @media print {
            @page { size: A4; margin: 15mm; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>INVOICE Penawaran {{ $data->space->name ?? '-' }}</h2>
        <p>Penawarans: {{ $data->code ?? 'TX-'.$data->id }}</p>
    </div>

    <p><strong>Date:</strong> {{ $data->date }}</p>
    <p><strong>Space:</strong> {{ $data->space->name ?? '-' }}</p>
    <p><strong>Total Amount:</strong> Rp{{ number_format($data->total_amount, 2, ',', '.') }}</p>

    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th>Type</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Disc %</th>
                <th>Disc Value</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data->details as $detail)
            <tr>
                <td>{{ $detail->item->name ?? '-' }}</td>
                <td>{{ $detail->type }}</td>
                <td>{{ $detail->quantity }}</td>
                <td>{{ number_format($detail->price, 0, ',', '.') }}</td>
                <td>{{ $detail->discount }}</td>
                <td>{{ number_format($detail->disc_value, 0, ',', '.') }}</td>
                <td>{{ number_format($detail->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr>
                <td colspan="6" class="total">Total</td>
                <td>{{ number_format($data->total_amount, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <br><br>
    <p>Thank you for your purchase.</p>
</body>
</html>
