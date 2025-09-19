@php
    $logoPath = public_path('svg/' . $data->space?->code . '.svg');
    $logoUrl = file_exists($logoPath) ? asset('svg/' . $data->space?->code . '.svg') : asset('svg/hehe.svg');

    $address = $data->space->address['detail'] ?? 'Blitar, Jawa Timur';


    $space_phone_number = $data->space?->phone_number ?? (get_variable('space.quotes.invoice.phone_number') ?? 'Phone Number');
    $payment_note = get_variable('space.quotes.invoice.payment_note') ?? null;


    
    $list_items = $data->details;
    $list_tx_data = [
        $data->id => [
            'number' => $data->number,
            'date' => optional($data->sent_time)->format('Y-m-d'),
        ],
    ];

    // cek children
    $children = $data->children->sortBy('sent_time');
    foreach($children as $child){
        $list_tx_data[$child->id] = [
            'number' => $child->number,
            'date' => optional($child->sent_time)->format('Y-m-d'),
        ];

        $list_items = $list_items->merge($child->details);
    }


    $other_bill_details = [];
    $other_bill = 0;
@endphp


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice Pesanan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
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
            background-color: #073763;
            color: #fff;
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
            width: 50%;
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
                <h1>{{ strtoupper($data->space?->name ?? 'Space Name') }}</h1>
                <h3>{{ $address ?? 'Space Address' }}</h3>
                <h3>{{ $space_phone_number }}</h3>
            </div>
        </div>

        <table style="text-align: center;">
            <tr>
                <th style="text-align: center; font-size: 20px;"><strong>Invoice Order</strong></th>
            </tr>
        </table>


        <!-- Info Transaksi -->
        <table style="margin: 20px auto; text-align: center; width: 100%;">
            <tr>
                <th>TGL TRANSAKSI</th>
                <td>{{ ($data->sent_time ?? today())->format('l, j F Y') }}</td>
                <th>NO INVOICE</th>
                <td>{{ $data->number ?? 'NO_INVOICE' }}</td>
            </tr>
            <tr>
                <th>NAMA PEMBELI</th>
                <td>{{ $data->receiver?->name ?? 'CUSTOMER NAME' }}</td>
                <th>TOKO</th>
                <td>{{ $data->space?->name ?? '-' }}</td>
            </tr>
            <tr>
                <th>HANDLER</th>
                <td>{{ $data?->sender?->name ?? '' }}, {{ $data?->handler?->name ?? '' }}</td>
                <th>CATATAN</th>
                <td>{{ $data->receiver_notes ?? '' }}</td>
            </tr>
        </table>



        <!-- Produk -->
        <table style="margin-top: 20px;">
            <thead>
                <tr>
                    <th>Invoice</th>
                    <th>Tanggal</th>
                    <th>SKU | NAMA PRODUK</th>
                    <th>QTY</th>
                    <th>BERAT</th>
                    <th>HARGA</th>
                    <th>Diskon</th>
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
                @foreach($list_items as $detail)
                    @if($detail->model_type == 'ITR')
                        @continue
                    @endif

                    @if(str_contains($detail->detail?->sku, 'bill') || str_contains($detail->detail?->sku, 'payment'))
                        @php
                            $other_bill_details[] = $detail;
                        @endphp
                        @continue
                    @endif

                    @php 
                        $qty = ($detail->quantity) * (-1);
                        $price = $detail->price ?? $detail->detail->price;
                    @endphp

                <tr>
                    <td>{{ $list_tx_data[$detail->transaction_id]['number'] }}</td>
                    <td>{{ $list_tx_data[$detail->transaction_id]['date'] }}</td>
                    <td>{{ $detail->detail?->sku }} | {{ $detail->detail?->name }}</td>
                    <td class="text-center">{{ $qty }}</td>
                    <td class="text-right">{{ number_format($detail->detail?->weight) }}</td>
                    <td class="text-right">Rp. {{ number_format($price) }}</td>
                    <td class="text-right">( {{ $detail->discount * 100 }}%) {{ number_format($detail->discount * $price) }}</td>
                    <td class="text-right">{{ number_format($qty * $price * (1 - $detail->discount)) }}</td>
                </tr>

                    @php
                        $total_weight += $detail->detail?->weight * $qty;
                        $total_product += $qty * $price;
                        $total_discount += $detail->discount * $price * $qty;
                    @endphp
                @endforeach
                <!-- Total berat -->
                <tr>
                    <th colspan="4" class="text-right">Subtotal</th>
                    <td class="text-right"><strong>{{ number_format($total_weight / 1000, 2) }} Kg</strong></td>
                    <td colspan="4" class="text-right"><strong>{{ number_format($total_product - $total_discount) }}</strong></td>
                </tr>
            </tbody>
        </table>

        <!-- Summary -->
        <table class="summary">
            <tr>
                <th colspan="3"><strong>TAGIHAN PRODUK</strong></th>
                <td class="text-right"><strong>{{ number_format($total_product) }}</strong></td>
            </tr>
            <tr>
                <th colspan="3"><strong>TOTAL DISKON</strong></th>
                <td class="text-right">{{ number_format($total_discount) }}</td>
            </tr>


            <!-- other bill -->
            @if($other_bill_details)
                @foreach($other_bill_details as $detail)
                    @php
                        $subtotal = $detail->quantity * $detail->price * (-1);// (str_contains($detail->detail?->sku, 'payment') ? -1 : 1);
                        $other_bill += $subtotal;
                    @endphp

                    <tr>
                        <td>{{ $list_tx_data[$detail->transaction_id]['number'] }}</td>
                        <td>{{ $list_tx_data[$detail->transaction_id]['date'] }}</td>
                        <th><strong>{{ $detail->detail->name }}</strong></th>
                        <td class="text-right"><strong>{{ number_format($subtotal) }}</strong></td>
                    </tr>
                @endforeach
            @endif

            @php
                $grand_total = $total_product - $total_discount + $other_bill;
            @endphp

            <tr>
                <th colspan="3"><strong>TOTAL TAGIHAN</strong></th>
                <th class="text-right"><strong>{{ number_format($grand_total) }}</strong></th>
            </tr>
        </table>

        <div class="footer">
            <br><br>
            <span style="font-size: 16px; font-weight: bold; line-height: 1.5;">
                @if($payment_note)
                    *Catatan Pembayaran: <br>
                    {!! $payment_note !!}
                @endif
            </span>
        </div>
    </div>
</body>
</html>
