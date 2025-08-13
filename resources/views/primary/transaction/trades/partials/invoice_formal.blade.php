@php
    $logoPath = public_path('svg/' . $data->space?->name . '.svg');
    $logoUrl = file_exists($logoPath) ? asset('svg/' . $data->space?->name . '.svg') : asset('svg/hehe.svg');
@endphp


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice Pesanan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 13px;
            color: #000;
            margin: 40px;
        }

        .invoice-box {
            width: 100%;
        }

        .header {
            display: flex;
            justify-content: justify-between;
            align-data_products: start;
            padding: 10px;
        }

        .header img {
            width: 100px;
            margin-right: 20px;
        }

        .header .space-info {
            text-align: left;
            line-height: 0.6;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }

        table th, table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        table th {
            background-color: #f0f0f0;
        }

        .no-border td {
            border: none;
            padding: 4px;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .summary {
            width: 40%;
            float: right;
            margin-top: 10px;
        }

        .summary td {
            padding: 6px 8px;
        }
    </style>
</head>
<body>
    <div class="invoice-box">
        <!-- Header -->
        <div class="header">
            <img src="{{ $logoUrl }}" alt="Logo">
            <div class="space-info">
                <h1>{{ $data->space?->name }}</h1>
                <h3>{{ $data->space?->address ?? 'Space Address' }}</h3>
                <h3>{{ $data->space?->phone_number ?? 'Phone Number'}}</h3>
            </div>
        </div>

        <!-- Info Transaksi -->
        <table style="margin-top: 20px; text-align: center;">
            <tr>
                <th><strong>TGL TRANSAKSI</strong></th>
                <td>{{ $data->sent_time->format('Y-m-d') ?? today()->format('Y-m-d') }}</td>
                <th><strong>NO INVOICE</strong></th>
                <td>{{ $data->number ?? 'NO_INVOICE' }}</td>
            </tr>
            <tr>
                <th><strong>NAMA PEMBELI</strong></th>
                <td>{{ $data->receiver?->name ?? 'CUSTOMER NAME' }}</td>
                <th><strong>TOKO</strong></th>
                <td>{{ $data->space?->name ?? '-' }}</td>
            </tr>
            <tr>
                <th><strong>CATATAN</strong></th>
                <td colspan="3">{{ $data->receiver_notes ?? '' }}</td>
            </tr>
        </table>

        <!-- Produk -->
        <table style="margin-top: 20px;">
            <thead>
                <tr>
                    <th>SKU | NAMA PRODUK</th>
                    <th>QTY</th>
                    <th>BERAT</th>
                    <th>HARGA</th>
                    <th>DISC</th>
                    <th>Disc Value</th>
                    <th>TOTAL</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $total_weight = 0;
                    $total_price = 0;
                    $total_discount = 0;
                    $total_subtotal = 0;
                    $total_product = 0;
                @endphp
                @foreach($data->details as $detail)
                <tr>
                    <td>{{ $detail->detail?->sku }} | {{ $detail->detail?->name }}</td>
                    <td class="text-center">{{ $detail->quantity }}</td>
                    <td class="text-right">{{ number_format($detail->detail?->weight) }}</td>
                    <td class="text-right">{{ number_format($detail->detail?->price) }}</td>
                    <td class="text-right">{{ $detail->discount * 100 }} %</td>
                    <td class="text-right">{{ number_format($detail->discount * $detail->detail?->price) }}</td>
                    <td class="text-right">{{ number_format($detail->quantity * $detail->detail?->price * (1 - $detail->discount)) }}</td>
                </tr>

                    @php
                        $total_weight += $detail->detail?->weight * $detail->quantity;
                        $total_product += $detail->quantity * $detail->detail?->price;
                        $total_discount += $detail->discount * $detail->detail?->price * $detail->quantity;
                    @endphp
                @endforeach
                <!-- Total berat -->
                <tr>
                    <td colspan="2"></td>
                    <td class="text-right"><strong>{{ number_format($total_weight, 2) }}</strong></td>
                    <td colspan="4" class="text-right"><strong>{{ number_format($total_product - $total_discount, 2) }}</strong></td>
                </tr>
            </tbody>
        </table>

        <!-- Summary -->
        <table class="summary">
            <tr>
                <th><strong>TAGIHAN PRODUK</strong></th>
                <td class="text-right">{{ number_format($total_product, 2) }}</td>
            </tr>
            <tr>
                <th><strong>TOTAL DISKON</strong></th>
                <td class="text-right">{{ number_format($total_discount, 2) }}</td>
            </tr>

            <!-- <tr>
                <th><strong>BIAYA ONGKIR</strong></th>
                <td class="text-right">{{ number_format($data->shipping_cost) }}</td>
            </tr> -->

            @php
                $grand_total = $total_product - $total_discount;
            @endphp

            <tr>
                <th><strong>TOTAL TAGIHAN</strong></th>
                <th class="text-right"><strong>{{ number_format($grand_total) }}</strong></th>
            </tr>
        </table>
    </div>
</body>
</html>
