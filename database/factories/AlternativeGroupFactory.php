<?php

namespace Database\Factories;

use App\Models\AlternativeGroup;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AlternativeGroup>
 */
class AlternativeGroupFactory extends Factory
{

    protected $model = AlternativeGroup::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'owner_u_fk' => User::factory(),
        ];
    }
}
