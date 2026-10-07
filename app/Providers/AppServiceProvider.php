<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::useTailwind();

        // On shared hosting, wrong APP_URL breaks image/CSS links.
        // Prefer the live request host when the app is served over HTTP.
        if (! $this->app->runningInConsole() && config('app.url')) {
            $request = request();
            if ($request && $request->getHost()) {
                URL::forceRootUrl($request->getSchemeAndHttpHost());
            }
        }
    }
}
