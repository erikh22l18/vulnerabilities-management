<?php

namespace App\Domain\Dashboard\Services;

use App\Domain\Projects\Models\Project;
use App\Models\User; // Assuming User model is in App\Models
use Illuminate\Http\Request;

class AdminDashboardService
{
    public function getData(Request $request = null): array
    {
        // Initial basic metrics for Admin
        $data = [
            'total_projects' => Project::count(),
            'total_users' => User::count(),
            // Add more admin-specific data points here later
        ];

        // Sample global alerts
        $data['global_alerts'] = [
            ['message' => 'Sistema: Actualización de seguridad programada para medianoche.', 'created_at' => now()->subHours(1)],
            ['message' => 'Proyecto Alpha: Vulnerabilidad crítica VULN-001 requiere atención inmediata.', 'created_at' => now()->subMinutes(30)],
        ];

        return $data;
    }
}
