<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController; // Ensure this is imported

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// New dashboard metrics API routes
Route::middleware('auth:sanctum')->prefix('v1/dashboard/metrics')->name('api.dashboard.metrics.')->group(function () {
    Route::get('admin', [DashboardController::class, 'getAdminDashboardApiMetrics'])->name('admin');
    Route::get('lider', [DashboardController::class, 'getLiderDashboardApiMetrics'])->name('lider');
    Route::get('miembro', [DashboardController::class, 'getMiembroDashboardApiMetrics'])->name('miembro');
});
