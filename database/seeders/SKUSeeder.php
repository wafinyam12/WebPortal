<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SKU;

class SKUSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SKU::insert([
            ['sku' => 'ATK000001', 'keterangan' => 'PENGGARIS 30 CM PLASTIK BUTTERFLY'],
            
        ]);
    }
}
