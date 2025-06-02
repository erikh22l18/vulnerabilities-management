<?php

namespace App\Domain\Organizations\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User; // For $this->route('user') type hint

class UpdateOrganizationUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var User|null $userToUpdate */
        $userToUpdate = $this->route('user'); // This is the user whose details are being updated
        if (!$userToUpdate) {
            return false;
        }
        // Authorization based on updating the user being modified.
        // Assumes a general UserPolicy@update exists or relevant permission.
        // The controller also checks if the user belongs to the organization.
        return $this->user()->can('update', $userToUpdate);
    }

    public function rules(): array
    {
        $userId = null;
        if ($this->route('user') instanceof User) {
            $userId = $this->route('user')->id;
        } elseif (is_numeric($this->route('user'))) {
            $userId = $this->route('user');
        }

        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $userId,
            // Password update is typically handled in a separate, dedicated form/request.
            // If password can be optionally updated here:
            // 'password' => 'nullable|string|min:8|confirmed',
            // 'area' could be added here if it's editable on this form
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser una dirección válida.',
            'email.unique' => 'Este correo electrónico ya está en uso por otro usuario.',
            // 'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            // 'password.confirmed' => 'La confirmación de contraseña no coincide.',
        ];
    }
}
