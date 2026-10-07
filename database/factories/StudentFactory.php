<?php

namespace Database\Factories;

use App\Enums\AccountStatus;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Student>
 */
class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->student(),
            'student_code' => 'WMA-'.fake()->unique()->numerify('2026-####'),
            'phone' => fake()->numerify('98########'),
            'date_of_birth' => fake()->date(),
            'joining_date' => now()->toDateString(),
            'status' => AccountStatus::Active,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AccountStatus::Inactive,
        ]);
    }
}
