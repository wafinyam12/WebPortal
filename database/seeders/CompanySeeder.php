<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Company;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Company::insert([
            [
                'code' => 'C00001',
                'name' => 'Utomodeck Group',
                'email' => 'yooerizki10@webmail.umm.ac.id',
                'address' => 'Jl. Basuki Rahmat No.149, Embong Kaliasin, Kec. Genteng, Surabaya, Jawa Timur 60271',
                'phone' => '62895341341001',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
