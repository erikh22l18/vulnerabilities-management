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
            Auth::user()->update(['last_login_at' => now()]);
        }
    }
}
