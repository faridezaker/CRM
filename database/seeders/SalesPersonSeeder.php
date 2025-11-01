<?php

namespace Database\Seeders;

use App\Models\SalesPerson;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SalesPersonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SalesPerson::factory()->count(10)->create();
    }
}
