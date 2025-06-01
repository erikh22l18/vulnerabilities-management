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
use App\Domain\Organizations\Models\Organization; // Added for view composer
use Illuminate\Support\Facades\View; // Added for view composer

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

        // Share organizations with the registration view
        View::composer('auth.register', function ($view) {
            $view->with('organizations', Organization::orderBy('name')->get());
        });
    }
}
