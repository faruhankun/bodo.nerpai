<?php

namespace Database\Seeders\Primary;

use App\Models\Company\Finance\Account;
use App\Models\Company\Finance\AccountType;
use Illuminate\Database\Seeder;

class AccountTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accountTypes = [
            ['id' => 1, 'basecode' => '1-101', 'name' => 'Kas & Bank', 'type' => 'Asset', 'debit' => 1, 'credit' => -1],
            ['id' => 2, 'basecode' => '1-102', 'name' => 'Akun Piutang', 'type' => 'Asset', 'debit' => 1, 'credit' => -1],
            ['id' => 3, 'basecode' => '1-103', 'name' => 'Persediaan', 'type' => 'Asset', 'debit' => 1, 'credit' => -1],
            ['id' => 4, 'basecode' => '1-104', 'name' => 'Aktiva Lancar Lainnya', 'type' => 'Asset', 'debit' => 1, 'credit' => -1],
            ['id' => 5, 'basecode' => '1-105', 'name' => 'Aktiva Tetap', 'type' => 'Asset', 'debit' => 1, 'credit' => -1],
            ['id' => 6, 'basecode' => '1-106', 'name' => 'Depresiasi & Amortisasi', 'type' => 'Asset', 'debit' => 1, 'credit' => -1],
            ['id' => 7, 'basecode' => '1-107', 'name' => 'Aktiva Lainnya', 'type' => 'Asset', 'debit' => 1, 'credit' => -1],
            ['id' => 8, 'basecode' => '2-201', 'name' => 'Akun Hutang', 'type' => 'Liability', 'debit' => -1, 'credit' => 1],
            ['id' => 9, 'basecode' => '2-202', 'name' => 'Kewajiban Lancar Lainnya', 'type' => 'Liability', 'debit' => -1, 'credit' => 1],
            ['id' => 10, 'basecode' => '2-203', 'name' => 'Kewajiban Jangka Panjang', 'type' => 'Liability', 'debit' => -1, 'credit' => 1],
            ['id' => 11, 'basecode' => '3-301', 'name' => 'Ekuitas', 'type' => 'Equity', 'debit' => -1, 'credit' => 1],
            ['id' => 12, 'basecode' => '4-401', 'name' => 'Pendapatan', 'type' => 'Revenue', 'debit' => -1, 'credit' => 1],
            ['id' => 13, 'basecode' => '5-501', 'name' => 'Harga Pokok Penjualan', 'type' => 'Expenses', 'debit' => 1, 'credit' => -1],
            ['id' => 14, 'basecode' => '6-601', 'name' => 'Beban', 'type' => 'Expenses', 'debit' => 1, 'credit' => -1],
            ['id' => 15, 'basecode' => '7-701', 'name' => 'Pendapatan Lainnya', 'type' => 'Revenue', 'debit' => -1, 'credit' => 1],
            ['id' => 16, 'basecode' => '8-801', 'name' => 'Beban Lainnya', 'type' => 'Expenses', 'debit' => 1, 'credit' => -1],
        ];

        foreach ($accountTypes as $accountType) {
            AccountType::create($accountType);
        }

        $accounts = [
            ['id' => 1, 'code' => '1-10101', 'name' => 'Kas', 'type_id' => 1, 'parent_id' => null, 'status' => 1, 'balance' => 0],
            ['id' => 2, 'code' => '1-10102', 'name' => 'Rekening Bank X', 'type_id' => 1, 'parent_id' => null, 'status' => 1, 'balance' => 0],
            ['id' => 3, 'code' => '1-10103', 'name' => 'Rekening Bank Y', 'type_id' => 1, 'parent_id' => null, 'status' => 1, 'balance' => 0],
            ['id' => 4, 'code' => '1-10201', 'name' => 'Piutang Usaha', 'type_id' => 2, 'parent_id' => null, 'status' => 1, 'balance' => 0],
            ['id' => 5, 'code' => '1-10301', 'name' => 'Persediaan Barang', 'type_id' => 3, 'parent_id' => null, 'status' => 1, 'balance' => 0],
            ['id' => 6, 'code' => '1-10401', 'name' => 'Biaya Dibayar Di Muka', 'type_id' => 4, 'parent_id' => null, 'status' => 1, 'balance' => 0],
            ['id' => 7, 'code' => '1-10402', 'name' => 'Uang Muka', 'type_id' => 4, 'parent_id' => null, 'status' => 1, 'balance' => 0],
            ['id' => 8, 'code' => '1-10403', 'name' => 'PPN Masukan', 'type_id' => 4, 'parent_id' => null, 'status' => 1, 'balance' => 0],
            ['id' => 9, 'code' => '1-10404', 'name' => 'Pajak Dibayar Di Muka - PPh 22', 'type_id' => 4, 'parent_id' => null, 'status' => 1, 'balance' => 0],
            ['id' => 10, 'code' => '1-10405', 'name' => 'Pajak Dibayar Di Muka - PPh 23', 'type_id' => 4, 'parent_id' => null, 'status' => 1, 'balance' => 0],
            ['id' => 11, 'code' => '1-10406', 'name' => 'Pajak Dibayar Di Muka - PPh 25', 'type_id' => 4, 'parent_id' => null, 'status' => 1, 'balance' => 0],
            ['id' => 12, 'code' => '1-10501', 'name' => 'Aset Tetap - Tanah', 'type_id' => 5, 'parent_id' => null, 'status' => 1, 'balance' => 0],
            ['id' => 13, 'code' => '1-10502', 'name' => 'Aset Tetap - Bangunan', 'type_id' => 5, 'parent_id' => null, 'status' => 1, 'balance' => 0],
            ['id' => 14, 'code' => '1-10503', 'name' => 'Aset Tetap - Kendaraan', 'type_id' => 5, 'parent_id' => null, 'status' => 1, 'balance' => 0],
            ['id' => 15, 'code' => '1-10504', 'name' => 'Aset Tetap - Mesin & Peralatan', 'type_id' => 5, 'parent_id' => null, 'status' => 1, 'balance' => 0],
            ['id' => 16, 'code' => '1-10505', 'name' => 'Aset Tak Berwujud', 'type_id' => 5, 'parent_id' => null, 'status' => 1, 'balance' => 0],
            ['id' => 17, 'code' => '1-10601', 'name' => 'Akumulasi Aset Tetap - Tanah', 'type_id' => 6, 'parent_id' => null, 'status' => 1, 'balance' => 0],
            ['id' => 18, 'code' => '1-10602', 'name' => 'Akumulasi Aset Tetap - Bangunan', 'type_id' => 6, 'parent_id' => null, 'status' => 1, 'balance' => 0],
            ['id' => 19, 'code' => '1-10603', 'name' => 'Akumulasi Aset Tetap - Kendaraan', 'type_id' => 6, 'parent_id' => null, 'status' => 1, 'balance' => 0],
            ['id' => 20, 'code' => '1-10604', 'name' => 'Akumulasi Aset Tetap - Mesin & Peralatan', 'type_id' => 6, 'parent_id' => null, 'status' => 1, 'balance' => 0],
            ['id' => 21, 'code' => '1-10605', 'name' => 'Akumulasi Amortisasi', 'type_id' => 6, 'parent_id' => null, 'status' => 1, 'balance' => 0],
            ['id' => 22, 'code' => '2-20101', 'name' => 'Hutang Usaha', 'type_id' => 8, 'parent_id' => null, 'status' => 1, 'balance' => 0],
            ['id' => 23, 'code' => '2-20201', 'name' => 'Hutang Gaji', 'type_id' => 9, 'parent_id' => null, 'status' => 1, 'balance' => 0],
            ['id' => 24, 'code' => '2-20202', 'name' => 'Pendapatan Diterima Di Muka', 'type_id' => 9, 'parent_id' => null, 'status' => 1, 'balance' => 0],
            ['id' => 25, 'code' => '2-20203', 'name' => 'Hutang Bank', 'type_id' => 9, 'parent_id' => null, 'status' => 1, 'balance' => 0],
            ['id' => 26, 'code' => '2-20204', 'name' => 'PPN Keluaran', 'type_id' => 9, 'parent_id' => null, 'status' => 1, 'balance' => 0],
            ['id' => 27, 'code' => '2-20205', 'name' => 'Hutang Pajak - PPh 21', 'type_id' => 9, 'parent_id' => null, 'status' => 1, 'balance' => 0],
            ['id' => 28, 'code' => '2-20206', 'name' => 'Hutang Pajak - PPh 22', 'type_id' => 9, 'parent_id' => null, 'status' => 1, 'balance' => 0],
            ['id' => 29, 'code' => '2-20207', 'name' => 'Hutang Pajak - PPh 23', 'type_id' => 9, 'parent_id' => null, 'status' => 1, 'balance' => 0],
            ['id' => 30, 'code' => '2-20208', 'name' => 'Hutang Pajak - PPh 29', 'type_id' => 9, 'parent_id' => null, 'status' => 1, 'balance' => 0],
            ['id' => 31, 'code' => '3-30101', 'name' => 'Modal Saham', 'type_id' => 11, 'parent_id' => null, 'status' => 1, 'balance' => 0],
            ['id' => 32, 'code' => '3-30102', 'name' => 'Laba Ditahan', 'type_id' => 11, 'parent_id' => null, 'status' => 1, 'balance' => 0],
            ['id' => 33, 'code' => '4-40101', 'name' => 'Pendapatan', 'type_id' => 12, 'parent_id' => null, 'status' => 1, 'balance' => 0],
            ['id' => 34, 'code' => '4-40102', 'name' => 'Diskon Penjualan', 'type_id' => 12, 'parent_id' => null, 'status' => 1, 'balance' => 0],
            ['id' => 35, 'code' => '4-40103', 'name' => 'Return Penjualan', 'type_id' => 12, 'parent_id' => null, 'status' => 1, 'balance' => 0],
            ['id' => 36, 'code' => '5-50101', 'name' => 'Beban Pokok Pendapatan', 'type_id' => 13, 'parent_id' => null, 'status' => 1, 'balance' => 0],
            ['id' => 37, 'code' => '5-50102', 'name' => 'Diskon Pembelian', 'type_id' => 13, 'parent_id' => null, 'status' => 1, 'balance' => 0],
            ['id' => 38, 'code' => '5-50103', 'name' => 'Return Pembelian', 'type_id' => 13, 'parent_id' => null, 'status' => 1, 'balance' => 0],
            ['id' => 39, 'code' => '5-50104', 'name' => 'Pengiriman & Pengangkutan', 'type_id' => 13, 'parent_id' => null, 'status' => 1, 'balance' => 0],
            ['id' => 40, 'code' => '5-50105', 'name' => 'Biaya Impor', 'type_id' => 13, 'parent_id' => null, 'status' => 1, 'balance' => 0],
            ['id' => 41, 'code' => '5-50106', 'name' => 'Biaya Produksi', 'type_id' => 13, 'parent_id' => null, 'status' => 1, 'balance' => 0],
            ['id' => 42, 'code' => '6-60101', 'name' => 'Komisi & Fee', 'type_id' => 14, 'parent_id' => null, 'status' => 1, 'balance' => 0],
            ['id' => 43, 'code' => '6-60102', 'name' => 'Gaji', 'type_id' => 14, 'parent_id' => null, 'status' => 1, 'balance' => 0],
            ['id' => 44, 'code' => '6-60103', 'name' => 'Lembur', 'type_id' => 14, 'parent_id' => null, 'status' => 1, 'balance' => 0],
            ['id' => 45, 'code' => '6-60104', 'name' => 'Makanan', 'type_id' => 14, 'parent_id' => null, 'status' => 1, 'balance' => 0],
            ['id' => 46, 'code' => '6-60105', 'name' => 'Iuran & Langganan', 'type_id' => 14, 'parent_id' => null, 'status' => 1, 'balance' => 0],
            ['id' => 47, 'code' => '6-60106', 'name' => 'Legal & Professional', 'type_id' => 14, 'parent_id' => null, 'status' => 1, 'balance' => 0],
            ['id' => 48, 'code' => '6-60107', 'name' => 'Listrik', 'type_id' => 14, 'parent_id' => null, 'status' => 1, 'balance' => 0],
            ['id' => 49, 'code' => '6-60108', 'name' => 'Air', 'type_id' => 14, 'parent_id' => null, 'status' => 1, 'balance' => 0],
            ['id' => 50, 'code' => '6-60109', 'name' => 'Langganan Software', 'type_id' => 14, 'parent_id' => null, 'status' => 1, 'balance' => 0],
            ['id' => 51, 'code' => '6-60110', 'name' => 'Biaya Sewa - Bangunan', 'type_id' => 14, 'parent_id' => null, 'status' => 1, 'balance' => 0],
            ['id' => 52, 'code' => '6-60111', 'name' => 'Biaya Sewa - Kendaraan', 'type_id' => 14, 'parent_id' => null, 'status' => 1, 'balance' => 0],
            ['id' => 53, 'code' => '6-60112', 'name' => 'Biaya Sewa - Operasional', 'type_id' => 14, 'parent_id' => null, 'status' => 1, 'balance' => 0],
            ['id' => 54, 'code' => '6-60113', 'name' => 'Penyusutan - Tanah', 'type_id' => 14, 'parent_id' => null, 'status' => 1, 'balance' => 0],
            ['id' => 55, 'code' => '6-60114', 'name' => 'Penyusutan - Bangunan', 'type_id' => 14, 'parent_id' => null, 'status' => 1, 'balance' => 0],
            ['id' => 56, 'code' => '6-60115', 'name' => 'Penyusutan - Kendaraan', 'type_id' => 14, 'parent_id' => null, 'status' => 1, 'balance' => 0],
            ['id' => 57, 'code' => '6-60116', 'name' => 'Penyusutan - Mesin & Peralatan', 'type_id' => 14, 'parent_id' => null, 'status' => 1, 'balance' => 0],
            ['id' => 58, 'code' => '7-70101', 'name' => 'Pendapatan Lainnya', 'type_id' => 15, 'parent_id' => null, 'status' => 1, 'balance' => 0],
            ['id' => 59, 'code' => '8-80101', 'name' => 'Penyesuaian Persediaan', 'type_id' => 16, 'parent_id' => null, 'status' => 1, 'balance' => 0],
            ['id' => 60, 'code' => '6-60117', 'name' => 'Biaya Logistik Pengiriman', 'type_id' => 14, 'parent_id' => null, 'status' => 1, 'balance' => 0],
        ];

        foreach ($accounts as $account) {
            Account::create($account);
        }
    }
}
