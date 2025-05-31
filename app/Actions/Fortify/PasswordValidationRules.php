<?php

namespace App\Actions\Fortify;

use Illuminate\Validation\Rules\Password;

trait PasswordValidationRules
{
    /**
     * Get the validation rules used to validate passwords.
     *
     * @return array<int, \Illuminate\Contracts\Validation\Rule|array<mixed>|string>
     */
    protected function passwordRules(): array
    {
        return [
            'required',
            'string',
            Password::min(10)       // Longitud mínima de 10 caracteres
                ->mixedCase()       // Requiere al menos una letra mayúscula y una minúscula
                ->numbers()         // Requiere al menos un número
                ->symbols()         // Requiere al menos un símbolo
                ->uncompromised(),  // Verifica si la contraseña ha aparecido en brechas de datos conocidas
            'confirmed'
        ];
    }
}
