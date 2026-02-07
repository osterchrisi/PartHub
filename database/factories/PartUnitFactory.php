<?php

namespace Database\Factories;

use App\Models\PartUnit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PartUnit>
 */
class PartUnitFactory extends Factory
{
    protected $model = PartUnit::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'unit_name' => $this->faker->randomElement(['pcs', 'cm', 'kg', 'ml', 'oz']),
        ];
    }
}
