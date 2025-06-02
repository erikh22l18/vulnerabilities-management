<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse; // Added JsonResponse
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
                'auditable_type' => \App\Models\User::class,
                'auditable_id' => $user->id,
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

    public function getAdminDashboardApiMetrics(Request $request): JsonResponse
    {
        if (!$request->user() || !$request->user()->hasRole('admin')) {
            return response()->json(['error' => 'Unauthorized. Admin role required.'], 403);
        }
        // $this->adminDashboardService is already injected
        $data = $this->adminDashboardService->getData($request);
        // Optionally, you might want to fetch async data too if the API should include it:
        // $avgResolutionData = $this->adminDashboardService->getAvgResolutionTimePerOrgData();
        // $data['avg_resolution_time_per_org'] = $avgResolutionData;
        // For now, let's stick to the main getData() for simplicity in this step.
        return response()->json($data);
    }

    public function getLiderDashboardApiMetrics(Request $request): JsonResponse
    {
        if (!$request->user() || !$request->user()->hasRole('lider')) {
            // Also allow admin to access this for broader testing/utility
            if (!$request->user()->hasRole('admin')) {
                 return response()->json(['error' => 'Unauthorized. Lider or Admin role required.'], 403);
            }
        }
        // $this->liderDashboardService is already injected
        $data = $this->liderDashboardService->getData($request->user(), $request);
        return response()->json($data);
    }

    public function getMiembroDashboardApiMetrics(Request $request): JsonResponse
    {
        if (!$request->user() || !$request->user()->hasRole('miembro')) {
            // Also allow admin to access this
            if (!$request->user()->hasRole('admin')) {
                return response()->json(['error' => 'Unauthorized. Miembro or Admin role required.'], 403);
            }
        }
        // $this->miembroDashboardService is already injected
        $data = $this->miembroDashboardService->getData($request->user(), $request);
        return response()->json($data);
    }
}
