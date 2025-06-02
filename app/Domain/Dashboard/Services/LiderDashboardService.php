<?php

namespace App\Domain\Dashboard\Services;

use App\Models\User; // Assuming User model is in App\Models
use App\Domain\Vulnerabilities\Models\Vulnerability; // Added Vulnerability model
use Carbon\Carbon; // Added Carbon for date manipulations
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // May be needed for complex queries

class LiderDashboardService
{
    public function getData(User $user, Request $request = null): array
    {
        $projectIds = [];
        if (method_exists($user, 'projectsLed')) {
            $projectIds = $user->projectsLed()->pluck('id')->toArray();
        } elseif (method_exists($user, 'projects')) {
            $projectIds = $user->projects()->pluck('id')->toArray();
        }

        $projectsLedCount = count($projectIds);

        $data = [
            'projects_led_count' => $projectsLedCount,
            // KPIs to be implemented
            'monthly_closure_rate' => $this->calculateMonthlyClosureRate($projectIds),
            'open_treatment_gaps_count' => $this->calculateOpenTreatmentGaps($projectIds),
            'accumulated_backlog_count' => $this->calculateAccumulatedBacklog($projectIds),
            'critical_vulnerabilities_in_projects_count' => $this->calculateCriticalVulnerabilitiesInProjects($projectIds),
            // New compliance metrics for Lider's projects
            'critical_high_remediation_rate_projects' => $this->calculateCriticalHighRemediationRateProjects($projectIds),
            'on_time_remediation_percentage_projects' => $this->calculateOnTimeRemediationPercentageProjects($projectIds),
        ];

        // Sample lider alerts
        $data['lider_alerts'] = [
            ['message' => 'Proyecto Beta: Tarea TASK-005 vencida.', 'created_at' => now()->subHours(2)],
            ['message' => 'Proyecto Gamma: Nueva vulnerabilidad de alta severidad detectada.', 'created_at' => now()->subHours(1)],
        ];

        return $data;
    }

    private function calculateMonthlyClosureRate(array $projectIds): float
    {
        if (empty($projectIds)) {
            return 0.0;
        }

        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $closedThisMonth = Vulnerability::whereIn('project_id', $projectIds)
            ->whereIn('state', ['Resuelta', 'Cerrada'])
            ->whereNotNull('resolved_at') // Ensure resolved_at is not null
            ->whereBetween('resolved_at', [$startOfMonth, $endOfMonth])
            ->count();

        // Total open vulnerabilities at month-end for projects associated with the leader
        // This counts vulnerabilities that are currently 'open' or 'in-progress'.
        $openAtMonthEnd = Vulnerability::whereIn('project_id', $projectIds)
            ->whereIn('state', ['Detectada', 'En tratamiento'])
            ->count();

        // The denominator: open vulnerabilities at month-end + those closed this month
        $denominator = $openAtMonthEnd + $closedThisMonth;

        if ($denominator === 0) {
            return 0.0; // Handle division by zero
        }

        return round($closedThisMonth / $denominator, 2); // Return as a percentage or ratio
    }

    private function calculateOpenTreatmentGaps(array $projectIds): int
    {
        if (empty($projectIds)) {
            return 0;
        }

        $now = Carbon::now();

        // Condition 1: Overdue vulnerabilities not resolved/closed
        $overdueQuery = Vulnerability::whereIn('project_id', $projectIds)
            ->where('resolution_deadline', '<', $now)
            ->whereNotIn('status', ['Resuelta', 'Cerrada']);

        // Condition 2: Open/In-progress vulnerabilities with no user assigned
        $unassignedQuery = Vulnerability::whereIn('project_id', $projectIds)
            ->whereIn('state', ['Detectada', 'En tratamiento'])
            ->whereNull('assigned_to');

        // To avoid double counting, we can get IDs from the first query
        // and use `orWhere` with a condition that the ID is not in the first set for the second part.
        // However, a simpler approach for just counting distinct vulnerabilities matching either condition:
        // Fetch IDs for both conditions and count the unique IDs.

        $overdueIds = $overdueQuery->pluck('id');
        $unassignedIds = $unassignedQuery->pluck('id');

        // Combine and count unique IDs
        // This ensures a vulnerability matching both conditions is counted only once.
        return $overdueIds->merge($unassignedIds)->unique()->count();
    }

    private function calculateAccumulatedBacklog(array $projectIds): int
    {
        if (empty($projectIds)) {
            return 0;
        }

        return Vulnerability::whereIn('project_id', $projectIds)
            ->whereNotIn('status', ['Resuelta', 'Cerrada'])
            ->count();
    }

    private function calculateCriticalVulnerabilitiesInProjects(array $projectIds): int
    {
        if (empty($projectIds)) {
            return 0;
        }

        return Vulnerability::whereIn('project_id', $projectIds)
            ->whereIn('severity_level', ['Alta', 'Crítica'])
            ->whereNotIn('status', ['Resuelta', 'Cerrada'])
            ->count();
    }

    private function calculateCriticalHighRemediationRateProjects(array $projectIds): float
    {
        if (empty($projectIds)) {
            return 0.0;
        }

        $resolvedCriticalHighCount = Vulnerability::whereIn('project_id', $projectIds)
            ->whereIn('severity_level', ['Alta', 'Crítica'])
            ->whereIn('state', ['Resuelta', 'Cerrada'])
            ->count();

        $totalCriticalHighCount = Vulnerability::whereIn('project_id', $projectIds)
            ->whereIn('severity_level', ['Alta', 'Crítica'])
            ->count();

        if ($totalCriticalHighCount === 0) {
            return 0.0; // Or 100.0 if no critical/high issues means 100% compliance by some definitions
        }

        return round(($resolvedCriticalHighCount / $totalCriticalHighCount) * 100, 2);
    }

    private function calculateOnTimeRemediationPercentageProjects(array $projectIds): float
    {
        if (empty($projectIds)) {
            return 0.0;
        }

        $onTimeResolvedCount = Vulnerability::whereIn('project_id', $projectIds)
            ->whereIn('state', ['Resuelta', 'Cerrada'])
            ->whereNotNull('resolution_deadline')
            ->whereNotNull('resolved_at')
            ->whereRaw('DATE(resolved_at) <= DATE(resolution_deadline)')
            ->count();

        $totalDeadlineResolvedCount = Vulnerability::whereIn('project_id', $projectIds)
            ->whereIn('state', ['Resuelta', 'Cerrada'])
            ->whereNotNull('resolution_deadline')
            ->count();

        if ($totalDeadlineResolvedCount === 0) {
            return 0.0;
        }

        return round(($onTimeResolvedCount / $totalDeadlineResolvedCount) * 100, 2);
    }
}
