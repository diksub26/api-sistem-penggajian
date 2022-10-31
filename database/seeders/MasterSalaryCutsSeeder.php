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
                'salary_cuts_name' => 'BPJS',
                'salary_cuts_amount' => '2.5',
                'salary_cuts_type' => 'percentage',
            ],
        ]);
    }
}
