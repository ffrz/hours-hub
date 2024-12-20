<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Factories\UserFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UserFactory::$defaultPassword = Hash::make('12345');
        User::factory()->create([
            'username' => 'ffr',
            'name' => 'Fahmi Fauzi Rahman',
            'email' => 'ffr@shiftech.my.id',
            'role' => 'admin',
            'active' => 1,
        ]);
        User::factory()->create([
            'username' => 'fahmi',
            'name' => 'Fahmi',
            'email' => 'fahmi@shiftech.my.id',
            'role' => 'user',
            'active' => 1,
        ]);
        User::factory(5)->create();
    }
}
