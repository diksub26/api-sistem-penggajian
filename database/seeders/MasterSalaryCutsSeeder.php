<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterSalaryCutsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('master_salary_cuts')->insert([
            [
                'salary_cuts_name' => 'Koperasi',
                'salary_cuts_amount' => '30000',
                'salary_cuts_type' => 'amount',
            ],
            [
                'salary_cuts_name' => 'JHT',
                'salary_cuts_amount' => '74846',
                'salary_cuts_type' => 'amount',
            ],
            [
                'salary_cuts_name' => 'JKN',
                'salary_cuts_amount' => '1',
                'salary_cuts_type' => 'percentage',
            ],
        ]);
    }
}
