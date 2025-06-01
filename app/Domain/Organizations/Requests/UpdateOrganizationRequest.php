<?php

namespace App\Domain\Organizations\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Domain\Organizations\Models\Organization; // Import for type hinting if needed

class UpdateOrganizationRequest extends FormRequest
{
    public function authorize(): bool
    {
        // $this->route('organization') will be the Organization model instance
        return $this->user()->can('update', $this->route('organization'));
    }

    public function rules(): array
    {
        $organizationId = null;
        if ($this->route('organization') instanceof Organization) {
            $organizationId = $this->route('organization')->id;
        } elseif (is_numeric($this->route('organization'))) {
            // Fallback if only ID is passed, though route model binding should provide the model
            $organizationId = $this->route('organization');
        }

        return [
            'name' => 'required|string|max:255|unique:organizations,name,' . $organizationId,
            'location' => 'nullable|string|max:255',
            'business_model' => 'nullable|string|max:255',
        ];
    }
}
