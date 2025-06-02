<?php

namespace App\Domain\Dashboard\Services;

use App\Domain\Projects\Models\Project;
use App\Models\User; // Assuming User model is in App\Models
use App\Domain\Vulnerabilities\Models\Vulnerability; // Assuming Vulnerability model path
use App\Domain\Organizations\Models\Organization; // Assuming Organization model path
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Required for advanced queries if needed
use Carbon\Carbon; // For date calculations

class AdminDashboardService
{
    public function getData(Request $request = null): array
    {
        // Initial basic metrics for Admin
        $data = [
            'total_projects' => Project::count(),
            'total_users' => User::count(),
            // Add more admin-specific data points here later
            'critical_open_vulnerabilities_count' => Vulnerability::whereIn('severity', ['high', 'critical'])
                ->whereIn('status', ['open', 'in-progress'])
                ->count(),
            'overdue_vulnerabilities_count' => Vulnerability::where('due_date', '<', now())
                ->whereNotIn('status', ['resolved', 'closed'])
                ->count(),
            'sla_compliance_percentage' => -1, // Placeholder: Specific SLA logic needed
            // Assuming User model has last_login_at. If not, this will throw an error.
            // Add a check or use a different approach if last_login_at is not guaranteed.
            'inactive_users_count' => User::where('last_login_at', '<', now()->subDays(30))->count(),
            // 'avg_resolution_time_per_org' will be fetched via a separate API call
        ];

        // Sample global alerts
        $data['global_alerts'] = [
            ['message' => 'Sistema: Actualización de seguridad programada para medianoche.', 'created_at' => now()->subHours(1)],
            ['message' => 'Proyecto Alpha: Vulnerabilidad crítica VULN-001 requiere atención inmediata.', 'created_at' => now()->subMinutes(30)],
        ];

        return $data;
    }

    private function calculateAvgResolutionTimePerOrg(): array
    {
        $avgResolutionTime = [];
        $organizations = Organization::with(['vulnerabilities' => function ($query) {
            $query->where('status', 'resolved')->whereNotNull('resolved_at')->whereNotNull('created_at');
        }])->get();

        foreach ($organizations as $org) {
            if ($org->vulnerabilities->isEmpty()) {
                $avgResolutionTime[$org->name] = 0; // Or null, depending on desired output for no resolved vulnerabilities
                continue;
            }

            $totalDays = 0;
            $resolvedCount = 0;

            foreach ($org->vulnerabilities as $vuln) {
                // Ensure created_at and resolved_at are Carbon instances for diffInDays
                $createdAt = Carbon::parse($vuln->created_at);
                $resolvedAt = Carbon::parse($vuln->resolved_at);
                $totalDays += $createdAt->diffInDays($resolvedAt);
                $resolvedCount++;
            }

            $avgResolutionTime[$org->name] = $resolvedCount > 0 ? round($totalDays / $resolvedCount, 2) : 0;
        }

        return $avgResolutionTime;
    }

    public function getAvgResolutionTimePerOrgData(): array
    {
        return $this->calculateAvgResolutionTimePerOrg();
    }
}
