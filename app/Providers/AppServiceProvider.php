<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $deployedPublicPath = realpath(base_path('../stockscan_app'));
        $defaultPublicPath = realpath(base_path('public'));
        $resolvedPublicPath = $deployedPublicPath ?: $defaultPublicPath;

        if ($resolvedPublicPath !== false) {
            config([
                'dompdf.public_path' => $resolvedPublicPath,
                'dompdf.options.chroot' => array_values(array_filter([
                    realpath(base_path()),
                    $resolvedPublicPath,
                ])),
            ]);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip() . '|' . $request->input('username'));
        });
    }
}
