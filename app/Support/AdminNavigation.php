<?php

namespace App\Support;

use Illuminate\Support\Facades\Route;

class AdminNavigation
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public static function items(): array
    {
        return collect(config('admin.navigation', []))
            ->map(fn (array $item) => self::hydrate($item))
            ->all();
    }

    /**
     * @param  array<string, mixed>  $item
     * @return array<string, mixed>
     */
    protected static function hydrate(array $item): array
    {
        if (isset($item['children'])) {
            $item['children'] = collect($item['children'])
                ->map(fn (array $child) => self::hydrate($child))
                ->all();

            $item['available'] = collect($item['children'])->contains(fn (array $child) => $child['available']);
            $item['active'] = collect($item['children'])->contains(fn (array $child) => $child['active']);

            return $item;
        }

        $route = $item['route'] ?? null;
        $available = is_string($route) && Route::has($route);

        $item['available'] = $available;
        $item['url'] = $available ? route($route) : null;
        $item['active'] = $available && request()->routeIs($route, $route.'.*');

        return $item;
    }
}
