<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create default users
        User::create([
            'name' => 'Admin',
            'email' => 'admin@smartclass.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Bendahara',
            'email' => 'bendahara@smartclass.com',
            'password' => bcrypt('password'),
            'role' => 'bendahara',
        ]);

        User::create([
            'name' => 'Sekretaris',
            'email' => 'sekretaris@smartclass.com',
            'password' => bcrypt('password'),
            'role' => 'sekretaris',
        ]);

        // Create some students
        $students = [
            ['name' => 'Siswa 1', 'email' => 'siswa1@smartclass.com'],
            ['name' => 'Siswa 2', 'email' => 'siswa2@smartclass.com'],
            ['name' => 'Siswa 3', 'email' => 'siswa3@smartclass.com'],
        ];

        foreach ($students as $student) {
            User::create([
                'name' => $student['name'],
                'email' => $student['email'],
                'password' => bcrypt('password'),
                'role' => 'siswa',
            ]);
        }

        // Generate weekly payments
        $this->call(WeeklyPaymentSeeder::class);
    }
}
