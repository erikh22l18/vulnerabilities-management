<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Domain\Vulnerabilities\Models\Vulnerability; // Añadido
use App\Observers\VulnerabilityObserver; // Añadido
use Illuminate\Support\Facades\Event;
use Illuminate\Database\Eloquent\Events\PivotAttached;
use Illuminate\Database\Eloquent\Events\PivotDetached;
use App\Listeners\HandlePivotAttached;
use App\Listeners\HandlePivotDetached;
use Illuminate\Auth\Events\Authenticated;
use App\Listeners\LogSuccessfulLogin;

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
        Vulnerability::observe(VulnerabilityObserver::class); // Añadido
        Event::listen(PivotAttached::class, [HandlePivotAttached::class, 'handle']);
        Event::listen(PivotDetached::class, [HandlePivotDetached::class, 'handle']);
        Event::listen(
            Authenticated::class,
            [LogSuccessfulLogin::class, 'handle']
        );
    }
}
