<?php

namespace App\Domain\Projects\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Domain\Projects\Models\Project; // Import Project

class UpdateProjectStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        // $this->route('project') will be the Project model instance
        // The 'update' permission (ProjectPolicy@update) should be sufficient
        // as changing status is a form of update.
        // If a more granular 'updateStatus' permission exists, it could be used.
        return $this->user()->can('update', $this->route('project'));
    }

    public function rules(): array
    {
        return [
            // Allowing both lowercase and capitalized versions for robustness,
            // though the application should ideally standardize on lowercase.
            'status' => 'required|string|in:active,inactive,activo,inactivo',
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        if ($this->has('status')) {
            $this->merge([
                'status' => strtolower($this->input('status')),
            ]);
        }
    }
}
