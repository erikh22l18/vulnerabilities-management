<?php

namespace App\Domain\Dashboard\Services;

use App\Models\User; // Assuming User model is in App\Models
use Illuminate\Http\Request;

class LiderDashboardService
{
    public function getData(User $user, Request $request = null): array
    {
        // Initial basic metrics for Lider
        // Assumes User model has a 'projects' relationship (e.g., projects they are a leader of)
        // Or, if 'lider' means they can see all projects, this logic might change.
        // For now, let's assume it's projects they are directly associated with as 'lider_id'
        // or through a pivot table if User->projects() is many-to-many.

        $projectsLedCount = 0;
        if (method_exists($user, 'projectsLed')) { // Example: if there's a specific relationship for projects led
            $projectsLedCount = $user->projectsLed()->count();
        } elseif (method_exists($user, 'projects')) { // Fallback to a general projects relationship
             // This might need refinement based on how 'lider' association is defined.
             // If 'lider' means they are the 'lider_id' on projects table:
             $projectsLedCount = \App\Domain\Projects\Models\Project::where('lider_id', $user->id)->count();
        }

        $data = [
            'projects_led_count' => $projectsLedCount,
            // Add more lider-specific data points here later
        ];

        // Sample lider alerts
        $data['lider_alerts'] = [
            ['message' => 'Proyecto Beta: Tarea TASK-005 vencida.', 'created_at' => now()->subHours(2)],
            ['message' => 'Proyecto Gamma: Nueva vulnerabilidad de alta severidad detectada.', 'created_at' => now()->subHours(1)],
        ];

        return $data;
    }
}
