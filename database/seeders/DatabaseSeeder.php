<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Create Admin User
        User::create([
            'name' => 'Alsaeyd J Alakhras',
            'email' => 'alsaeydjalkhras@gmail.com',
            'password'  => '20051118Jamal',
            'username'  => 'saeyd_jamal',
            'last_activity'  => now(),
            'avatar'  => null,
            'super_admin'  => 1,
        ]);
    }
}
