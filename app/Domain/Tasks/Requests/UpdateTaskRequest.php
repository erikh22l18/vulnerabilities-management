<?php

namespace App\Domain\Tasks\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Domain\Vulnerabilities\Models\Vulnerability; // For context if vulnerability_id changes
use App\Domain\Tasks\Models\Task; // For $this->route('task') type hint if possible

class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = Auth::user();
        if (!$user) {
            return false;
        }

        /** @var Task|null $task */
        $task = $this->route('task'); // $task is the task being updated
        if (!$task) {
            return false; // Should not happen with route model binding
        }

        // Check general permission to update this specific task (TaskPolicy@update)
        if (!$user->can('update', $task)) {
            return false;
        }

        // If vulnerability_id is being provided (or is already set on the task),
        // ensure the user can still create/manage tasks for that vulnerability context.
        // This prevents associating the task with a vulnerability in an inactive project or a closed one.
        $vulnerabilityId = $this->input('vulnerability_id', $task->vulnerability_id);

        if ($vulnerabilityId) {
            $vulnerability = Vulnerability::find($vulnerabilityId);
            if ($vulnerability) {
                // Check if user can generally create tasks for this vulnerability (context check)
                // VulnerabilityPolicy@crearTareas includes project active and vuln state checks.
                return $user->can('crearTareas', $vulnerability);
            }
            return false; // New/existing vulnerability_id points to a non-existent vulnerability
        }

        // If task is not linked to a vulnerability (e.g., only project_id, or standalone if allowed)
        // The TaskPolicy@update already performed its checks (e.g., project status if directly linked to project).
        // If task is being *disassociated* from a vulnerability (vulnerability_id becomes null),
        // this logic path is taken. This assumes disassociation is allowed if general update is.
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'vulnerability_id' => 'sometimes|nullable|exists:vulnerabilities,id', // Nullable if task can be disassociated
            'project_id' => 'sometimes|nullable|exists:projects,id', // Often derived
            'assigned_to' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date',
            'priority' => 'sometimes|required|string|in:baja,media,alta,critica',
            'status' => 'sometimes|required|string|in:pendiente,en_progreso,completada,cancelada',
        ];
    }
}
