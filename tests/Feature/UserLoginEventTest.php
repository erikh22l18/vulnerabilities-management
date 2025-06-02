<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Authenticated;

class UserLoginEventTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the last_login_at timestamp is updated when a user logs in.
     *
     * @return void
     */
    public function test_last_login_at_is_updated_on_login(): void
    {
        // Create a user
        $user = User::factory()->create();

        // Assert that last_login_at is initially null
        $this->assertNull($user->last_login_at);

        // Simulate login by firing the Authenticated event
        Event::dispatch(new Authenticated('web', $user));

        // Refresh the user model from the database
        $user->refresh();

        // Assert that last_login_at is now set
        $this->assertNotNull($user->last_login_at);

        // Assert that the timestamp is recent (within 5 seconds of now)
        $this->assertTrue($user->last_login_at->diffInSeconds(now()) < 5);
    }
}
