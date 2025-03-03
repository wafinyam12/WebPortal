<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class IncomingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $faker = \Faker\Factory::create();

        // Seed suppliers
        $suppliers = [];
        for ($i = 1; $i <= 5; $i++) {
            $suppliers[] = [
                'name' => $faker->company,
                'phone' => $faker->phoneNumber,
                'email' => $faker->optional()->companyEmail,
                'address' => $faker->address,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('incoming_suppliers')->insert($suppliers);

        // Get existing users, branches, and warehouses
        $users = DB::table('users')->pluck('id');
        $branches = DB::table('branches')->pluck('id');
        $warehouses = DB::table('warehouses')->pluck('id');
        $supplierIds = DB::table('incoming_suppliers')->pluck('id');

        // Seed shipments
        for ($i = 1; $i <= 10; $i++) {
            $shipmentId = DB::table('incoming_shipments')->insertGetId([
                'code' => 'SHIP-' . Str::upper(Str::random(6)),
                'created_by' => $faker->randomElement($users),
                'branch_id' => $faker->randomElement($branches),
                'supplier_id' => $faker->randomElement($supplierIds),
                'warehouse_id' => $faker->optional()->randomElement($warehouses),
                'drop_site' => $faker->optional()->city,
                'phone_drop_site' => $faker->optional()->phoneNumber,
                'email_drop_site' => $faker->optional()->email,
                'eta' => $faker->dateTimeBetween('+1 week', '+1 month'),
                'notes' => $faker->optional()->paragraph,
                'attachment' => $faker->optional()->mimeType('application/pdf'),
                'status' => $faker->randomElement(['On Progress', 'Received']),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Seed inventory for each shipment
            $inventoryCount = $faker->numberBetween(1, 5);
            for ($j = 1; $j <= $inventoryCount; $j++) {
                DB::table('incoming_inventory')->insert([
                    'shipment_id' => $shipmentId,
                    'item_name' => $faker->randomElement(['Laptop', 'Printer', 'Monitor', 'Keyboard', 'Mouse']),
                    'quantity' => $faker->numberBetween(1, 100),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
