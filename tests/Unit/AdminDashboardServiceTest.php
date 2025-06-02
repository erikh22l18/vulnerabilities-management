<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Services\AdminDashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

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
}
