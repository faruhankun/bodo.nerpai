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
            align-store_pos_products: start;
            padding: 10px;
        }

        .header img {
            width: 100px;
            margin-right: 20px;
        }

        .header .store-info {
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
            <img src="{{ asset('svg/haebot.svg') }}" alt="Logo">
            <div class="store-info">
                <h1>{{ session('company_store_name'); }}</h1>
                <h3>{{ $store_pos->store?->address ?? 'Store Address' }}</h3>
                <h3>KONTAK: 085 246 428 746</h3>
            </div>
        </div>

        <!-- Info Transaksi -->
        <table style="margin-top: 20px; text-align: center;">
            <tr>
                <th><strong>TGL TRANSAKSI</strong></th>
                <td>{{ $store_pos->date->format('Y-m-d') ?? today()->format('Y-m-d') }}</td>
                <th><strong>NO INVOICE</strong></th>
                <td>{{ $store_pos->number ?? 'NO_INVOICE' }}</td>
            </tr>
            <tr>
                <th><strong>NAMA PEMBELI</strong></th>
                <td>{{ $store_pos->customer_name ?? 'CUSTOMER NAME' }}</td>
                <th><strong>TOKO</strong></th>
                <td>{{ $store_pos->store?->name ?? '-' }}</td>
            </tr>
            <tr>
                <th><strong>CATATAN</strong></th>
                <td colspan="3">{{ $store_pos->notes ?? '' }}</td>
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
                @foreach($store_pos->store_pos_products as $item)
                <tr>
                    <td>{{ $item->store_product?->product->sku }} | {{ $item->store_product?->product->name }}</td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">{{ number_format($item->store_product?->product->weight) }}</td>
                    <td class="text-right">{{ number_format($item->store_product?->store_price) }}</td>
                    <td class="text-right">{{ $item->discount }} %</td>
                    <td class="text-right">{{ number_format($item->discount / 100 * $item->store_product?->store_price * $item->quantity) }}</td>
                    <td class="text-right">{{ number_format($item->subtotal) }}</td>
                </tr>

                    @php
                        $total_weight += $item->store_product?->product->weight * $item->quantity;
                        $total_product += $item->quantity * $item->store_product?->store_price;
                        $total_discount += $item->discount / 100 * $item->store_product?->store_price * $item->quantity;
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
                <td class="text-right">{{ number_format($store_pos->shipping_cost) }}</td>
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
