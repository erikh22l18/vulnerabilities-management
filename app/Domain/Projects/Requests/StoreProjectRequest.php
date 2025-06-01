<?php

namespace App\Domain\Projects\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Domain\Projects\Models\Project;

class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Project::class);
    }

    public function rules(): array
    {
        return [
            'identifier'      => 'required|string|max:100|unique:projects,identifier',
            'name'            => 'required|string|max:255',
            'general_objective' => 'required|string|max:500', // As per controller, was required
            'organization_id' => 'required|exists:organizations,id',
            // 'status' typically defaults on creation, not set by user directly here
            // 'lider_id' could be added here if it's set during creation
        ];
    }
}
