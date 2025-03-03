<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class VehicleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $faker = \Faker\Factory::create();

        // Seed vehicle types
        DB::table('vehicle_type')->insert([
            [
                'code' => 'VTY-SEDAN',
                'name' => 'Sedan',
                'description' => 'Standard sedan car',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'VTY-SUV',
                'name' => 'SUV',
                'description' => 'Sports utility vehicle',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'VTY-TRUCK',
                'name' => 'Truck',
                'description' => 'Commercial truck',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'VTY-MOTOR',
                'name' => 'Motorcycle',
                'description' => 'Two-wheeled vehicle',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Seed 5 kendaraan
        for ($i = 1; $i <= 5; $i++) {
            $vehicleId = DB::table('vehicle')->insertGetId([
                'owner_id' => DB::table('branches')->inRandomOrder()->first()->id,
                'type_id' => DB::table('vehicle_type')->inRandomOrder()->first()->id,
                'code' => 'KO-' . Str::upper(Str::random(6)), // example KO-00001
                'brand' => $faker->randomElement(['Toyota', 'Honda', 'Ford', 'BMW']),
                'model' => $faker->randomElement(['Camry', 'Civic', 'Focus', 'X5']),
                'color' => $faker->colorName,
                'license_plate' => Str::upper(Str::random(3)) . ' ' . $faker->numberBetween(10, 99),
                'transmission' => $faker->randomElement(['Automatic', 'Manual']),
                'fuel' => $faker->randomElement(['Gasoline', 'Diesel']),
                'mileage' => $faker->numberBetween(1000, 100000),
                'year' => $faker->year,
                'tax_year' => $faker->dateTimeBetween('-1 year')->format('Y-m-d'),
                'tax_five_year' => $faker->dateTimeBetween('now', '+5 years')->format('Y-m-d'),
                'inspected' => $faker->dateTimeBetween('-6 months')->format('Y-m-d'),
                'purchase_date' => $faker->dateTimeBetween('-5 years')->format('Y-m-d'),
                'purchase_price' => $faker->randomFloat(2, 10000, 50000),
                'description' => $faker->sentence(3, true),
                'origin' => $faker->country,
                'status' => $faker->randomElement(['Active', 'Maintenance', 'Inactive']),
                'photo' => 'https://www.shutterstock.com/image-vector/car-logo-icon-emblem-design-600nw-473088025.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Maintenance records
            DB::table('vehicle_maintenance_record')->insert([
                'vehicle_id' => $vehicleId,
                'code' => 'VMR-' . Str::upper(Str::random(6)),
                'mileage' => $faker->numberBetween(1000, 100000),
                'maintenance_date' => $faker->dateTimeBetween('-1 year')->format('Y-m-d'),
                'description' => $faker->sentence(3, true),
                'cost' => $faker->randomFloat(2, 100, 1000),
                'next_maintenance' => $faker->dateTimeBetween('now', '+1 year')->format('Y-m-d'),
                'status' => $faker->randomElement(['In Progress', 'Cancelled', 'Completed']),
                'notes' => $faker->paragraph,
                'photo' => 'https://w7.pngwing.com/pngs/89/668/png-transparent-auto-mechanic-car-repairman-auto-repair-man-auto-repair-technology-thumbnail.png',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Insurance policies
            DB::table('vehicle_insurance_policy')->insert([
                'vehicle_id' => $vehicleId,
                'code' => 'VIP-' . Str::upper(Str::random(6)),
                'insurance_provider' => $faker->company,
                'policy_number' => 'POL-' . Str::upper(Str::random(10)),
                'coverage_start' => $faker->dateTimeBetween('-1 year')->format('Y-m-d'),
                'coverage_end' => $faker->dateTimeBetween('now', '+1 year')->format('Y-m-d'),
                'premium' => $faker->randomFloat(2, 500, 5000),
                'notes' => $faker->paragraph,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Assignments
            DB::table('vehicle_assignment')->insert([
                'vehicle_id' => $vehicleId,
                'user_id' => DB::table('users')->inRandomOrder()->first()->id, // get employee id from employee table
                'code' => 'VAS-' . Str::upper(Str::random(6)),
                'assignment_date' => $faker->dateTimeBetween('-6 months')->format('Y-m-d'),
                'return_date' => $faker->dateTimeBetween('now', '+6 months')->format('Y-m-d'),
                'notes' => $faker->paragraph,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Reimbursements
            $status = $faker->randomElement(['Approved', 'Pending', 'Rejected']);
            $approvedAt = $status === 'Approved' ? $faker->dateTimeThisYear : null;
            $rejectedAt = $status === 'Rejected' ? $faker->dateTimeThisYear : null;

            DB::table('vehicle_reimbursement')->insert([
                'vehicle_id' => $vehicleId,
                'date_recorded' => $faker->dateTimeThisYear->format('Y-m-d'),
                'user_by' => DB::table('users')->inRandomOrder()->first()->id, // get employee id from employee table
                'fuel' => $faker->randomElement(['Petrol', 'Diesel']),
                'amount' => $faker->numberBetween(10, 100),
                'price' => $faker->randomFloat(2, 50, 500),
                'first_mileage' => $faker->numberBetween(1000, 50000),
                'last_mileage' => $faker->numberBetween(50001, 100000),
                'attachment_mileage' => 'mileage.jpg',
                'attachment_receipt' => 'receipt.jpg',
                'notes' => $faker->paragraph,
                'reason' => $faker->sentence(3, true),
                'status' => $status,
                'approved_at' => $approvedAt,
                'approved_by' => $status === 'Approved' ? DB::table('users')->inRandomOrder()->first()->id : null,
                'rejected_at' => $rejectedAt,
                'rejected_by' => $status === 'Rejected' ? DB::table('users')->inRandomOrder()->first()->id : null,
                'type' => $faker->randomElement(['Refueling', 'Parking', 'E-Toll']),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
