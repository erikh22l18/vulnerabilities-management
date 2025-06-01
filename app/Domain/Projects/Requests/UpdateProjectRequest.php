<?php

namespace App\Domain\Projects\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Domain\Projects\Models\Project; // Import Project for type hinting if needed

class UpdateProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        // $this->route('project') will be the Project model instance due to route model binding
        return $this->user()->can('update', $this->route('project'));
    }

    public function rules(): array
    {
        $projectId = null;
        if ($this->route('project') instanceof Project) {
            $projectId = $this->route('project')->id;
        } elseif (is_numeric($this->route('project'))) {
            $projectId = $this->route('project');
        }

        return [
            'identifier'      => 'required|string|max:100|unique:projects,identifier,' . $projectId,
            'name'            => 'required|string|max:255',
            'general_objective' => 'required|string|max:500', // As per controller, was required
            'organization_id' => 'required|exists:organizations,id',
            // 'status' is handled by UpdateProjectStatusRequest
            // 'lider_id' could be added here if it's updatable via this form
        ];
    }
}
