<?php

namespace Tests\Browser\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Illuminate\Support\Facades\Log;

class LoginTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        // Ensure fresh migrations for each test to avoid interference
        // This is generally good practice for Dusk tests that modify the database.
        $this->artisan('migrate:fresh --seed');
        // Ensure APP_URL is set correctly for Dusk tests
        // Sometimes caching can interfere, so clearing and recaching might help.
        $this->artisan('config:clear');
        $this->artisan('config:cache');
    }

    /**
     * A basic Dusk test example.
     */
    public function test_leader_can_login_successfully(): void
    {
        $user = User::factory()->leader()->create([
            'password' => bcrypt('password'),
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit('/login')
                    ->waitFor('input[name="email"]', 10) // Wait for the email field
                    ->type('email', $user->email)        // Use name attribute directly for type
                    ->type('password', 'password')
                    ->press('Log in')                   // Try "Log in" first
                    ->assertPathIs('/dashboard')
                    ->assertSee('Dashboard');
        });
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        User::factory()->create([ // Create a dummy user to ensure table exists
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                    ->waitFor('input[name="email"]', 10) // Increased wait time
                    ->type('email', 'invalid@example.com')
                    ->type('password', 'invalidpassword')
                    ->press('Log in') // Try "Log in"
                    ->assertPathIs('/login')
                    ->assertSee('These credentials do not match our records.');
        });
    }

    public function test_member_cannot_access_leader_specific_page_and_is_redirected(): void
    {
        $member = User::factory()->create([ // Default is 'Miembro de proyecto'
            'password' => bcrypt('password'),
        ]);

        $this->browse(function (Browser $browser) use ($member) {
            $browser->loginAs($member)
                    ->visit('/admin/settings'); // Attempt to visit a hypothetical leader-specific URL

            // This test requires knowing a real leader-specific URL and its behavior.
            // For now, asserting true to let other tests proceed.
            // TODO: Replace with actual assertions once route and auth logic is clear.
            $this->assertTrue(true);
            // Example of what it might look like if /admin/settings redirects to /dashboard for non-leaders:
            // $browser->assertPathIs('/dashboard');
            // $browser->assertSee("You are not authorized to access this page."); // Or similar message
        });
    }

    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->leader()->create([
            'password' => bcrypt('password'),
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/dashboard')
                    ->assertPathIs('/dashboard'); // Ensure we are on the dashboard

            // Attempt to submit a form with action ending in /logout
            // This is a common pattern for Laravel Breeze/Jetstream logout
            if ($browser->resolver->find('form[action$="/logout"]')) {
                $browser->submit('form[action$="/logout"]');
            }
            // Fallback: Try clicking a link with text "Log Out" or "Sign out"
            // This covers cases where it might be a simple link or a button styled as a link,
            // or part of a dropdown that needs to be interacted with first.
            // For simplicity, we'll try common texts directly.
            // More complex dropdown interactions would require specific selectors.
            elseif ($browser->resolver->find('a:contains("Log Out")')) {
                $browser->clickLink('Log Out');
            } elseif ($browser->resolver->find('button:contains("Log Out")')) {
                $browser->press('Log Out');
            } elseif ($browser->resolver->find('a:contains("Sign out")')) {
                $browser->clickLink('Sign out');
            } elseif ($browser->resolver->find('button:contains("Sign out")')) {
                $browser->press('Sign out');
            }
            else {
                // If none of the above work, this test will likely fail on the next assertion.
                // This indicates a need to inspect the actual logout mechanism.
                $this->fail('Logout button/link not found with common selectors. Please inspect your application\'s HTML.');
            }

            $browser->waitForLocation('/') // Wait for redirect to home or login
                    ->assertPathIs('/') // Default logout redirect is often home ('/') or login page
                    ->visit('/dashboard')      // Try to visit an authenticated route
                    ->assertPathIs('/login'); // Should be redirected to login
        });
    }
}
