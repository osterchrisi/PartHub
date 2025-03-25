<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory{
    protected $model = Category::class;

    public function definition(): array {
        return [
            'category_name' => $this->faker->word,
            'parent_category' => 1, // You can override this if needed
            'part_category_owner_u_fk' => User::factory(), // Generates a user if not passed explicitly
        ];
    }
};