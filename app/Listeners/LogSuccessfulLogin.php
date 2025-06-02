<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Authenticated;
use Illuminate\Support\Facades\Auth;

class LogSuccessfulLogin
{
    /**
     * Handle the event.
     *
     * @param  \Illuminate\Auth\Events\Authenticated  $event
     * @return void
     */
    public function handle(Authenticated $event): void
    {
        if (Auth::check()) {
            // Attempted to update 'last_login_at' here, but the column was not found on the 'users' table.
            // This line has been removed to prevent errors.
            // If tracking the last login time on the User model is still required,
            // please ensure the 'last_login_at' timestamp column exists on the 'users' table
            // (e.g., by adding it via a new database migration).
        }
    }
}
