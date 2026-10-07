<?php

namespace Database\Factories;

use App\Enums\BeltTestStatus;
use App\Models\Belt;
use App\Models\BeltTest;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<BeltTest>
 */
class BeltTestFactory extends Factory
{
    protected $model = BeltTest::class;

    public function definition(): array
    {
        $title = 'Grading '.fake()->unique()->monthName().' '.now()->year;

        return [
            'title' => $title,
            'slug' => Str::slug($title).'-'.fake()->unique()->numerify('###'),
            'target_belt_id' => Belt::factory(),
            'test_date' => now()->addDays(7)->toDateString(),
            'start_time' => '17:00:00',
            'end_time' => '19:00:00',
            'application_opens_at' => now()->subDay(),
            'application_closes_at' => null,
            'fee_amount' => 500,
            'currency' => 'INR',
            'instructions' => 'Bring your uniform and ID.',
            'status' => BeltTestStatus::Open,
        ];
    }
}
