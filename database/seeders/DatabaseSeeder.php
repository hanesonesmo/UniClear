<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

//default admin account

class DatabaseSeeder extends Seeder
{

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
      $departments = [
        ['department_name' => 'Library Department', 'description' => 'Handles book loans and returns.'],
        ['department_name' => 'Finance Department', 'description' => 'Manages tuition fees and financial records.'],
        ['department_name' => 'Academic Department', 'description' => 'Oversees course registration and academic records.'],
        ['department_name' => 'Examination Department', 'description' => 'Provides examination services and manages exam records.'],
        ['department_name' => 'IT Department', 'description' => 'Manages campus technology and support.'],
        ['department_name' => 'Hostel Department', 'description' => 'Oversees student accommodation and related services.'],
        ['department_name' => 'Student Affairs Department', 'description' => 'Handles student activities, counseling, and support services.'],
      ];

      foreach ($departments as $dept) {
        DB::table('departments')->insert(array_merge($dept, [
            'created_at' => now(),
            'updated_at' => now(),
        ]));
      }

      //Create the system adminstrator account with a default password
      DB::table('users')->insert([
        'name' => 'Admin User',
        'email' => 'admin@uniclear.com',
        'password' => Hash::make('Admin@12345'),
        'department_id' => null,
        'role' => 'admin',
        'created_at' => now(),
        'updated_at' => now(),
      ]);

//Creating one sample for staff account per department for testing purposes
$staffAccounts = [
    ['name' => 'Library Staff', 'email' => 'library@uniclear.com', 'department_id' => 1],

    ['name' => 'Finance Staff', 'email' => 'finance@uniclear.com', 'department_id' => 2],

    ['name' => 'Academic Staff', 'email' => 'academic@uniclear.com', 'department_id' => 3],

    ['name' => 'Examination Staff', 'email' => 'examination@uniclear.com', 'department_id' => 4],

    ['name' => 'IT Staff', 'email' => 'it@uniclear.com', 'department_id' => 5],

    ['name' => 'Hostel Staff', 'email' => 'hostel@uniclear.com', 'department_id' => 6],

    ['name' => 'Student Affairs Staff', 'email' => 'studentaffairs@uniclear.com', 'department_id' => 7]
];

foreach ($staffAccounts as $staff) {
    DB::table('users')->insert([
        'name' => $staff['name'],
        'email' => $staff['email'],
        'password' => Hash::make('Staff@12345'),
        'department_id' => $staff['department_id'],
        'role' => 'staff',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}
    }
}
