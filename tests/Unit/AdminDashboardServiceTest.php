<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Services\AdminDashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;
use App\Domain\Vulnerabilities\Models\Vulnerability;
use App\Domain\Projects\Models\Project; // Required for factory

class AdminDashboardServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the inactive_users_count method of AdminDashboardService.
     *
     * @return void
     */
    public function test_inactive_users_count(): void
    {
        // Create a user who logged in less than 30 days ago
        User::factory()->create(['last_login_at' => Carbon::now()->subDays(10)]);

        // Create a user who logged in more than 30 days ago
        User::factory()->create(['last_login_at' => Carbon::now()->subDays(40)]);

        // Create a user who has never logged in
        User::factory()->create(['last_login_at' => null]);

        // Create another user who also logged in more than 30 days ago
        User::factory()->create(['last_login_at' => Carbon::now()->subDays(50)]);

        // Instantiate the AdminDashboardService
        $service = new AdminDashboardService();

        // Call the getData() method
        $data = $service->getData();

        // Assert that the inactive_users_count in the result is 2
        // (Users who logged in more than 30 days ago OR never logged in)
        $this->assertEquals(2, $data['inactive_users_count']);
    }

    /**
     * Test the on_time_remediation_percentage calculation of AdminDashboardService.
     *
     * @return void
     */
    public function test_on_time_remediation_percentage(): void
    {
        // Common setup for vulnerabilities
        $user = User::factory()->create();
        $project = Project::factory()->create(['owner_id' => $user->id]);

        // 1. Vulnerability resolved ON TIME
        $vuln1 = Vulnerability::factory()->create([
            'project_id' => $project->id,
            'created_by' => $user->id,
            'state' => Vulnerability::STATE_DETECTADA,
            'resolution_deadline' => Carbon::now()->addDays(5),
            'resolved_at' => null,
        ]);
        $vuln1->update(['state' => Vulnerability::STATE_RESUELTA]); // Observer sets resolved_at to now()
        // For this test, we need to ensure it's considered on time relative to the deadline
        // If observer sets it to now(), and deadline is now()->addDays(5), it's on time.

        // 2. Vulnerability resolved LATE
        $vuln2 = Vulnerability::factory()->create([
            'project_id' => $project->id,
            'created_by' => $user->id,
            'state' => Vulnerability::STATE_DETECTADA,
            'resolution_deadline' => Carbon::now()->addDays(1),
            'resolved_at' => null,
        ]);
        $vuln2->update(['state' => Vulnerability::STATE_RESUELTA]); // Observer sets resolved_at to now()
        // Manually update resolved_at to be after the deadline for testing "late" scenario
        Vulnerability::withoutEvents(function () use ($vuln2) {
            $vuln2->update(['resolved_at' => Carbon::now()->addDays(2)]);
        });


        // 3. Vulnerability resolved, NO DEADLINE
        $vuln3 = Vulnerability::factory()->create([
            'project_id' => $project->id,
            'created_by' => $user->id,
            'state' => Vulnerability::STATE_DETECTADA,
            'resolution_deadline' => null,
            'resolved_at' => null,
        ]);
        $vuln3->update(['state' => Vulnerability::STATE_RESUELTA]); // Observer sets resolved_at

        // 4. Vulnerability NOT RESOLVED, has deadline
        Vulnerability::factory()->create([
            'project_id' => $project->id,
            'created_by' => $user->id,
            'state' => Vulnerability::STATE_EN_TRATAMIENTO,
            'resolution_deadline' => Carbon::now()->addDays(1),
        ]);

        // 5. Vulnerability resolved EXACTLY ON DEADLINE
        $vuln5 = Vulnerability::factory()->create([
            'project_id' => $project->id,
            'created_by' => $user->id,
            'state' => Vulnerability::STATE_DETECTADA,
            'resolution_deadline' => Carbon::now()->addDays(3),
            'resolved_at' => null,
        ]);
        // Simulate it being resolved exactly on deadline.
        // Observer will set resolved_at to now(). For exact match, we might need to control time or set manually.
        // For this test, let's assume observer sets it to now(), and we adjust deadline to match now() for "exactly on time"
        Vulnerability::withoutEvents(function () use ($vuln5) {
            $exactDeadline = Carbon::now(); // Simulate resolution time
            $vuln5->update([
                'state' => Vulnerability::STATE_RESUELTA,
                'resolution_deadline' => $exactDeadline,
                'resolved_at' => $exactDeadline
            ]);
        });


        $service = new AdminDashboardService();
        $data = $service->getData();

        // $onTimeResolvedCount should be 2 (vuln1, vuln5)
        // $totalDeadlineResolvedCount should be 3 (vuln1, vuln2, vuln5 - those resolved with a deadline)
        // vuln3 is resolved but has no deadline, so not in $totalDeadlineResolvedCount.
        // vuln2 is resolved with deadline, but late.

        // Expected: (2 on time / 3 total with deadline) * 100
        $expectedPercentage = (2 / 3) * 100;

        $this->assertEquals($expectedPercentage, $data['on_time_remediation_percentage']);
    }
}
