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

class TaskPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser, $liderUser, $miembroUser;

    protected Project $activeProject, $inactiveProject;
    protected Vulnerability $openVulnOnActiveProject, $closedVulnOnActiveProject, $openVulnOnInactiveProject;
    protected Task $taskForOpenVulnOnActiveProject, $taskForClosedVulnOnActiveProject, $taskForOpenVulnOnInactiveProject, $taskForActiveProject, $taskForInactiveProject;

    // Project statuses
    const PROJECT_STATUS_ACTIVE = 'active';
    const PROJECT_STATUS_INACTIVE = 'inactive';

    // Vulnerability states
    const VULN_STATE_CERRADA = 'Cerrada'; // Ensure this matches your Vulnerability model
    const VULN_STATE_ABIERTA = 'Abierta';

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'RolePermissionSeeder']); // Adjust if necessary

        $this->adminUser = User::factory()->create()->assignRole('admin');
        $this->liderUser = User::factory()->create()->assignRole('lider');
        $this->miembroUser = User::factory()->create()->assignRole('miembro');

        // Base permissions for Lider/Miembro for tasks
        // The TaskPolicy's 'create' method checks for 'crear tareas'
        // Update/delete in TaskPolicy by default check roles or created_by/assigned_to,
        // so we ensure Lider/Miembro have roles. Add specific task permissions if your policy uses them.
        Permission::findOrCreate('crear tareas', 'web');
        $this->liderUser->givePermissionTo('crear tareas');
        $this->miembroUser->givePermissionTo('crear tareas');
        // For update/delete, let's assume for now Lider can always, Miembro if created/assigned
        // (these are the base rules before our status checks)

        // Setup Projects
        $this->activeProject = Project::factory()->create(['status' => self::PROJECT_STATUS_ACTIVE, 'lider_id' => $this->liderUser->id]);
        $this->inactiveProject = Project::factory()->create(['status' => self::PROJECT_STATUS_INACTIVE, 'lider_id' => $this->liderUser->id]);

        // Setup Vulnerabilities
        $this->openVulnOnActiveProject = Vulnerability::factory()->create(['project_id' => $this->activeProject->id, 'state' => self::VULN_STATE_ABIERTA]);
        $this->closedVulnOnActiveProject = Vulnerability::factory()->create(['project_id' => $this->activeProject->id, 'state' => self::VULN_STATE_CERRADA]);
        $this->openVulnOnInactiveProject = Vulnerability::factory()->create(['project_id' => $this->inactiveProject->id, 'state' => self::VULN_STATE_ABIERTA]);

        // Setup Tasks
        $this->taskForOpenVulnOnActiveProject = Task::factory()->create(['vulnerability_id' => $this->openVulnOnActiveProject->id, 'project_id' => null, 'created_by' => $this->liderUser->id]);
        $this->taskForClosedVulnOnActiveProject = Task::factory()->create(['vulnerability_id' => $this->closedVulnOnActiveProject->id, 'project_id' => null, 'created_by' => $this->liderUser->id]);
        $this->taskForOpenVulnOnInactiveProject = Task::factory()->create(['vulnerability_id' => $this->openVulnOnInactiveProject->id, 'project_id' => null, 'created_by' => $this->liderUser->id]);
        $this->taskForActiveProject = Task::factory()->create(['project_id' => $this->activeProject->id, 'vulnerability_id' => null, 'created_by' => $this->liderUser->id]);
        $this->taskForInactiveProject = Task::factory()->create(['project_id' => $this->inactiveProject->id, 'vulnerability_id' => null, 'created_by' => $this->liderUser->id]);
    }

    // --- Task Create Tests ---
    // Context: Vulnerability
    /** @test */
    public function user_cannot_create_task_for_vulnerability_on_inactive_project()
    {
        $this->actingAs($this->liderUser)
            // The policy's 'create' method takes $context as the second param.
            // Controllers usually pass [Task::class, $vulnerability] or [Task::class, $project]
            // to $this->authorize('create', ...).
            // For direct policy test, one might mock Gate or test via HTTP request.
            // Assuming HTTP request to a controller that uses this policy:
            ->postJson(route('vulnerabilities.tasks.store', $this->openVulnOnInactiveProject), ['title' => 'New Task'])
            ->assertStatus(403);
    }

    /** @test */
    public function user_cannot_create_task_for_closed_vulnerability_on_active_project()
    {
        $this->actingAs($this->liderUser)
            ->postJson(route('vulnerabilities.tasks.store', $this->closedVulnOnActiveProject), ['title' => 'New Task'])
            ->assertStatus(403);
    }

    /** @test */
    public function user_can_create_task_for_open_vulnerability_on_active_project()
    {
        $this->actingAs($this->liderUser)
            ->postJson(route('vulnerabilities.tasks.store', $this->openVulnOnActiveProject), ['title' => 'New Task'])
            ->assertStatus(201); // Or relevant success code
    }

    // Context: Project
    /** @test */
    public function user_cannot_create_task_directly_for_inactive_project()
    {
        $this->actingAs($this->liderUser)
            // Assuming a route like 'projects.tasks.store' for tasks directly under a project
            ->postJson(route('projects.tasks.store', $this->inactiveProject), ['title' => 'New Task'])
            ->assertStatus(403);
    }

    /** @test */
    public function user_can_create_task_directly_for_active_project()
    {
        $this->actingAs($this->liderUser)
            ->postJson(route('projects.tasks.store', $this->activeProject), ['title' => 'New Task'])
            ->assertStatus(201);
    }

    // Context: Null (General Task Creation, not tied to specific Project/Vulnerability at policy check time)
    /** @test */
    public function user_can_initiate_creating_general_task_if_allowed_by_role()
    {
        // This tests `$this->authorize('create', Task::class);`
        // The policy as written allows this if 'crear tareas' permission and role (lider/miembro) match.
        // No project/vulnerability status checks apply if no context is given.
        $this->actingAs($this->liderUser)
             ->postJson(route('tasks.store'), ['title' => 'General Task']) // Assuming a general tasks.store route
             ->assertStatus(201); // Or 422 if project_id/vulnerability_id is required by validation
                                  // but policy itself should pass for authorization.
    }


    // --- Task Update Tests ---
    /** @test */
    public function user_cannot_update_task_for_vulnerability_on_inactive_project()
    {
        $this->actingAs($this->liderUser)
            ->putJson(route('tasks.update', $this->taskForOpenVulnOnInactiveProject), ['title' => 'Updated Title'])
            ->assertStatus(403);
    }

    /** @test */
    public function user_cannot_update_task_for_closed_vulnerability()
    {
        $this->actingAs($this->liderUser)
            ->putJson(route('tasks.update', $this->taskForClosedVulnOnActiveProject), ['title' => 'Updated Title'])
            ->assertStatus(403);
    }

    /** @test */
    public function user_cannot_update_task_directly_for_inactive_project()
    {
        $this->actingAs($this->liderUser)
            ->putJson(route('tasks.update', $this->taskForInactiveProject), ['title' => 'Updated Title'])
            ->assertStatus(403);
    }

    /** @test */
    public function user_can_update_task_for_open_vulnerability_on_active_project()
    {
        $this->actingAs($this->liderUser)
            ->putJson(route('tasks.update', $this->taskForOpenVulnOnActiveProject), ['title' => 'Updated Title'])
            ->assertStatus(200); // Or relevant success code
    }

    /** @test */
    public function user_can_update_task_directly_for_active_project()
    {
        $this->actingAs($this->liderUser)
            ->putJson(route('tasks.update', $this->taskForActiveProject), ['title' => 'Updated Title'])
            ->assertStatus(200);
    }

    // --- Task Delete Tests (similar logic to update) ---
    /** @test */
    public function user_cannot_delete_task_for_vulnerability_on_inactive_project()
    {
        $this->actingAs($this->liderUser)
            ->deleteJson(route('tasks.destroy', $this->taskForOpenVulnOnInactiveProject))
            ->assertStatus(403);
    }

    /** @test */
    public function user_cannot_delete_task_for_closed_vulnerability()
    {
        $this->actingAs($this->liderUser)
            ->deleteJson(route('tasks.destroy', $this->taskForClosedVulnOnActiveProject))
            ->assertStatus(403);
    }

    /** @test */
    public function user_cannot_delete_task_directly_for_inactive_project()
    {
        $this->actingAs($this->liderUser)
            ->deleteJson(route('tasks.destroy', $this->taskForInactiveProject))
            ->assertStatus(403);
    }

    /** @test */
    public function user_can_delete_task_for_open_vulnerability_on_active_project()
    {
        $this->actingAs($this->liderUser)
            ->deleteJson(route('tasks.destroy', $this->taskForOpenVulnOnActiveProject))
            ->assertStatus(200); // Or 204
    }

    /** @test */
    public function user_can_delete_task_directly_for_active_project()
    {
        $this->actingAs($this->liderUser)
            ->deleteJson(route('tasks.destroy', $this->taskForActiveProject))
            ->assertStatus(200); // Or 204
    }

    // Admin bypass tests would also be relevant here.
}
