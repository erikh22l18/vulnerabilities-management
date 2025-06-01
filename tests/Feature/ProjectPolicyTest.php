<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Domain\Projects\Models\Project;
use App\Domain\Vulnerabilities\Models\Vulnerability; // Assuming path
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ProjectPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser, $liderUser, $miembroUser;
    protected Project $activeProject, $inactiveProject;

    // Assuming 'active' and 'inactive' are the status strings used in Project model
    const PROJECT_STATUS_ACTIVE = 'active';
    const PROJECT_STATUS_INACTIVE = 'inactive';

    protected function setUp(): void
    {
        parent::setUp();

        // Seed basic roles and permissions if not already done by a global seeder
        $this->artisan('db:seed', ['--class' => 'RolePermissionSeeder']); // Adjust if your seeder is different

        $this->adminUser = User::factory()->create()->assignRole('admin');
        $this->liderUser = User::factory()->create()->assignRole('lider');
        $this->miembroUser = User::factory()->create()->assignRole('miembro');

        // Ensure 'lider' has permissions for creating/viewing vulnerabilities
        // These permissions are checked first in the policy before the status check.
        Role::findByName('lider')->givePermissionTo('crear vulnerabilidades');
        Role::findByName('lider')->givePermissionTo('ver vulnerabilidades');
        Role::findByName('lider')->givePermissionTo('ver tareas'); // Added for viewing tasks link
        Role::findByName('miembro')->givePermissionTo('ver vulnerabilidades');
        Role::findByName('miembro')->givePermissionTo('ver tareas'); // Added for viewing tasks link


        $this->activeProject = Project::factory()->create(['status' => self::PROJECT_STATUS_ACTIVE, 'lider_id' => $this->liderUser->id]);
        $this->inactiveProject = Project::factory()->create(['status' => self::PROJECT_STATUS_INACTIVE, 'lider_id' => $this->liderUser->id]);

        // Assign users to active project for some tests if needed by view logic
        $this->activeProject->users()->attach($this->miembroUser);
        $this->inactiveProject->users()->attach($this->miembroUser);
    }

    /** @test */
    public function lider_cannot_create_vulnerability_for_inactive_project()
    {
        $this->actingAs($this->liderUser)
            ->postJson(route('projects.vulnerabilities.store', $this->inactiveProject), [
                'name' => 'Test Vulnerability',
                'description' => 'Details',
                // Add other required fields for vulnerability creation
            ])
            ->assertStatus(403);
    }

    /** @test */
    public function lider_can_create_vulnerability_for_active_project()
    {
        $this->actingAs($this->liderUser)
            ->postJson(route('projects.vulnerabilities.store', $this->activeProject), [
                'name' => 'Test Vulnerability Active Project',
                'description' => 'Details',
                 // Add other required fields
            ])
            ->assertStatus(201); // Or 200, or 302 if redirecting, depending on controller
    }

    /** @test */
    public function admin_can_create_vulnerability_for_inactive_project_due_to_before_method()
    {
        // Admin bypasses specific policy logic due to before() method in policies
        $this->actingAs($this->adminUser)
            ->postJson(route('projects.vulnerabilities.store', $this->inactiveProject), [
                'name' => 'Admin Test Vulnerability',
                'description' => 'Details',
            ])
            ->assertStatus(201); // Or relevant success status
    }

    /** @test */
    public function lider_can_view_vulnerabilities_of_inactive_project() // RENAMED and behaviour changed
    {
        // Policy for verVulnerabilidades no longer checks project status.
        // And assuming there's a route like projects.vulnerabilities.index

        // Create a vulnerability in the inactive project to test against
        Vulnerability::factory()->create(['project_id' => $this->inactiveProject->id]);

        $this->actingAs($this->liderUser)
            ->getJson(route('projects.vulnerabilities.index', $this->inactiveProject))
            ->assertStatus(200); // CHANGED from 403 to 200
    }

    /** @test */
    public function miembro_can_view_vulnerabilities_of_inactive_project_they_are_member_of() // RENAMED (slightly for clarity) and behaviour changed
    {
        // Policy for verVulnerabilidades no longer checks project status.
        // Miembro should be able to see vulnerabilities if they are part of the project.
        Vulnerability::factory()->create(['project_id' => $this->inactiveProject->id]);

        $this->actingAs($this->miembroUser) // User is attached to inactiveProject in setUp()
            ->getJson(route('projects.vulnerabilities.index', $this->inactiveProject))
            ->assertStatus(200); // CHANGED from 403 to 200
    }

    /** @test */
    public function lider_can_view_vulnerabilities_of_active_project()
    {
        Vulnerability::factory()->create(['project_id' => $this->activeProject->id]);

        $this->actingAs($this->liderUser)
            ->getJson(route('projects.vulnerabilities.index', $this->activeProject))
            ->assertStatus(200);
    }

    /** @test */
    public function miembro_can_view_vulnerabilities_of_active_project_they_are_member_of()
    {
        Vulnerability::factory()->create(['project_id' => $this->activeProject->id]);

        $this->actingAs($this->miembroUser)
            ->getJson(route('projects.vulnerabilities.index', $this->activeProject))
            ->assertStatus(200);
    }

    // --- Tests for UI elements on projects.vulnerabilities.index ---

    /** @test */
    public function lider_does_not_see_create_vulnerability_button_on_project_vulnerabilities_index_for_inactive_project()
    {
        // The page itself is viewable due to verVulnerabilidades policy change
        $response = $this->actingAs($this->liderUser)
            ->get(route('projects.vulnerabilities.index', $this->inactiveProject));

        $response->assertOk();
        // Check based on the Blade logic: @if(isset($viewModel->project) && $viewModel->context === 'project') @can('crearVulnerabilidades', $viewModel->project)
        // Since project is inactive, 'crearVulnerabilidades' policy will be false.
        $response->assertDontSee('Nueva Vulnerabilidad'); // Text of the button
    }

    /** @test */
    public function lider_sees_create_vulnerability_button_on_project_vulnerabilities_index_for_active_project()
    {
        $response = $this->actingAs($this->liderUser)
            ->get(route('projects.vulnerabilities.index', $this->activeProject));

        $response->assertOk();
        // For active project, 'crearVulnerabilidades' policy should be true for lider.
        $response->assertSee('Nueva Vulnerabilidad');
    }

    /** @test */
    public function lider_sees_tareas_link_for_vulnerability_in_inactive_project_on_project_vulnerabilities_index()
    {
        $vulnerabilityInInactiveProject = Vulnerability::factory()->create(['project_id' => $this->inactiveProject->id]);
        $this->liderUser->givePermissionTo('ver tareas'); // Ensure permission

        $response = $this->actingAs($this->liderUser)
            ->get(route('projects.vulnerabilities.index', $this->inactiveProject));

        $response->assertOk();
        // Assert that the link to view tasks for this specific vulnerability is present.
        // The link text is "Tareas" and it's wrapped in @can('viewTasks', $vulnerability)
        // VulnerabilityPolicy@viewTasks allows viewing tasks for inactive projects.
        $response->assertSee(route('vulnerabilities.tasks.index', $vulnerabilityInInactiveProject));
        // A more robust check might be to find the link specifically in the dropdown for that vulnerability row.
        // For now, checking if the route appears in the rendered HTML is a good indicator.
    }

    /** @test */
    public function miembro_sees_tareas_link_for_vulnerability_in_inactive_project_on_project_vulnerabilities_index_if_member()
    {
        $vulnerabilityInInactiveProject = Vulnerability::factory()->create(['project_id' => $this->inactiveProject->id]);
        // Miembro is already associated with inactiveProject in setUp()
        $this->miembroUser->givePermissionTo('ver tareas'); // Ensure permission

        $response = $this->actingAs($this->miembroUser)
            ->get(route('projects.vulnerabilities.index', $this->inactiveProject));

        $response->assertOk();
        $response->assertSee(route('vulnerabilities.tasks.index', $vulnerabilityInInactiveProject));
    }
}
