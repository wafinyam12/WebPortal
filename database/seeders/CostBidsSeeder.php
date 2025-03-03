<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CostBidsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $faker = \Faker\Factory::create();

        // Get existing users and branches
        $users = DB::table('users')->pluck('id');
        $branches = DB::table('branches')->pluck('id');

        // Seed cost bids
        for ($i = 1; $i <= 5; $i++) {
            $costBidId = DB::table('cost_bids')->insertGetId([
                'code' => 'BID-' . Str::upper(Str::random(6)),
                'branch_id' => $faker->randomElement($branches),
                'project_name' => $faker->randomElement(['Project ', 'Construction ', 'Development ']) . $faker->city,
                'document_date' => $faker->dateTimeBetween('-1 year'),
                'bid_date' => $faker->dateTimeBetween('now', '+1 month'),
                'selected_vendor' => $faker->optional()->company,
                'attachment' => $faker->optional()->mimeType('application/pdf'),
                'notes' => $faker->optional()->paragraph,
                'status' => $faker->randomElement(['Open', 'Approved', 'Rejected']),
                'created_by' => $faker->randomElement($users),
                'token' => $faker->optional()->uuid,
                'approved_by' => $faker->optional()->randomElement($users),
                'rejected_by' => $faker->optional()->randomElement($users),
                'reason' => $faker->optional()->sentence(),
                'approved_at' => $faker->optional()->dateTimeThisYear,
                'rejected_at' => $faker->optional()->dateTimeThisYear,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Seed vendors for each cost bid
            $vendorCount = $faker->numberBetween(1, 3);
            $vendorIds = [];
            for ($j = 1; $j <= $vendorCount; $j++) {
                $vendorId = DB::table('cost_bids_vendor')->insertGetId([
                    'cost_bids_id' => $costBidId,
                    'name' => $faker->company,
                    'phone' => $faker->phoneNumber,
                    'email' => $faker->optional()->companyEmail,
                    'address' => $faker->address,
                    'grand_total' => $faker->randomFloat(2, 1000, 100000),
                    'discount' => $faker->randomFloat(2, 0, 20),
                    'final_total' => $faker->randomFloat(2, 1000, 100000),
                    'terms_of_payment' => $faker->optional()->sentence(),
                    'lead_time' => $faker->optional()->numberBetween(1, 30) . ' days',
                    'notes' => $faker->optional()->paragraph,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $vendorIds[] = $vendorId;
            }

            // Seed items for each cost bid
            $itemCount = $faker->numberBetween(1, 5);
            $itemIds = [];
            for ($j = 1; $j <= $itemCount; $j++) {
                $itemId = DB::table('cost_bids_items')->insertGetId([
                    'cost_bids_id' => $costBidId,
                    'description' => $faker->sentence(),
                    'quantity' => $faker->numberBetween(1, 100),
                    'uom' => $faker->randomElement(['Unit', 'Set', 'Pcs', 'Meter']),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $itemIds[] = $itemId;
            }

            // Seed analysis for each item and vendor
            foreach ($itemIds as $itemId) {
                foreach ($vendorIds as $vendorId) {
                    DB::table('cost_bids_analysis')->insert([
                        'cost_bids_item_id' => $itemId,
                        'cost_bids_vendor_id' => $vendorId,
                        'price' => $faker->randomFloat(2, 10, 1000),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
