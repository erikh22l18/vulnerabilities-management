<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Domain\Dashboard\Services\AdminDashboardService;
use App\Domain\Dashboard\Services\LiderDashboardService;
use App\Domain\Dashboard\Services\MiembroDashboardService;
use App\Models\AuditLog; // Added AuditLog model

class DashboardController extends Controller
{
    protected AdminDashboardService $adminDashboardService;
    protected LiderDashboardService $liderDashboardService;
    protected MiembroDashboardService $miembroDashboardService;

    public function __construct(
        AdminDashboardService $adminDashboardService,
        LiderDashboardService $liderDashboardService,
        MiembroDashboardService $miembroDashboardService
    ) {
        $this->adminDashboardService = $adminDashboardService;
        $this->liderDashboardService = $liderDashboardService;
        $this->miembroDashboardService = $miembroDashboardService;
    }

    public function index(Request $request): View
    {
        $user = Auth::user();
        $data = [];
        $dashboard_type = 'default'; // Fallback dashboard type

        // Determine role and fetch data accordingly
        // Assumes primary role for simplicity. Order matters if user can have multiple roles.
        if ($user->hasRole('admin')) {
            $data = $this->adminDashboardService->getData($request);
            $dashboard_type = 'admin';
        } elseif ($user->hasRole('lider')) {
            $data = $this->liderDashboardService->getData($user, $request);
            $dashboard_type = 'lider';
        } elseif ($user->hasRole('miembro')) {
            $data = $this->miembroDashboardService->getData($user, $request);
            $dashboard_type = 'miembro';
        } else {
            // Handle users with no specific dashboard role or a default view
            // For now, $data remains empty, or you can define default data.
        }

        // Add Audit Log entry for dashboard view
        if (Auth::check()) { // Ensure user is authenticated before logging
            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'view_dashboard',
                'description' => "User viewed {$dashboard_type} dashboard.",
                'auditable_type' => 'Dashboard',
                'auditable_id' => null,
            ]);
        }

        return view('dashboard', [
            'service_data' => $data,
            'dashboard_type' => $dashboard_type
        ]);
    }

    public function getAdminAvgResolutionTimeByOrg(Request $request, AdminDashboardService $adminDashboardService)
    {
        $user = $request->user();

        // Ensure only authenticated admins can access
        if (!$user || !$user->hasRole('admin')) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $data = $adminDashboardService->getAvgResolutionTimePerOrgData();
        return response()->json($data);
    }
}
