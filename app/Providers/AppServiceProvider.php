<?php

namespace App\Providers;

use App\Models\Kun;
use App\Models\Osoba;
use App\Policies\KunPolicy;
use App\Policies\OsobaPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
        Gate::policy(Osoba::class, OsobaPolicy::class);
        Gate::policy(Kun::class, KunPolicy::class);

        RateLimiter::for('auth-login', function (Request $request) {
            return Limit::perMinute(5)->by((string) $request->ip());
        });

        RateLimiter::for('auth-register', function (Request $request) {
            return Limit::perMinute(3)->by((string) $request->ip());
        });

        RateLimiter::for('prihlasky-store', function (Request $request) {
            $key = $request->user()?->id ? 'user:'.$request->user()->id : 'ip:'.$request->ip();

            return Limit::perMinute(20)->by($key);
        });
    }
}
