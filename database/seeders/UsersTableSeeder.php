<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run() : void
    {
        User::create([
            'first_name' => 'Super',
            'last_name' => 'Administrator',
            'email' => 'admin@example.com',
            'password' => bcrypt('Password'),
            'is_admin' => true,
        ]);

        User::factory(50)->create([
            'password' => bcrypt('Password'),
            'is_admin' => false,
        ]);
    }
}
