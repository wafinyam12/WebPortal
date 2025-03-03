<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('notifications')->insert([
            [
                'name' => 'Contract Expired Notification',
                'description' => 'Pemberitahuan 1',
                'roles' => json_encode(['Admin Legal']),
                'template' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Vehicle Expired Notification',
                'description' => 'Pemberitahuan 2',
                'roles' => json_encode(['Admin GA']),
                'template' => '2',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Incoming Notification',
                'description' => 'Pemberitahuan 3',
                'roles' => json_encode(['Admin Purchasing', 'Manager']),
                'template' => '3',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Cost Bids Analysis Notification',
                'description' => 'Pemberitahuan 4',
                'roles' => json_encode(['Admin Purchasing', 'Manager']),
                'template' => '4',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
