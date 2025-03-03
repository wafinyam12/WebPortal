<?php

namespace Database\Seeders;

use App\Models\Warehouses;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Warehouses::insert([
            [
                'company_id' => 1,
                'branch_id' => 1,
                'code' => 'WRHS00001',
                'name' => 'Warehouse Lawang',
                'phone' => '1234567890',
                'email' => 'warehouse1@example.com',
                'address' => 'Jl Srengseng Sawah 87, Dki Jakarta',
                'description' => 'Warehouse 1 description',
                'status' => 'Active',
                'type' => 'Warehouse',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'company_id' => 1,
                'branch_id' => 1,
                'code' => 'WRHS00002',
                'name' => 'Warehouse Rungkut',
                'phone' => '1234567890',
                'email' => 'warehouse1@example.com',
                'address' => 'Jl Srengseng Sawah 87, Dki Jakarta',
                'description' => 'Warehouse 1 description',
                'status' => 'Active',
                'type' => 'Warehouse',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'company_id' => 1,
                'branch_id' => 1,
                'code' => 'WRHS00003',
                'name' => 'Warehouse Pier',
                'phone' => '1234567890',
                'email' => 'warehouse1@example.com',
                'address' => 'Jl Srengseng Sawah 87, Dki Jakarta',
                'description' => 'Warehouse 1 description',
                'status' => 'Active',
                'type' => 'Warehouse',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }
}
