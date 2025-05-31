<?php

namespace App\Listeners;

use App\Domain\Vulnerabilities\Models\Vulnerability;
use App\Models\AuditLog;
use App\Models\User as UserModelForPivot;
use Illuminate\Database\Eloquent\Events\PivotDetached;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class HandlePivotDetached
{
    /**
     * Handle the event.
     *
     * @param  \Illuminate\Database\Eloquent\Events\PivotDetached  $event
     * @return void
     */
    public function handle(PivotDetached $event)
    {
        // Extract data from the event
        $model = $event->model;
        $relationName = $event->relationName;
        $pivotIds = $event->pivotIds;

        // Check if the event is for the correct model and relation
        if ($model instanceof Vulnerability && $relationName === 'assignedUsers') {
            /** @var Vulnerability $vulnerability */
            $vulnerability = $model;
            $vulnerability->loadMissing('project'); // Ensure the project is loaded

            $loggedInUser = Auth::user();
            $loggedInUserName = $loggedInUser ? $loggedInUser->name : 'Sistema';

            foreach ($pivotIds as $userId) {
                $detachedUser = UserModelForPivot::find($userId);
                $detachedUserName = $detachedUser ? $detachedUser->name : 'ID:'.$userId;

                AuditLog::create([
                    'user_id' => $loggedInUser ? $loggedInUser->id : null,
                    'action' => 'vulnerability_user_detached',
                    'description' => "Usuario '{$detachedUserName}' desasignado de la vulnerabilidad '{$vulnerability->title}' por {$loggedInUserName}.",
                    'auditable_id' => $vulnerability->id,
                    'auditable_type' => get_class($vulnerability),
                    'project_id' => $vulnerability->project_id,
                    'organization_id' => $vulnerability->project->organization_id ?? null,
                    'old_values' => ['user_id_detached' => $userId], // Changed to old_values as per original logic
                ]);

                // Optional: Log to system log for debugging or confirmation
                // Log::info("User {$userId} detached from Vulnerability {$vulnerability->id} by user " . (Auth::id() ?? 'System'));
            }
        }
    }
}
