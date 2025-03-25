<?php

namespace Database\Factories;

use App\Models\Footprint;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FootprintFactory extends Factory
 {
    protected $model = Footprint::class;

    public function definition(): array {
        return [
            'footprint_name' => $this->faker->word,
            'footprint_alias' => $this->faker->optional()->word,
            'footprint_owner_u_fk' => User::factory(),
        ];
    }
};