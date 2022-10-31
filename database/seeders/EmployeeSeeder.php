<?php

namespace Database\Seeders;

use App\Models\Employee;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $employe = Employee::create([
            'no_induk' => '12097182',
            'fullname' => 'Diki Subagja',
            'gender' => 'L',
            'place_of_birth' => 'Bandung',
            'dob' => '1997-12-14',
            'address' => 'Jl. Test',
            'religion' => 'ISLAM',
            'no_hp' => '081111110223',
            'employee_position_id' => '1',
            'assignment_date' => '2019-08-01',
            'division' => 'Project',
            'functional_salary' => '500000',
        ]);

        $employe->user()->create([
            'employee_id' => $employe->id,
            'email' => 'dikisubagja50@gmail.com',
            'role' => 'admin',
            'password' => Hash::make('12345678'),
        ]);
    }
}
