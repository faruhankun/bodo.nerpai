@php
$config_haebot = config('migrate_client.haebot');

$source_conn = new mysqli('202.59.193.173', 
                            $config_haebot['source']['user'], 
                            $config_haebot['source']['pass'], 
                            $config_haebot['source']['db'],
                            $config_haebot['source']['port'])
                            or die("Koneksi gagal: " . mysqli_connect_error());

// customers
dd(\App\Models\Company\Customer::all());

// Query untuk mendapatkan data dari database asal
$source_query = "SELECT CUSTOMER_NO, CUSTOMER_NAME, CUSTOMER_STATE, CONTACT, ADDRESS, CUSTOMER_DATE_REG  FROM customers";
$source_result = $source_conn->query($source_query);

if ($source_result->num_rows > 0) {
    dd($source_result);

    while ($row = $source_result->fetch_assoc()) {
        // Persiapkan data untuk dimasukkan ke database tujuan
        $id = $row['CUSTOMER_NO'];
        $name = $row['CUSTOMER_NAME'];
        $status = $row['CUSTOMER_STATE'];
        $contact_data = json_decode($row['CONTACT'], true);
        $address = $row['ADDRESS'];
        $reg_date = $row['CUSTOMER_DATE_REG'];

        // Jika JSON tidak valid, lewati data ini
        if (json_last_error() !== JSON_ERROR_NONE) {
            continue;
        }

        $email = isset($contact_data['CONTACT_EMAIL']) ? $contact_data['CONTACT_EMAIL'] : null;
        $phone_number = isset($contact_data['CONTACT_NO']) ? $contact_data['CONTACT_NO'] : null;

        // Query untuk memasukkan data ke database tujuan
        $destination_query = $destination_conn->prepare(
            "INSERT INTO customers (id, name, email, phone_number, status, address, reg_date) VALUES (?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE name = VALUES(name), email = VALUES(email), phone_number = VALUES(phone_number), status = VALUES(status), address = VALUES(address), reg_date = VALUES(reg_date)"
        );

        $destination_query->bind_param("issssss", $id, $name, $email, $phone_number, $status, $address, $reg_date);

        if ($destination_query->execute()) {
            echo "Data pelanggan dengan ID $id berhasil dipindahkan. <br>";
        } else {
            echo "Gagal memindahkan data pelanggan dengan ID $id: " . $destination_query->error . "<br>";
        }
    }
} else {
    echo "Tidak ada data ditemukan di database asal. <br>";
}


@endphp

