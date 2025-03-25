<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Part;
use App\Models\User;
use App\Models\Category;
use App\Models\Footprint;
use App\Models\Supplier;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Part>
 */
class PartFactory extends Factory
{

    protected $model = Part::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'part_name' => $this->faker->word,
            'part_description' => $this->faker->sentence,
            'part_comment' => $this->faker->sentence,
            'part_category_fk' => Category::factory(),
            'part_footprint_fk' => Footprint::factory(),
            'part_unit_fk' => 1, // assuming unit 1 exists or change accordingly
            'part_owner_u_fk' => User::factory(),
            'part_supplier_fk' => Supplier::factory(),
            'stocklevel_notification_threshold' => 0,
        ];
    }
}
