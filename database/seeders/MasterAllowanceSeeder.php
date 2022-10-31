<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterAllowanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('master_allowances')->insert([
            [
                'allowance_name' => 'Tunjangan Transportasi',
                'allowance_amount' => '200000',
            ],
            [
                'allowance_name' => 'Tunjangan Makan',
                'allowance_amount' => '300000',
            ],
        ]);
    }
}
