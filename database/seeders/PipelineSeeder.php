<?php

namespace Database\Seeders;

use App\Models\Pipeline;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;


class PipelineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pipelines = [
            ['name' => 'Registered', 'slug' => 'registered'],
            ['name' => 'Purchased', 'slug' => 'purchased'],
            ['name' => 'Expired', 'slug' => 'expired'],
            ['name' => 'Follow up', 'slug' => 'follow_up'],
        ];
        foreach ($pipelines as $pipeline) {
            Pipeline::create($pipeline);
        }
    }
}
