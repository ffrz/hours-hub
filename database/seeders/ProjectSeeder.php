<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Project::factory()->create([
            'name' => 'Hours Hub',
            'client_id' => null,
        ]);
        Project::factory()->create([
            'name' => 'Tahfidz Monitoring',
            'client_id' => null,
        ]);
        Project::factory()->create([
            'name' => 'EPPDB',
            'client_id' => null,
        ]);
        Project::factory()->create([
            'name' => 'SPPIE',
            'client_id' => null,
        ]);
        Project::factory()->create([
            'name' => 'Shiftech POS Desktop',
            'client_id' => null,
        ]);
        Project::factory()->create([
            'name' => 'Shiftech POS Web',
            'client_id' => null,
        ]);
    }
}
