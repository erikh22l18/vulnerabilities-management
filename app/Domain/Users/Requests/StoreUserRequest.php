<?php

namespace App\Domain\Users\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'identification' => 'required|string|max:20|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'area' => 'nullable|string|max:100',
            'organization_id' => 'required|exists:organizations,id',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|exists:roles,name'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'El nombre es requerido',
            'identification.required' => 'La identificación es requerida',
            'identification.unique' => 'Esta identificación ya está en uso',
            'email.required' => 'El correo electrónico es requerido',
            'email.email' => 'Debe ser un correo electrónico válido',
            'email.unique' => 'Este correo electrónico ya está en uso',
            'organization_id.required' => 'La organización es requerida',
            'organization_id.exists' => 'La organización seleccionada no existe',
            'password.required' => 'La contraseña es requerida',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres',
            'password.confirmed' => 'Las contraseñas no coinciden',
            'role.required' => 'El rol es requerido',
            'role.exists' => 'El rol seleccionado no existe'
        ];
    }
}