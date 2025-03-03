<?php

namespace Database\Seeders;

use App\Models\Saldo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class saldoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Saldo::insert([
            'owner_id' => 1,
            'saldo' => 1000000
        ]);
    }
}
