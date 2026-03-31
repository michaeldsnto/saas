<?php

namespace App\Providers;

use App\Support\Tenant\TenantManager;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // TenantManager is a singleton so every model and service in the same
        // request reads the same active company context.
        $this->app->singleton(TenantManager::class, fn () => new TenantManager());
    }

    public function boot(): void
    {
        // Central place for app-wide boot logic. Keep tenant bootstrapping small
        // and let the dedicated middleware handle per-request context.
    }
}
