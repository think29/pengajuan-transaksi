<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [

            [
                'name' => 'Administrator',
                'email' => 'admin@company.test',
                'role' => 'Admin',
            ],

            [
                'name' => 'Supervisor',
                'email' => 'spv@company.test',
                'role' => 'SPV',
            ],

            [
                'name' => 'Manager',
                'email' => 'manager@company.test',
                'role' => 'Manager',
            ],

            [
                'name' => 'Director',
                'email' => 'director@company.test',
                'role' => 'Director',
            ],

            [
                'name' => 'Finance',
                'email' => 'finance@company.test',
                'role' => 'Finance',
            ],

            [
                'name' => 'Staff',
                'email' => 'staff@company.test',
                'role' => 'Staff',
            ],

        ];

        foreach ($users as $data) {

            $user = User::updateOrCreate(

                [
                    'email' => $data['email']
                ],

                [
                    'name' => $data['name'],
                    'password' => Hash::make('password'),
                ]

            );

            $user->syncRoles([$data['role']]);
        }
    }
}