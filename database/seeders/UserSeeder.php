<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Company;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Warehouses;
use App\Models\Employe;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define permissions
        $permissions = [
            // users
            'view profile',
            'update profile',

            // Manage Users
            'view users',
            'show users',
            'create users',
            'update users',
            'delete users',

            'view roles',
            'show roles',
            'create roles',
            'assign roles',
            'update roles',
            'delete roles',

            'view permissions',
            'create permissions',
            'update permissions',
            'delete permissions',

            // Manage Companies
            'view companies',
            'show companies',
            'create companies',
            'update companies',
            'delete companies',

            // Manage Branches
            'view branches',
            'show branches',
            'create branches',
            'update branches',
            'delete branches',

            // Manage Departments
            'view departments',
            'show departments',
            'create departments',
            'update departments',
            'delete departments',

            // Manage Warehouses
            'view warehouses',
            'show warehouses',
            'create warehouses',
            'update warehouses',
            'delete warehouses',

            // Manage Employees
            'view employees',
            'show employees',
            'create employees',
            'update employees',
            'delete employees',

            // Manage Tickets
            'view ticketing categories',
            'create ticketing categories',
            'update ticketing categories',
            'delete ticketing categories',

            'view ticketing',
            'show ticketing',
            'create ticketing',
            'update ticketing',
            'handle ticketing',
            'comment ticketing',
            'solved ticketing',
            'cenceled ticketing',
            'delete ticketing',

            // Manage Contracts
            'view contracts',
            'show contracts',
            'create contracts',
            'update contracts',
            'print contracts',
            'delete contracts',

            // Manage Tools
            'view tools categories',
            'create tools categories',
            'update tools categories',
            'delete tools categories',

            'view tools',
            'show tools',
            'create tools',
            'update tools',
            'delete tools',

            'view projects',
            'show projects',
            'create projects',
            'update projects',
            'delete projects',

            'view tools transactions',
            'show tools transactions',
            'print tools transactions',
            'create tools transactions',
            'update tools transactions',
            'delete tools transactions',

            'view tools maintenances',
            'show tools maintenances',
            'create tools maintenances',
            'update tools maintenances',
            'complete tools maintenances',
            'cancel tools maintenances',
            'delete tools maintenances',

            // Manage Vehicles
            'view vehicle types',
            'create vehicle types',
            'update vehicle types',
            'delete vehicle types',

            'view vehicles',
            'show vehicles',
            'create vehicles',
            'update vehicles',
            'assign vehicles',
            'delete vehicles',

            'view vehicle maintenances',
            'show vehicle maintenances',
            'create vehicle maintenances',
            'update vehicle maintenances',
            'complete vehicle maintenances',
            'cancel vehicle maintenances',
            'print vehicle maintenances',
            'delete vehicle maintenances',

            'view vehicle insurances',
            'show vehicle insurances',
            'create vehicle insurances',
            'update vehicle insurances',
            'print vehicle insurances',
            'delete vehicle insurances',

            'view vehicle reimbursements',
            'show vehicle reimbursements',
            'create vehicle reimbursements',
            'update vehicle reimbursements',
            'approved vehicle reimbursements',
            'rejected vehicle reimbursements',
            'export vehicle reimbursements',
            'delete vehicle reimbursements',

            // Manage Incoming
            'view incoming suppliers',
            'show incoming suppliers',
            'create incoming suppliers',
            'update incoming suppliers',
            'delete incoming suppliers',

            'view incoming plan',
            'show incoming plan',
            'create incoming plan',
            'update incoming plan',
            'print incoming plan',
            'delete incoming plan',

            // Manage Cost Bids Analysis
            'view bids analysis',
            'show bids analysis',
            'create bids analysis',
            'update bids analysis',
            'print bids analysis',
            'delete bids analysis',

            // Manage Patty Cash
            'view petty cash',
            'show petty cash',
            'create petty cash',
            'export petty cash',
            'approve petty cash',
            'add saldo',

        ];

        // Create or update permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles
        $rolesPermissions = [
            'Superadmin' => Permission::all(), // Assign all permissions to Superadmin
            'Account Payyable' => [
                'view profile',
            ],
            'Budget Control' => [
                'view profile',
            ],  // Define specific permissions here if needed
            'Manager' => [
                'view profile',
            ], // Define specific permissions here if needed
            'Area Manager' => [
                'view profile',
            ], // Define specific permissions here if needed
            'Admin Warehouse' => [
                'view profile',
            ], // Define specific permissions here if needed
            'Admin Legal' => [
                'view profile',

                'view contracts',
                'show contracts',
                'create contracts',
                'update contracts',
                'print contracts',
                'delete contracts',
            ], // Define specific permissions here if needed
            'Admin Branch' => [
                'view profile',

                'view petty cash',
                'show petty cash',
                'create petty cash',
                'export petty cash',
            ], // Define specific permissions here if needed
            'Admin GA' => [
                'view profile',

                'view vehicle types',
                'create vehicle types',
                'update vehicle types',
                'delete vehicle types',

                'view vehicles',
                'show vehicles',
                'create vehicles',
                'assign vehicles',
                'update vehicles',
                'delete vehicles',

                'view vehicle reimbursements',
                'show vehicle reimbursements',
                'create vehicle reimbursements',
                'update vehicle reimbursements',
                'export vehicle reimbursements',
            ], // Define specific permissions here if needed
            'Admin Purchasing' => [
                'view profile',

                'view incoming suppliers',
                'show incoming suppliers',
                'create incoming suppliers',
                'update incoming suppliers',
                'delete incoming suppliers',

                'view incoming plan',
                'show incoming plan',
                'create incoming plan',
                'update incoming plan',
                'print incoming plan',
                'delete incoming plan',

                'view bids analysis',
                'show bids analysis',
                'create bids analysis',
                'update bids analysis',
                'print bids analysis',
                'delete bids analysis',
            ], // Define specific permissions here if needed
            'Admin Project' => [
                'view profile',

                'view tools categories',
                'create tools categories',
                'update tools categories',
                'delete tools categories',

                'view tools',
                'show tools',
                'create tools',
                'update tools',
                'delete tools',

                'view projects',
                'show projects',
                'create projects',
                'update projects',
                'delete projects',

                'view tools transactions',
                'show tools transactions',
                'create tools transactions',
                'update tools transactions',
                'print tools transactions',
                'delete tools transactions',
            ], // Define specific permissions here if needed
            'Staff' => [], // Define specific permissions here if needed
            'User' => [], // Define specific permissions here if needed
        ];

        foreach ($rolesPermissions as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->syncPermissions($rolePermissions);
        }

        // Create users
        $users = [
            // Superadmin
            [
                'name' => 'Super',
                'username' => 'superadmin',
                'last_name' => 'Admin',
                'email' => 'superadmin@example.com',
                'password' => Hash::make('password'),
                'role' => 'Superadmin',
            ],
            // Manager
            [
                'name' => 'Manager',
                'username' => 'manager',
                'last_name' => 'Manager',
                'email' => 'manager@example.com',
                'password' => Hash::make('password'),
                'role' => 'Manager',
            ],
            // Area Manager
            [
                'name' => 'Area',
                'username' => 'areamanager',
                'last_name' => 'Manager',
                'email' => 'areamanager@example.com',
                'password' => Hash::make('password'),
                'role' => 'Area Manager',
            ],
            // Admin Department
            [
                'name' => 'Admin',
                'username' => 'adminlegal',
                'last_name' => 'Legal',
                'email' => 'adminlegal@example.com',
                'password' => Hash::make('password'),
                'role' => 'Admin Legal',
            ],
            [
                'name' => 'Admin',
                'username' => 'adminga',
                'last_name' => 'GA',
                'email' => 'adminga@example.com',
                'password' => Hash::make('password'),
                'role' => 'Admin GA',
            ],
            [
                'name' => 'Admin',
                'username' => 'adminpurchasing',
                'last_name' => 'Purchasing',
                'email' => 'adminpurchasing@example.com',
                'password' => Hash::make('password'),
                'role' => 'Admin Purchasing',
            ],
            // Admin Branch
            [
                'name' => 'Admin',
                'username' => 'adminbranch',
                'last_name' => 'Branch 1',
                'email' => 'adminbranch@example.com',
                'password' => Hash::make('password'),
                'role' => 'Admin Branch',
            ],
            // Admin Warehouse
            [
                'name' => 'Admin',
                'username' => 'adminwarehouse',
                'last_name' => 'Warehouse 1',
                'email' => 'adminwarehouse@example.com',
                'password' => Hash::make('password'),
                'role' => 'Admin Warehouse',
            ],
            // Admin Project
            [
                'name' => 'Admin',
                'username' => 'adminproject',
                'last_name' => 'Project',
                'email' => 'adminproject@example.com',
                'password' => Hash::make('password'),
                'role' => 'Admin Project',
            ],

        ];

        // Seeder untuk users
        foreach ($users as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'username' => $userData['username'],
                    'last_name' => $userData['last_name'],
                    'password' => Hash::make('password'), // Gunakan hashing
                    'email_verified_at' => now(),
                    'remember_token' => Str::random(10),
                ]
            );
            $user->assignRole($userData['role']);
        }

        // Seeder untuk employees
        $employees = [];
        foreach (User::all() as $user) {
            $employees[] = [
                'user_id' => $user->id,
                'company_id' => 1,
                'branch_id' => 1,
                'department_id' => 1,
                'code' => 'EMP' . str_pad($user->id, 3, '0', STR_PAD_LEFT), // ID dinamis
                // 'nik' => 'NIK' . str_pad($user->id, 8, '0', STR_PAD_LEFT), // NIK dinamis
                'full_name' => 'Employee ' . $user->last_name, // Nama dinamis
                'gender' => 'Male',
                // 'age' => '30',
                'phone' => '62895341341001',
                'position' => 'Admin',
                'address' => 'Jl. Contoh, No. 123, Contoh, Kec. Contoh, Kota Contoh, Prov. Contoh',
                'status' => 'Active',
                'photo' => 'photo1.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Masukkan semua data employees dalam sekali query
        Employe::insert($employees);
    }
}
