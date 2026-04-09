<?php

namespace App\Providers;

use App\Models\Kun;
use App\Models\Osoba;
use App\Models\Prihlaska;
use App\Policies\KunPolicy;
use App\Policies\OsobaPolicy;
use App\Policies\PrihlaskaPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

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
        // Ignore stray public/hot files outside local development so production
        // always uses the built assets from public/build.
        if (! $this->app->isLocal()) {
            Vite::useHotFile(storage_path('framework/vite.hot'));
        }

        $appUrlScheme = strtolower((string) parse_url((string) config('app.url'), PHP_URL_SCHEME));

        if (
            $this->app->environment('production')
            && $appUrlScheme === 'https'
            && $this->shouldForceHttps(request())
        ) {
            URL::forceScheme('https');
        }

        Gate::policy(Osoba::class, OsobaPolicy::class);
        Gate::policy(Kun::class, KunPolicy::class);
        Gate::policy(Prihlaska::class, PrihlaskaPolicy::class);

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

    private function shouldForceHttps(SymfonyRequest $request): bool
    {
        $host = strtolower((string) $request->getHost());

        if (in_array($host, ['localhost', '127.0.0.1', '::1'], true)) {
            return false;
        }

        if (filter_var($host, FILTER_VALIDATE_IP) !== false) {
            return filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false;
        }

        return ! Str::endsWith($host, ['.local', '.test', '.internal', '.lan']);
    }
}
