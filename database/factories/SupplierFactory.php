<?php

namespace Database\Factories;

use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SupplierFactory extends Factory
 {
    protected $model = Supplier::class;

    public function definition(): array {
        return [
            'supplier_name' => $this->faker->company,
            'supplier_owner_g_fk' => null,
            'supplier_owner_u_fk' => User::factory(),
        ];
    }
};