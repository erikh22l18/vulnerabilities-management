<?php

namespace App\Domain\Tasks\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Domain\Vulnerabilities\Models\Vulnerability;
use App\Domain\Tasks\Models\Task;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = Auth::user();
        if (!$user) {
            return false;
        }

        // General permission to create any task (TaskPolicy@create with null context)
        if (!$user->can('create', Task::class)) {
            return false;
        }

        $vulnerabilityId = $this->input('vulnerability_id');
        if ($vulnerabilityId) {
            $vulnerability = Vulnerability::find($vulnerabilityId);
            if ($vulnerability) {
                // Specific permission to create tasks FOR THIS VULNERABILITY (VulnerabilityPolicy@crearTareas)
                return $user->can('crearTareas', $vulnerability);
            }
            return false; // Vulnerability not found, deny.
        }
        // If vulnerability_id is not part of the request, but rules say it's required,
        // validation will fail it. If it were optional, this FormRequest would need
        // to decide if creating a task not linked to a vulnerability is allowed at this stage.
        // Given 'vulnerability_id' is required in rules, this path (vulnerabilityId is null)
        // should ideally not be hit if validation runs before authorize (which it does).
        // However, if somehow it's hit, and vulnerability_id is required, it's an invalid state.
        // Let's assume for a task creation, vulnerability_id is crucial, so if not found, it's an issue.
        // The 'required' rule for vulnerability_id will handle cases where it's not provided.
        // This authorize method primarily ensures that IF a vulnerability_id is given, the user can create tasks for it.
        return false; // Should not be reached if vulnerability_id is required and present.
                      // If vulnerability_id is not present, validation handles it.
                      // If it is present but not found, handled above.
                      // This line implies creating a task without a vulnerability_id is denied by default.
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'vulnerability_id' => 'required|exists:vulnerabilities,id',
            'project_id' => 'nullable|exists:projects,id', // Often derived from vulnerability
            'assigned_to' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date',
            'priority' => 'required|string|in:baja,media,alta,critica',
            'status' => 'required|string|in:pendiente,en_progreso,completada,cancelada',
        ];
    }
}
