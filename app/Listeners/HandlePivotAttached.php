<?php

namespace App\Listeners;

use App\Domain\Vulnerabilities\Models\Vulnerability;
use App\Models\AuditLog;
use App\Models\User as UserModelForPivot;
use Illuminate\Database\Eloquent\Events\PivotAttached;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class HandlePivotAttached
{
    /**
     * Handle the event.
     *
     * @param  \Illuminate\Database\Eloquent\Events\PivotAttached  $event
     * @return void
     */
    public function handle(PivotAttached $event)
    {
        // Extract data from the event
        $model = $event->model;
        $relationName = $event->relationName;
        $pivotIds = $event->pivotIds;
        // $pivotIdsAttributes = $event->pivotIdsAttributes; // Not used in the original logic but available

        // Check if the event is for the correct model and relation
        if ($model instanceof Vulnerability && $relationName === 'assignedUsers') {
            /** @var Vulnerability $vulnerability */
            $vulnerability = $model;
            $vulnerability->loadMissing('project'); // Ensure the project is loaded

            $loggedInUser = Auth::user();
            $loggedInUserName = $loggedInUser ? $loggedInUser->name : 'Sistema';

            foreach ($pivotIds as $userId) {
                $assignedUser = UserModelForPivot::find($userId);
                $assignedUserName = $assignedUser ? $assignedUser->name : 'ID:'.$userId;

                AuditLog::create([
                    'user_id' => $loggedInUser ? $loggedInUser->id : null,
                    'action' => 'vulnerability_user_assigned',
                    'description' => "Usuario '{$assignedUserName}' asignado a la vulnerabilidad '{$vulnerability->title}' por {$loggedInUserName}.",
                    'auditable_id' => $vulnerability->id,
                    'auditable_type' => get_class($vulnerability),
                    'project_id' => $vulnerability->project_id,
                    'organization_id' => $vulnerability->project->organization_id ?? null,
                    'new_values' => ['user_id_assigned' => $userId],
                ]);

                // Optional: Log to system log for debugging or confirmation
                // Log::info("User {$userId} attached to Vulnerability {$vulnerability->id} by user " . (Auth::id() ?? 'System'));
            }
        }
    }
}
