<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Domain\Projects\Models\Project;
use App\Domain\Vulnerabilities\Models\Vulnerability;
use App\Domain\Tasks\Models\Task;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser, $liderUser, $miembroUser;

    // Project statuses
    const PROJECT_STATUS_ACTIVE = 'active';
    const PROJECT_STATUS_INACTIVE = 'inactive';

    // Vulnerability states - ensure this matches your Vulnerability model
    const VULN_STATE_CERRADA = 'Cerrada';
    const VULN_STATE_ABIERTA = 'Abierta'; // Or 'Detectada', 'En análisis', etc.

    protected function setUp(): void
    {
        parent::setUp();
        // Seed basic roles and permissions. Adjust if your seeder is different or you handle this globally.
        $this->artisan('db:seed', ['--class' => 'RolePermissionSeeder']);

        $this->adminUser = User::factory()->create()->assignRole('admin');
        $this->liderUser = User::factory()->create()->assignRole('lider');
        $this->miembroUser = User::factory()->create()->assignRole('miembro');

        // Ensure necessary permissions exist and are assigned for the policies to pass basic checks
        $permissionsToEnsure = ['ver tareas', 'crear tareas', /*'crear vulnerabilidades', 'editar vulnerabilidades'*/];
        foreach ($permissionsToEnsure as $permissionName) {
            Permission::findOrCreate($permissionName, 'web');
        }

        $this->liderUser->givePermissionTo(['ver tareas', 'crear tareas']);
        // Miembro gets 'ver tareas' by default. 'crear tareas' general perm is also given.
        // Specific ability to create a task via VulnerabilityPolicy@crearTareas will be key.
        $this->miembroUser->givePermissionTo(['ver tareas', 'crear tareas']);
    }

    private function setupScenario(string $projectStatusForVulnA, string $vulnStateA, ?User $assignMiembroToVulnA = null): Vulnerability
    {
        $projectA = Project::factory()->create(['status' => $projectStatusForVulnA, 'lider_id' => $this->liderUser->id]);
        if ($assignMiembroToVulnA) { // Simplified: just add miembro to project for now
            $projectA->users()->attach($this->miembroUser);
        }

        $vulnerabilityA = Vulnerability::factory()->create([
            'project_id' => $projectA->id,
            'state' => $vulnStateA
        ]);
        if ($assignMiembroToVulnA && $vulnerabilityA->project->status === self::PROJECT_STATUS_ACTIVE && $vulnerabilityA->state !== self::VULN_STATE_CERRADA) {
            // Further setup for VulnerabilityPolicy@crearTareas if 'miembro' needs assignment to vulnerability
            // For example: $vulnerabilityA->assignedUsers()->attach($this->miembroUser); // If direct assignment to vuln is a model relationship
            // Or ensure the conditions in VulnerabilityPolicy@crearTareas for miembro are met.
            // The current VulnerabilityPolicy@crearTareas for miembro is:
            // return $vulnerability->assigned_user_id == $user->id;
            // So, we'd need to set $vulnerabilityA->assigned_user_id = $this->miembroUser->id;
            // This detail is important for Test 5.
        }

        // Another project/vulnerability to ensure filtering works
        $inactiveProject = Project::factory()->create(['status' => self::PROJECT_STATUS_INACTIVE, 'lider_id' => $this->liderUser->id]);
        Vulnerability::factory()->create(['project_id' => $inactiveProject->id, 'state' => self::VULN_STATE_ABIERTA]);

        $activeProjectWithClosedVuln = Project::factory()->create(['status' => self::PROJECT_STATUS_ACTIVE, 'lider_id' => $this->liderUser->id]);
        Vulnerability::factory()->create(['project_id' => $activeProjectWithClosedVuln->id, 'state' => self::VULN_STATE_CERRADA]);

        return $vulnerabilityA; // Return the main vulnerability for specific assertions if needed
    }

    /** @test */
    public function lider_sees_create_task_button_if_active_project_has_open_vulnerability()
    {
        $this->setupScenario(self::PROJECT_STATUS_ACTIVE, self::VULN_STATE_ABIERTA);

        $response = $this->actingAs($this->liderUser)->get(route('tasks.index'));

        $response->assertOk();
        $response->assertViewHas('show_create_task_button', true);
        // Optional: ->assertSee('Crear Tarea'); // Check for button text if using @if in blade
    }

    /** @test */
    public function lider_does_not_see_create_task_button_if_only_closed_vulnerabilities_exist()
    {
        $this->setupScenario(self::PROJECT_STATUS_ACTIVE, self::VULN_STATE_CERRADA); // Main vuln is closed

        // Ensure no other open vulnerabilities on active projects for lider
        Project::query()->update(['status' => self::PROJECT_STATUS_INACTIVE]); // Make all projects inactive initially
        $activeProject = Project::factory()->create(['status' => self::PROJECT_STATUS_ACTIVE, 'lider_id' => $this->liderUser->id]);
        Vulnerability::factory()->create(['project_id' => $activeProject->id, 'state' => self::VULN_STATE_CERRADA]);


        $response = $this->actingAs($this->liderUser)->get(route('tasks.index'));

        $response->assertOk();
        $response->assertViewHas('show_create_task_button', false);
    }

    /** @test */
    public function lider_does_not_see_create_task_button_if_only_vulnerabilities_in_inactive_projects_exist()
    {
        $this->setupScenario(self::PROJECT_STATUS_INACTIVE, self::VULN_STATE_ABIERTA); // Main vuln in inactive project

        // Ensure all other potentially accessible vulnerabilities are also in inactive projects or closed
        Vulnerability::where('state', self::VULN_STATE_ABIERTA)->update(['state' => self::VULN_STATE_CERRADA]); // Close all open
        Project::factory()->create(['status' => self::PROJECT_STATUS_INACTIVE, 'lider_id' => $this->liderUser->id])
                 ->vulnerabilities()->create(Vulnerability::factory()->raw(['state' => self::VULN_STATE_ABIERTA]));


        $response = $this->actingAs($this->liderUser)->get(route('tasks.index'));

        $response->assertOk();
        $response->assertViewHas('show_create_task_button', false);
    }

    /** @test */
    public function miembro_sees_create_task_button_if_their_active_project_has_open_vulnerability()
    {
        $vulnerability = $this->setupScenario(self::PROJECT_STATUS_ACTIVE, self::VULN_STATE_ABIERTA, $this->miembroUser);
        // Ensure 'miembro' can create tasks for this specific vulnerability based on VulnerabilityPolicy@crearTareas
        // This policy is: $vulnerability->assigned_user_id == $user->id;
        $vulnerability->assigned_user_id = $this->miembroUser->id;
        $vulnerability->save();


        $response = $this->actingAs($this->miembroUser)->get(route('tasks.index'));

        $response->assertOk();
        $response->assertViewHas('show_create_task_button', true);
    }

    /** @test */
    public function miembro_does_not_see_create_task_button_if_their_projects_only_have_closed_vulnerabilities()
    {
        // Setup: Miembro is part of an active project, but it only has a closed vulnerability.
        $activeProjectForMiembro = Project::factory()->create(['status' => self::PROJECT_STATUS_ACTIVE]);
        $activeProjectForMiembro->users()->attach($this->miembroUser);
        Vulnerability::factory()->create([
            'project_id' => $activeProjectForMiembro->id,
            'state' => self::VULN_STATE_CERRADA,
            'assigned_user_id' => $this->miembroUser->id // still assign, but it's closed
        ]);

        // Ensure no other projects give miembro access to open vulnerabilities
        Project::where('id', '!=', $activeProjectForMiembro->id)->get()->each(function($p) {
            $p->users()->detach($this->miembroUser);
        });


        $response = $this->actingAs($this->miembroUser)->get(route('tasks.index'));

        $response->assertOk();
        $response->assertViewHas('show_create_task_button', false);
    }

    /** @test */
    public function miembro_does_not_see_create_task_button_if_their_projects_are_inactive()
    {
        // Setup: Miembro is part of an inactive project with an open vulnerability.
        $inactiveProjectForMiembro = Project::factory()->create(['status' => self::PROJECT_STATUS_INACTIVE]);
        $inactiveProjectForMiembro->users()->attach($this->miembroUser);
        Vulnerability::factory()->create([
            'project_id' => $inactiveProjectForMiembro->id,
            'state' => self::VULN_STATE_ABIERTA,
            'assigned_user_id' => $this->miembroUser->id
        ]);

        // Ensure no other projects give miembro access to open vulnerabilities on active projects
         Project::where('status', self::PROJECT_STATUS_ACTIVE)->get()->each(function($p) {
            $p->users()->detach($this->miembroUser);
        });

        $response = $this->actingAs($this->miembroUser)->get(route('tasks.index'));

        $response->assertOk();
        $response->assertViewHas('show_create_task_button', false);
    }

    /** @test */
    public function miembro_does_not_see_button_if_vulnerabilitypolicy_denies_creartareas()
    {
        // Setup: Miembro is part of active project with open Vulnerability A.
        // BUT VulnerabilityPolicy@crearTareas for 'miembro' is: return $vulnerability->assigned_user_id == $user->id;
        // So, if miembro is NOT assigned to the vulnerability, policy denies.
        $vulnerability = $this->setupScenario(self::PROJECT_STATUS_ACTIVE, self::VULN_STATE_ABIERTA, $this->miembroUser);
        $vulnerability->assigned_user_id = $this->liderUser->id; // Assign to someone else
        $vulnerability->save();

        $response = $this->actingAs($this->miembroUser)->get(route('tasks.index'));

        $response->assertOk();
        $response->assertViewHas('show_create_task_button', false);
    }
}
