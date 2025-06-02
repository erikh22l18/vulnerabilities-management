<?php

namespace App\Domain\Organizations\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Domain\Organizations\Models\Organization;

class StoreOrganizationUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Organization|null $organization */
        $organization = $this->route('organization');
        if (!$organization) {
            return false; // Should not happen with route model binding
        }
        // Authorization is based on whether the current user can update the organization
        // (which implies they can add users to it).
        // This aligns with the controller's original $this->authorize('update', $organization);
        return $this->user()->can('update', $organization);
    }

    public function rules(): array
    {
        if ($this->has('create_new_user_form') || $this->input('action_type') === 'create_new') {
            // Rules for creating a new user and adding them to the organization
            return [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email',
                'password' => 'required|string|min:8|confirmed',
                // 'area' can be added here if it's part of new user creation form
                // 'role_id' or 'role_name' can be added if role is set at creation time
            ];
        } else {
            // Rules for adding existing users to the organization
            return [
                'user_ids' => 'required|array',
                'user_ids.*' => 'exists:users,id',
            ];
        }
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del nuevo usuario es obligatorio.',
            'email.required' => 'El correo electrónico del nuevo usuario es obligatorio.',
            'email.unique' => 'Este correo electrónico ya está registrado.',
            'email.email' => 'El correo electrónico debe ser una dirección válida.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
            'user_ids.required' => 'Debe seleccionar al menos un usuario existente.',
            'user_ids.*.exists' => 'Uno o más IDs de usuario seleccionados no son válidos.',
        ];
    }
}
