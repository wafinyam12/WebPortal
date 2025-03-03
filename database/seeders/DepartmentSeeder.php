<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Department::insert([
            [
                'code' => 'D00001',
                'company_id' => 1,
                'name' => 'Head Office',
                'email' => 'riski.bocaliar86@gmail.com',
                'description' => 'Description for Head Office',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'D00002',
                'company_id' => 1,
                'name' => 'IT',
                'email' => 'riski.bocaliar86@gmail.com',
                'description' => 'Description for Department IT',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'D00003',
                'company_id' => 1,
                'name' => 'Legal',
                'email' => 'riski.bocaliar86@gmail.com',
                'description' => 'Description for Department Legal',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'D00004',
                'company_id' => 1,
                'name' => 'GA',
                'email' => 'riski.bocaliar86@gmail.com',
                'description' => 'Description for Department GA',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'D00005',
                'company_id' => 1,
                'name' => 'Purchasing',
                'email' => 'riski.bocaliar86@gmail.com',
                'description' => 'Description for Department Purchasing',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }
}
