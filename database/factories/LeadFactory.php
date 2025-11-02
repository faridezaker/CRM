<?php

namespace Database\Factories;

use App\Models\Contact;
use App\Models\Pipeline;
use App\Models\SalesPerson;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lead>
 */
class LeadFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'contact_id' => Contact::inRandomOrder()->first()?->id,
            'assigned_to' => SalesPerson::inRandomOrder()->first()?->id,
            'pipeline'=>$this->faker->randomElement(['Registered','Purchased','Expired','Follow up']),
            'status' => $this->faker->randomElement(['active', 'archived']),
        ];
    }
}
