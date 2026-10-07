<?php

namespace Database\Factories;

use App\Enums\AccountStatus;
use App\Models\Belt;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Belt>
 */
class BeltFactory extends Factory
{
    protected $model = Belt::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true).' Belt',
            'color' => fake()->hexColor(),
            'rank_order' => fake()->unique()->numberBetween(1, 500),
            'description' => fake()->sentence(),
            'status' => AccountStatus::Active,
        ];
    }
}
