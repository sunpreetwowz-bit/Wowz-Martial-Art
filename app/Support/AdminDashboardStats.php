<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

class AdminDashboardStats
{
    public const CACHE_KEY = 'admin.dashboard.stats';

    public static function forget(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
