<?php

use Illuminate\Support\Facades\Route;
use App\Domain\Vulnerabilities\Controllers\VulnerabilityController;
use App\Domain\Vulnerabilities\Controllers\VulnerabilityUserController;
use App\Http\Controllers\Admin\UserController as AdminUserController; // Changed this line
use App\Domain\Organizations\Controllers\OrganizationController;
use App\Domain\Organizations\Controllers\OrganizationProjectController;
use App\Domain\Projects\Controllers\ProjectController;
use App\Domain\Projects\Controllers\ProjectUserController;
use App\Domain\Projects\Controllers\ProjectTaskController;
use App\Domain\Vulnerabilities\Controllers\VulnerabilityTaskController;
use App\Domain\Tasks\Controllers\TaskController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::middleware('role:lider')->group(function () {
        Route::get('/users/{user}/roles', [UserRoleController::class, 'edit'])->name('users.roles.edit');
        Route::put('/users/{user}/roles', [UserRoleController::class, 'update'])->name('users.roles.update');

        Route::get('/users/{user}/organization', [UserOrganizationController::class, 'edit'])->name('users.organization.edit');
        Route::put('/users/{user}/organization', [UserOrganizationController::class, 'update'])->name('users.organization.update');

        Route::resource('vulnerabilities', VulnerabilityController::class)->except(['index', 'show']);

        Route::prefix('vulnerabilities/{vulnerability}/users')->name('vulnerabilities.users.')->group(function () {
            Route::get('/', [VulnerabilityUserController::class, 'index'])->name('index');
            Route::get('/create', [VulnerabilityUserController::class, 'create'])->name('create');
            Route::post('/', [VulnerabilityUserController::class, 'store'])->name('store');
            Route::delete('/{user}', [VulnerabilityUserController::class, 'destroy'])->name('destroy');
        });

        // Route::get('/projects/{project}/tasks', [ProjectTaskController::class, 'index'])
        //     ->name('projects.tasks.index');
        // Route::get('/users/{user}/tasks', [UserTaskController::class, 'index'])
        //     ->name('users.tasks.index');

        Route::get('/vulnerabilities/charge', [VulnerabilityController::class, 'chargeFile'])
            ->name('vulnerabilities.charge');

        Route::post('/vulnerabilities/upload', [VulnerabilityController::class, 'uploadFile'])
            ->name('vulnerabilities.upload');

        Route::get('/vulnerabilities/template', [VulnerabilityController::class, 'template'])
            ->name('vulnerabilities.template');

        Route::get('/vulnerabilities/{vulnerability}/pdf', [VulnerabilityController::class, 'generatePDF'])->name('vulnerabilities.pdf');

        Route::post('/vulnerabilities/validate-rows', [VulnerabilityController::class, 'validateRows'])
            ->name('vulnerabilities.validateRows');

        Route::post('/vulnerabilities/validate-header', [VulnerabilityController::class, 'validateHeader'])
            ->name('vulnerabilities.validateHeader');

        Route::get('/vulnerabilities/{vulnerability}/tasks/create', [VulnerabilityTaskController::class, 'create'])
            ->name('vulnerabilities.tasks.create');
            
        Route::post('/vulnerabilities/{vulnerability}/tasks', [VulnerabilityTaskController::class, 'store'])
            ->name('vulnerabilities.tasks.store');

        Route::post('/vulnerabilities/{vulnerability}/comment', [VulnerabilityController::class, 'comment']) // Esta se mantendrá para solo añadir comentarios
            ->name('vulnerabilities.comment');
        
        Route::post('/vulnerabilities/{vulnerability}/change-state', [VulnerabilityController::class, 'changeState']) // Nueva ruta para cambio de estado
            ->name('vulnerabilities.change-state');

        Route::resource('tasks', TaskController::class);

        Route::resource('organizations', OrganizationController::class)
            ->except(['show']);

        Route::prefix('organizations/{organization}/users')->name('organizations.users.')->group(function () {
            Route::get('/', [OrganizationUserController::class, 'index'])->name('index');
            Route::get('/create', [OrganizationUserController::class, 'create'])->name('create');
            Route::post('/', [OrganizationUserController::class, 'store'])->name('store');
            Route::get('/{user}/edit', [OrganizationUserController::class, 'edit'])->name('edit');
            Route::put('/{user}', [OrganizationUserController::class, 'update'])->name('update');
            Route::delete('/{user}', [OrganizationUserController::class, 'destroy'])->name('destroy');
        });

        Route::get('/organizations/{organization}/projects', [OrganizationProjectController::class, 'index'])
            ->name('organizations.projects.index');
        Route::get('/organizations/{organization}/projects/create', [ProjectController::class, 'create'])
            ->name('organizations.projects.create');


        Route::get('/projects/{project}/users', [ProjectUserController::class, 'index'])->name('projects.users.index');
        Route::get('/projects/{project}/users/create', [ProjectUserController::class, 'create'])->name('projects.users.create');
        Route::post('/projects/{project}/users', [ProjectUserController::class, 'store'])->name('projects.users.store');
        Route::delete('/projects/{project}/users/{user}', [ProjectUserController::class, 'destroy'])->name('projects.users.destroy');


        Route::get('/projects/{project}/vulnerabilities/create', [ProjectVulnerabilityController::class, 'create'])
            ->name('projects.vulnerabilities.create');
        Route::post('/projects/{project}/vulnerabilities', [ProjectVulnerabilityController::class, 'store'])
            ->name('projects.vulnerabilities.store');
        
        // Nueva ruta para el informe PDF del proyecto
        Route::get('/projects/{project}/report/pdf', [ProjectController::class, 'generateProjectReportPDF'])
            ->name('projects.report.pdf');

        Route::resource('admin/users', AdminUserController::class)->names('admin.users'); // Changed this line
    });

    Route::resource(('projects'), ProjectController::class)
        ->except(['show']);
    Route::get('/projects/{project}/vulnerabilities', [ProjectVulnerabilityController::class, 'index'])
        ->name('projects.vulnerabilities.index');

    Route::get('/vulnerabilities', [VulnerabilityController::class, 'index'])
        ->name('vulnerabilities.index');

    Route::get('/vulnerabilities/{vulnerability}', [VulnerabilityController::class, 'show'])
        ->name('vulnerabilities.show');

    Route::get('/vulnerabilities/{vulnerability}/tasks', [VulnerabilityTaskController::class, 'index'])
        ->name('vulnerabilities.tasks.index');

    Route::get('/vulnerabilities/{vulnerability}/tasks/{task}', [VulnerabilityTaskController::class, 'show'])
        ->name('vulnerabilities.tasks.show');
});
