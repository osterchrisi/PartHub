<?php

namespace Database\Factories;

use App\Models\Part;
use App\Models\Supplier;
use App\Models\SupplierData;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SupplierData>
 */
class SupplierDataFactory extends Factory
{

    protected $model = SupplierData::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'part_id_fk' => Part::factory(),
            'supplier_id_fk' => Supplier::factory(),
            'URL' => $this->faker->url,
            'SPN' => strtoupper($this->faker->bothify('SPN-####')),
            'price' => $this->faker->randomFloat(2, 0.1, 50),
            'supplier_data_owner_u_fk' => User::factory(),
        ];
    }
}
