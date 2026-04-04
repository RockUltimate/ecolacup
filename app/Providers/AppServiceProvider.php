<?php

namespace App\Providers;

use App\Models\Kun;
use App\Models\Osoba;
use App\Policies\KunPolicy;
use App\Policies\OsobaPolicy;
use Illuminate\Support\Facades\Gate;
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
        Gate::policy(Osoba::class, OsobaPolicy::class);
        Gate::policy(Kun::class, KunPolicy::class);
    }
}
