<?php

namespace Database\Factories;

use App\Models\Location;
use App\Models\Part;
use App\Models\StockLevel;
use Illuminate\Database\Eloquent\Factories\Factory;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StockLevel>
 */
class StockLevelFactory extends Factory
{

    protected $model = StockLevel::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'part_id_fk' => Part::factory(),
            'location_id_fk' => Location::factory(),
            'stock_level_quantity' => $this->faker->numberBetween(1, 100),
        ];
    }
}
