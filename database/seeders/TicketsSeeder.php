<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TicketsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $faker = \Faker\Factory::create();

        // Seed ticket categories
        $categories = [
            [
                'name' => 'Technical Support',
                'slug' => 'technical-support',
                'description' => 'Issues related to technical problems and IT support.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Hardware Issue',
                'slug' => 'hardware-issue',
                'description' => 'Problems with hardware components and devices.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Software Issue',
                'slug' => 'software-issue',
                'description' => 'Issues related to software applications and systems.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Network Problem',
                'slug' => 'network-problem',
                'description' => 'Connectivity and network-related issues.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'General Inquiry',
                'slug' => 'general-inquiry',
                'description' => 'General questions and inquiries.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        DB::table('tickets_categories')->insert($categories);

        // Get existing users and departments
        $users = DB::table('users')->pluck('id');
        $departments = DB::table('departments')->pluck('id');

        // Seed tickets
        for ($i = 1; $i <= 20; $i++) {
            $ticketId = DB::table('tickets')->insertGetId([
                'user_id' => $faker->randomElement($users),
                'category_id' => DB::table('tickets_categories')->inRandomOrder()->first()->id,
                'assigned_id' => $faker->randomElement($departments),
                'user_by' => $faker->optional()->randomElement($users),
                'code' => 'TICKET-' . Str::upper(Str::random(6)),
                'title' => $faker->sentence(),
                'description' => $faker->paragraph,
                'priority' => $faker->randomElement(['Low', 'Medium', 'High', 'Urgent', 'Other']),
                'status' => $faker->randomElement(['Open', 'Closed', 'In Progress', 'Cancelled']),
                'solution' => $faker->optional()->paragraph,
                'attachment' => $faker->optional()->imageUrl(),
                'closed_date' => $faker->optional()->dateTimeThisYear(),
                'reason' => $faker->optional()->sentence(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Seed comments for each ticket
            $commentCount = $faker->numberBetween(1, 5);
            for ($j = 1; $j <= $commentCount; $j++) {
                DB::table('tickets_comments')->insert([
                    'ticket_id' => $ticketId,
                    'user_id' => $faker->randomElement($users),
                    'comment' => $faker->paragraph,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
