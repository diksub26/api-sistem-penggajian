<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmployeePositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('employee_positions')->insert([
            [
                'position_name' => 'Manajer',
                'position_salary' => '8000000',
            ],
            [
                'position_name' => 'Junior Programer',
                'position_salary' => '4000000',
            ],
            [
                'position_name' => 'Senior Programer',
                'position_salary' => '6000000',
            ],
            [
                'position_name' => 'Admin',
                'position_salary' => '3600000',
            ],
            [
                'position_name' => 'Staff Backoffice',
                'position_salary' => '400000',
            ],
            [
                'position_name' => 'Implementator',
                'position_salary' => '400000',
            ],
        ]);
    }
}
