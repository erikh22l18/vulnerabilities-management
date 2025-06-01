<?php

namespace App\Domain\Dashboard\Services;

use App\Models\User; // Assuming User model is in App\Models
use App\Domain\Tasks\Models\Task; // Assuming Task model path
use Illuminate\Http\Request;

class MiembroDashboardService
{
    public function getData(User $user, Request $request = null): array
    {
        // Initial basic metrics for Miembro
        $assignedTasksCount = Task::where('assigned_to', $user->id)
                                  ->whereNotIn('status', ['Completada', 'Cerrada']) // Example: count only active tasks
                                  ->count();

        $data = [
            'my_active_assigned_tasks_count' => $assignedTasksCount,
            // Add more miembro-specific data points here later
        ];

        // Sample personal alerts
        $data['personal_alerts'] = [
            ['message' => 'Tarea TASK-010 asignada a usted vence mañana.', 'created_at' => now()->subHours(3)],
            ['message' => 'Nueva vulnerabilidad VULN-078 asignada a usted.', 'created_at' => now()->subMinutes(15)],
        ];

        return $data;
    }
}
