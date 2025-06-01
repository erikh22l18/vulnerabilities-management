<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Domain\Projects\Models\Project; // For AdminDashboardService data
use App\Domain\Tasks\Models\Task;       // For MiembroDashboardService data
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser, $liderUser, $miembroUser, $unassignedUser;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed basic roles and permissions. Adjust if your seeder is different.
        $this->artisan('db:seed', ['--class' => 'RolePermissionSeeder']);

        $this->adminUser = User::factory()->create();
        $this->adminUser->assignRole('admin');

        $this->liderUser = User::factory()->create();
        $this->liderUser->assignRole('lider');

        $this->miembroUser = User::factory()->create();
        $this->miembroUser->assignRole('miembro');

        $this->unassignedUser = User::factory()->create(); // User with no specific dashboard role

        // Create some dummy data that services might pick up
        Project::factory()->count(3)->create();
        User::factory()->count(2)->create(); // Other users
        Task::factory()->create(['assigned_to' => $this->miembroUser->id, 'status' => 'Pendiente']);
    }

    /** @test */
    public function admin_sees_admin_dashboard_with_correct_data()
    {
        $response = $this->actingAs($this->adminUser)->get(route('dashboard'));

        $response->assertOk();
        $response->assertViewIs('dashboard');
        $response->assertViewHas('dashboard_type', 'admin');
        $response->assertViewHas('service_data', function ($data) {
            return isset($data['total_projects']) &&
                   isset($data['total_users']) &&
                   isset($data['global_alerts']) &&
                   is_array($data['global_alerts']);
        });
        // Check for specific sample alert message
        $response->assertSeeText('Sistema: Actualización de seguridad programada para medianoche.');
        // Check for a metric display
        $response->assertSeeText('Total de Proyectos');
    }

    /** @test */
    public function lider_sees_lider_dashboard_with_correct_data()
    {
        // Assign this lider to a project for the projects_led_count
        Project::factory()->create(['lider_id' => $this->liderUser->id]);

        $response = $this->actingAs($this->liderUser)->get(route('dashboard'));

        $response->assertOk();
        $response->assertViewIs('dashboard');
        $response->assertViewHas('dashboard_type', 'lider');
        $response->assertViewHas('service_data', function ($data) {
            return isset($data['projects_led_count']) &&
                   isset($data['lider_alerts']) &&
                   is_array($data['lider_alerts']);
        });
        $response->assertSeeText('Proyectos Liderados');
        $response->assertSeeText('Proyecto Beta: Tarea TASK-005 vencida.');
    }

    /** @test */
    public function miembro_sees_miembro_dashboard_with_correct_data()
    {
        $response = $this->actingAs($this->miembroUser)->get(route('dashboard'));

        $response->assertOk();
        $response->assertViewIs('dashboard');
        $response->assertViewHas('dashboard_type', 'miembro');
        $response->assertViewHas('service_data', function ($data) {
            return isset($data['my_active_assigned_tasks_count']) &&
                   isset($data['personal_alerts']) &&
                   is_array($data['personal_alerts']);
        });
        $response->assertSeeText('Mis Tareas Activas Asignadas');
        $response->assertSeeText('Tarea TASK-010 asignada a usted vence mañana.');
    }

    /** @test */
    public function user_with_unhandled_role_sees_default_dashboard_content()
    {
        // This user has no specific dashboard role assigned in DashboardController logic
        $response = $this->actingAs($this->unassignedUser)->get(route('dashboard'));

        $response->assertOk();
        $response->assertViewIs('dashboard');
        $response->assertViewHas('dashboard_type', 'default'); // As per DashboardController logic
        $response->assertViewHas('service_data', []); // Expect empty array for default

        // Check for text that might appear in the default/fallback part of dashboard.blade.php
        $response->assertSeeText('Dashboard General');
        // Or, if it was an error message:
        // $response->assertSeeText('Dashboard no disponible para su rol.');
    }
}
