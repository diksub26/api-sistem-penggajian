<?php

namespace Database\Seeders;

use App\Models\MasterData\Penalties as MasterDataPenalties;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class Penalties extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        MasterDataPenalties::create(
            [
                'penalty_name' => 'Kesiangan',
                'penalty_amount' => '10000'
            ]
        );
    }
}
