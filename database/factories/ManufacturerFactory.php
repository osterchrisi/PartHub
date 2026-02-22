<?php

namespace Database\Factories;

use App\Models\Manufacturer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ManufacturerFactory extends Factory
{
    protected $model = Manufacturer::class;

    public function definition(): array
    {
        return [
            'manufacturer_name' => $this->faker->company(),
            'manufacturer_owner_u_fk' => User::factory(),
        ];
    }

    /**
     * Explicitly bind to a given user.
     */
    public function forUser(User $user): static
    {
        return $this->state(fn () => [
            'manufacturer_owner_u_fk' => $user->id,
        ]);
    }
}
