<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ToolsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $faker = \Faker\Factory::create();

        // Seed projects
        $projects = [];
        for ($i = 1; $i <= 5; $i++) {
            $projects[] = [
                'code' => 'PRJ-' . Str::upper(Str::random(6)),
                'name' => $faker->randomElement(['Project ', 'Construction ', 'Development ']) . $faker->city,
                'address' => $faker->address,
                'phone' => $faker->phoneNumber,
                'email' => $faker->companyEmail,
                'ppic' => $faker->name,
                'description' => $faker->paragraph,
                'status' => $faker->randomElement(['Active', 'Inactive']),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('projects')->insert($projects);

        // Seed tools categories
        $categories = [
            [
                'code' => 'CT-CONST',
                'name' => 'Construction',
                'description' => 'Heavy construction equipment',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'CT-ELEC',
                'name' => 'Electrical',
                'description' => 'Electrical tools and equipment',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'CT-MEAS',
                'name' => 'Measuring',
                'description' => 'Measurement instruments',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        DB::table('tools_categories')->insert($categories);

        // Get existing company and user
        $companyId = DB::table('branches')->first()->id;
        $userId = DB::table('users')->first()->id;

        // Seed tools
        for ($i = 1; $i <= 10; $i++) {
            $toolId = DB::table('tools')->insertGetId([
                'owner_id' => $companyId,
                'category_id' => DB::table('tools_categories')->inRandomOrder()->first()->id,
                'code' => 'TL-' . Str::upper(Str::random(6)),
                'serial_number' => 'SN-' . Str::upper(Str::random(8)),
                'name' => $faker->randomElement(['Drill ', 'Crane ', 'Hammer ', 'Multimeter ']) . $faker->word,
                'brand' => $faker->randomElement(['Bosch', 'Makita', 'Stanley', 'Fluke']),
                'type' => $faker->word,
                'model' => $faker->bothify('Model-??##'),
                'year' => $faker->year,
                'origin' => $faker->country,
                'quantity' => $faker->numberBetween(1, 10),
                'unit' => $faker->randomElement(['Unit', 'Set', 'Pcs']),
                'condition' => $faker->randomElement(['New', 'Used', 'Broken']),
                'status' => $faker->randomElement(['Active', 'Maintenance', 'Inactive']),
                'description' => $faker->paragraph,
                'purchase_date' => $faker->dateTimeBetween('-5 years'),
                'purchase_price' => $faker->randomFloat(2, 100, 10000),
                'warranty' => $faker->randomElement(['1 Year', '2 Years', 'No Warranty']),
                'warranty_start' => $faker->dateTimeBetween('-1 year'),
                'warranty_end' => $faker->dateTimeBetween('now', '+2 years'),
                'photo' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Maintenance records
            DB::table('tools_maintenance')->insert([
                'tool_id' => $toolId,
                'code' => 'MT-' . Str::upper(Str::random(6)),
                'maintenance_date' => $faker->dateTimeBetween('-6 months'),
                'cost' => $faker->randomFloat(2, 50, 1000),
                'status' => $faker->randomElement(['In Progress', 'Completed', 'Cancelled']),
                'description' => $faker->sentence(),
                'completion_date' => $faker->optional()->dateTimeBetween('-1 month'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Seed transactions
        $projectIds = DB::table('projects')->pluck('id');
        $tools = DB::table('tools')->get();

        foreach (range(1, 15) as $index) {
            $transactionId = DB::table('tools_transactions')->insertGetId([
                'user_id' => $userId,
                'source_project_id' => $faker->randomElement($projectIds),
                'destination_project_id' => $faker->randomElement($projectIds),
                'document_code' => 'DOC-' . Str::upper(Str::random(8)),
                'document_date' => $faker->dateTimeBetween('-1 year'),
                'delivery_date' => $faker->dateTimeBetween('+1 week', '+1 month'),
                'ppic' => $faker->name,
                'driver' => $faker->name,
                'driver_phone' => $faker->phoneNumber,
                'transportation' => $faker->randomElement(['Truck', 'Pickup', 'Van']),
                'plate_number' => Str::upper(Str::random(3)) . ' ' . $faker->numberBetween(1000, 9999),
                'status' => $faker->randomElement(['In Progress', 'Completed', 'Cancelled']),
                'type' => $faker->randomElement(['Delivery Note', 'Transfer', 'Return']),
                'notes' => $faker->paragraph,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Seed transaction shipments
            foreach ($faker->randomElements($tools, $faker->numberBetween(1, 5)) as $tool) {
                DB::table('tools_transactions_shipments')->insert([
                    'transactions_id' => $transactionId,
                    'tool_id' => $tool->id,
                    'quantity' => $faker->numberBetween(1, $tool->quantity),
                    'unit' => $tool->unit,
                    'last_location' => $faker->address,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Seed asuransi project
        foreach ($projectIds as $projectId) {
            DB::table('asuransi_project')->insert([
                'project_id' => $projectId,
                'name' => 'Asuransi ' . $faker->word,
                'tanggal_mulai' => $faker->dateTimeBetween('-1 year'),
                'masa_berlaku' => $faker->dateTimeBetween('now', '+2 years'),
                'tanggal_jatuh_tempo' => $faker->dateTimeBetween('now', '+2 years'),
                'status' => $faker->randomElement(['OPEN', 'CLOSED', 'RENEWED']),
                'catatan' => $faker->paragraph,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
