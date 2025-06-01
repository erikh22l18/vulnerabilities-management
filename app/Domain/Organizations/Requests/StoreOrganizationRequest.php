<?php

namespace App\Domain\Organizations\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Domain\Organizations\Models\Organization;

class StoreOrganizationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Organization::class);
    }

    public function rules(): array
    {
        return [
            'name'           => 'required|string|max:255|unique:organizations,name',
            'location'       => 'nullable|string|max:255',
            'business_model' => 'nullable|string|max:255',
        ];
    }
}
