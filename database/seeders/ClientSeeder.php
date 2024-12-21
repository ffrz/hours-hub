<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Client::factory()->create([
            'name' => 'BS Motor Putra',
            'phone' => '081212341234',
            'email' => 'bsmotorputra@gmail.com',
            'address' => 'Talaga',
            'active' => 1,
        ]);
        Client::factory()->create([
            'name' => 'BS Guvilli Mobilindo',
            'phone' => '085112341234',
            'email' => 'bsguvillimobilindo@gmail.com',
            'address' => 'Talaga',
            'active' => 1,
        ]);
        Client::factory(5)->create();
    }
}
