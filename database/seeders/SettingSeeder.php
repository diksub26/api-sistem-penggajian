<?php

namespace Database\Seeders;

use App\Models\MasterData\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Setting::create(
            [
                'name' => 'Denda Terlambat',
                'value' => '10000'
            ],
            [
                'name' => 'Fee Lembur',
                'value' => '10000'
            ],
            [
                'name' => 'Tgl Cut off',
                'value' => '20'
            ]
        );
    }
}
