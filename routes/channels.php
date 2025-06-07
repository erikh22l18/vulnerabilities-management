<?php

use App\Models\ImportBatch;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('import-batch.{batchId}', function (User $user, string $batchId) {
    $batch = ImportBatch::find($batchId);
    // Ensure the user is either the one who initiated the batch or an admin.
    // This assumes the User model has a hasRole() method (e.g., from Spatie/laravel-permission).
    // And ImportBatch has a user_id linking to the user who started it.
    return ($batch && $batch->user_id === $user->id) || $user->hasRole('admin');
});
