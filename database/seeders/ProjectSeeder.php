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
            'name' => 'Project 1',
            'client_id' => 1,
        ]);
        Project::factory()->create([
            'name' => 'Project 2',
            'client_id' => null,
        ]);
        Project::factory()->create([
            'name' => 'Project 3',
            'client_id' => 4,
        ]);
        Project::factory(5)->create();
    }
}
