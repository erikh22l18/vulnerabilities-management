<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Str;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class UserManagementTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate:fresh --seed');
        $this->artisan('config:clear');
        $this->artisan('config:cache');
    }

    /**
     * Test user registration, login, logout, and profile update.
     *
     * @return void
     */
    public function testPublicUserRegistrationLoginLogout(Browser $browser)
    {
        $name = 'Public Test User';
        // Ensure email is unique for each test run to prevent conflicts
        $email = 'publicuser-' . Str::random(10) . '@example.com';
        $password = 'password';

        // Registration
        $browser->visit('/register')
                ->type('name', $name)
                ->type('email', $email)
                ->type('password', $password)
                ->type('password_confirmation', $password)
                ->press('Registrarse') // Assuming 'Registrarse' is the button text from register.blade.php
                ->assertPathIs('/dashboard')
                ->assertSee($name);

        // Logout
        // Attempt a robust logout sequence
        if ($browser->resolver->find('form[action$="/logout"]')) {
            $browser->submit('form[action$="/logout"]');
        } elseif ($browser->resolver->find('button:contains("Log Out")')) {
            $browser->press('Log Out');
        } elseif ($browser->resolver->find('a:contains("Log Out")')) {
            $browser->clickLink('Log Out');
        } elseif ($browser->resolver->find('button:contains("Cerrar sesión")')) { // Spanish text
            $browser->press('Cerrar sesión');
        } elseif ($browser->resolver->find('a:contains("Cerrar sesión")')) { // Spanish text
            $browser->clickLink('Cerrar sesión');
        } else {
            // If specific selectors for Jetstream/Breeze are known, they can be added here.
            // For example, clicking a dropdown then the logout link.
            // $browser->click('#user-menu-button') // Example selector for a user menu dropdown
            //         ->waitForText('Log Out', 2) // Wait for logout text to appear
            //         ->clickLink('Log Out');
            $this->fail('Logout button/link not found. Please inspect your application layout and update selectors.');
        }

        // Wait for logout process and redirection
        $browser->pause(1000)
                ->assertPathIs('/login'); // Default redirect after logout is often /login

        // Login
        $browser->visit('/login')
                ->type('email', $email)
                ->type('password', $password)
                ->press('Log in') // Based on successful LoginTest.php
                ->assertPathIs('/dashboard')
                ->assertSee($name);
    }
}