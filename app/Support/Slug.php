<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Make a unique URL slug like "yellow-belt-test" or "yellow-belt-test-2".
 */
class Slug
{
    public static function unique(string $value, string $table, string $column = 'slug', ?int $ignoreId = null): string
    {
        $base = Str::slug($value);
        if ($base === '') {
            $base = 'item';
        }

        $slug = $base;
        $number = 2;

        while (self::exists($table, $column, $slug, $ignoreId)) {
            $slug = $base.'-'.$number;
            $number++;
        }

        return $slug;
    }

    protected static function exists(string $table, string $column, string $slug, ?int $ignoreId): bool
    {
        $query = DB::table($table)->where($column, $slug);

        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        return $query->exists();
    }
}
