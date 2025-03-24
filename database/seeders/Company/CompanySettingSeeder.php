<?php

namespace Database\Seeders\Company;

use Illuminate\Database\Seeder;
use App\Models\Company\CompanySetting;

class CompanySettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = config('settings.company'); // Ambil data dari config/settings.php

        foreach ($settings as $key => $value) {
            if(substr($key, 0, 5) == 'comp.') {
                CompanySetting::updateOrCreate(
                    ['key' => $key], // Cek apakah sudah ada
                    ['value' => is_array($value) ? json_encode($value) : $value] // Simpan sebagai string jika array
                );
            }
        }
    }
}
