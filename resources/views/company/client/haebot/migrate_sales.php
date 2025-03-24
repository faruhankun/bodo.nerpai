<?php
// Koneksi ke server 192.168.5.240 (database asal)
$source_conn = new mysqli("192.168.5.240", "id14269137_farhan123", "611281Eb15e*", "haebot1");

// Koneksi ke server tujuan
$destination_conn = new mysqli("localhost", "root", "", "business_management");

// Check koneksi sumber
if ($source_conn->connect_error) {
    die("Koneksi ke database asal gagal: " . $source_conn->connect_error);
}

// Check koneksi tujuan
if ($destination_conn->connect_error) {
    die("Koneksi ke database tujuan gagal: " . $destination_conn->connect_error);
}

// Query untuk mendapatkan data dari database asal
$source_query = "SELECT * FROM orders";
$source_result = $source_conn->query($source_query);

if ($source_result->num_rows > 0) {
    $i = 0;
    $max = 5;
    while ($row = $source_result->fetch_assoc()) {
        $i++;
        // Limit jumlah data yang diproses
        if ($i > $max) {
            break;
        }

        // Persiapkan data untuk dimasukkan ke database tujuan
        $id = $row['ORDER_NO'];
        $sale_date = $row['ORDER_DATE'];
        $sales_team = $row['ORDER_ADMIN'];

        $status_change = array(
            'NEGO' => 'Planned',
            'UNPAID' => 'Unpaid',
            'PAID' => 'Pending Shipment',
            'INS' => 'In Transit',
            'COMPLAINT' => 'Customer Complain',
            'DONE' => 'Completed',
        );
        $status = isset($status_change[$row['ORDER_STATUS']]) ? $status_change[$row['ORDER_STATUS']] : 'Planned';

        $customer_notes = $row['ORDER_NOTE_FROM_CUSTOMER'];
        $admin_notes = $row['ORDER_NOTE_FOR_ADMIN'];
        $customer_id = $row['CUSTOMER_NO'];
        $shipping_fee_disc_code = array(
            'KG' => 20000,
            'CARGO' => 60000,
        );
        $shipping_fee_disc = isset($shipping_fee_disc_code[$row['SHIPPING_FEE_PROMO']]) ? $shipping_fee_disc_code[$row['SHIPPING_FEE_PROMO']] : 0; 
        $estimated_shipping_fee = $row['SHIPPING_FEE_RATE'] - $shipping_fee_disc;

        $list_product = json_decode($row['LIST_PRODUCT'], true);

        // Jika JSON tidak valid, log kesalahan
        if (json_last_error() !== JSON_ERROR_NONE) {
            echo "JSON Error in sale ID $id: " . json_last_error_msg() . "<br>";
            continue;
        }


        // Jika ada produk dalam LIST_PRODUCT_DATA, proses datanya
        if (isset($list_product['LIST_PRODUCT_DATA'])) {
            foreach ($list_product['LIST_PRODUCT_DATA'] as $product) {
                $product_id = $product['PRODUCT_SKU'];
                $product_qty = $product['LIST_PRODUCT_QTY'];
                $product_price = $product['LIST_PRODUCT_COST'] * $product['PRICE_BASE'];
                $product_note = $product['LIST_PRODUCT_NOTE'];
                $destination_query = $destination_conn->prepare(
                    "INSERT INTO sales_products (sale_id, product_id, quantity, price, note) VALUES (?, ?, ?, ?, ?)
                     ON DUPLICATE KEY UPDATE quantity = VALUES(quantity), price = VALUES(price), note = VALUES(note)"
                );

                $destination_query->bind_param("iiids", $id, $product_id, $product_qty, $product_price, $product_note);

                if (!$destination_query->execute()) {
                    echo "Gagal data produk sku $product_id dengan sales ID $id: " . $destination_query->error . "<br>";
                }
            }
        }

        // Query untuk memasukkan data ke database tujuan
        $destination_query = $destination_conn->prepare(
            "INSERT INTO sales (id, sale_date, sales_team, `status`, customer_notes, admin_notes, customer_id, estimated_shipping_fee) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE sale_date = VALUES(sale_date), 
                                     sales_team = VALUES(sales_team),
                                     `status` = VALUES(`status`), 
                                     customer_notes = VALUES(customer_notes), 
                                     admin_notes = VALUES(admin_notes), 
                                     customer_id = VALUES(customer_id), 
                                     estimated_shipping_fee = VALUES(estimated_shipping_fee)"
        );

        $destination_query->bind_param("isssssid", $id, $sale_date, $sales_team, $status, $customer_notes, $admin_notes, $customer_id, $estimated_shipping_fee);

        if (!$destination_query->execute()) {
            echo "Gagal memindahkan data sales dengan ID $id: " . $destination_query->error . "<br>";
        } else {
            echo "Data sales dengan ID $id berhasil dipindahkan.<br>";
        }
    }
} else {
    echo "Tidak ada data ditemukan di database asal.<br>";
}

// Tutup koneksi
$source_conn->close();
$destination_conn->close();
?>
