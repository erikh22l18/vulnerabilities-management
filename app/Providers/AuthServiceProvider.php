<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

// Models
use App\Domain\Projects\Models\Project;
use App\Domain\Vulnerabilities\Models\Vulnerability;
use App\Domain\Tasks\Models\Task;
use App\Domain\Organizations\Models\Organization;
use App\Models\User;

// Policies
use App\Domain\Projects\Policies\ProjectPolicy;
use App\Domain\Vulnerabilities\Policies\VulnerabilityPolicy;
use App\Domain\Tasks\Policies\TaskPolicy;
use App\Domain\Organizations\Policies\OrganizationPolicy;
use App\Domain\Users\Policies\UserPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Project::class => ProjectPolicy::class,
        Vulnerability::class => VulnerabilityPolicy::class,
        Task::class => TaskPolicy::class,
        Organization::class => OrganizationPolicy::class,
        User::class => UserPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Implicitly grant 'admin' role all permissions
        // This works globally, before any other Gate checks
        Gate::before(function ($user, $ability) {
            return $user->hasRole('admin') ? true : null;
        });
    }
}
