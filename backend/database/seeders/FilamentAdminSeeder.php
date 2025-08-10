<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FilamentAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $email = 'admin@example.com';

        if (User::where('email', $email)->exists()) {
            $this->command->info("Admin user already exists. $email");

            return;
        }

        $user = User::create([
            'name' => 'Admin',
            'email' => $email,
            'password' => bcrypt('password'),
        ]);

        $this->command->info("Admin user created. Email: $email / password: password");
    }
}
