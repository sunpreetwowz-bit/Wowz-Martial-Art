<?php

namespace Database\Seeders;

use App\Enums\AccountStatus;
use App\Models\Belt;
use Illuminate\Database\Seeder;

class BeltSeeder extends Seeder
{
    public function run(): void
    {
        $belts = [
            ['name' => 'White Belt', 'color' => '#FFFFFF', 'rank_order' => 1],
            ['name' => 'Yellow Belt', 'color' => '#FACC15', 'rank_order' => 2],
            ['name' => 'Green Belt', 'color' => '#22C55E', 'rank_order' => 3],
            ['name' => 'Blue Belt', 'color' => '#3B82F6', 'rank_order' => 4],
            ['name' => 'Red Belt', 'color' => '#EF4444', 'rank_order' => 5],
            ['name' => 'Black Belt', 'color' => '#111827', 'rank_order' => 6],
        ];

        foreach ($belts as $belt) {
            Belt::query()->updateOrCreate(
                ['rank_order' => $belt['rank_order']],
                [
                    'name' => $belt['name'],
                    'color' => $belt['color'],
                    'description' => $belt['name'].' rank.',
                    'status' => AccountStatus::Active,
                ]
            );
        }
    }
}
