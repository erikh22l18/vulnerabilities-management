<?php

namespace App\Domain\Dashboard\Services;

use App\Models\User; // Assuming User model is in App\Models
use App\Domain\Tasks\Models\Task; // Assuming Task model path
use Carbon\Carbon; // Added Carbon for date calculations
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // May be needed for complex queries

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
            // New KPIs for Miembro role
            'individual_productivity_last_month' => $this->calculateIndividualProductivityLastMonth($user),
            'resolution_time_compliance_percentage' => $this->calculateResolutionTimeCompliance($user),
            'assigned_vs_closed_ratio' => $this->calculateAssignedVsClosedRatio($user),
            // Add more miembro-specific data points here later
        ];

        // Sample personal alerts
        $data['personal_alerts'] = [
            ['message' => 'Tarea TASK-010 asignada a usted vence mañana.', 'created_at' => now()->subHours(3)],
            ['message' => 'Nueva vulnerabilidad VULN-078 asignada a usted.', 'created_at' => now()->subMinutes(15)],
        ];

        return $data;
    }

    private function calculateIndividualProductivityLastMonth(User $user): int
    {
        // This KPI counts tasks assigned to the user and resolved/closed in the last 30 days.
        // It could be expanded to include vulnerabilities if members are directly assigned vulnerabilities
        // and a similar 'assigned_to' and 'resolved_at' field exists on the Vulnerability model.
        $thirtyDaysAgo = Carbon::now()->subDays(30);

        return Task::where('assigned_to', $user->id)
            ->whereIn('status', ['Completada', 'Cerrada']) // Assuming these are terminal statuses
            ->whereNotNull('completed_at') // Ensure completed_at is not null
            ->where('completed_at', '>=', $thirtyDaysAgo)
            ->count();
    }

    private function calculateResolutionTimeCompliance(User $user): float
    {
        // This KPI calculates the percentage of resolved/closed tasks assigned to the user
        // that were completed on or before their due_date.
        // It could be expanded to include vulnerabilities if relevant fields exist.

        $resolvedTasksWithDueDate = Task::where('assigned_to', $user->id)
            ->whereIn('status', ['Completada', 'Cerrada'])
            ->whereNotNull('completed_at')
            ->whereNotNull('due_date')
            ->get();

        if ($resolvedTasksWithDueDate->isEmpty()) {
            return 0.0; // No tasks to measure compliance against
        }

        $resolvedOnTimeCount = 0;
        foreach ($resolvedTasksWithDueDate as $task) {
            $completedAtDate = Carbon::parse($task->completed_at)->startOfDay();
            $dueDate = Carbon::parse($task->due_date)->startOfDay();
            if ($completedAtDate->lte($dueDate)) {
                $resolvedOnTimeCount++;
            }
        }

        return round(($resolvedOnTimeCount / $resolvedTasksWithDueDate->count()) * 100, 2);
    }

    private function calculateAssignedVsClosedRatio(User $user): float
    {
        // This KPI calculates the lifetime ratio of resolved/closed tasks to total assigned tasks for the user.
        // It could be expanded to include vulnerabilities if relevant fields exist.

        $totalAssignedTasks = Task::where('assigned_to', $user->id)
            ->distinct() // Ensure we count unique tasks if a task could be assigned multiple times (though unlikely with this schema)
            ->count();

        if ($totalAssignedTasks === 0) {
            return 0.0; // No tasks ever assigned
        }

        $totalClosedTasksByUser = Task::where('assigned_to', $user->id)
            ->whereIn('status', ['Completada', 'Cerrada'])
            ->distinct()
            ->count();

        return round($totalClosedTasksByUser / $totalAssignedTasks, 2); // Ratio, e.g., 0.75 for 75%
    }
}
