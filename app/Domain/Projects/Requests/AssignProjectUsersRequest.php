<?php

namespace App\Domain\Projects\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Domain\Projects\Models\Project;
use App\Models\User; // For checking organization consistency if needed
use Illuminate\Validation\Rule; // For more complex rules if needed

class AssignProjectUsersRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Project|null $project */
        $project = $this->route('project');
        if (!$project) {
            return false; // Should not happen with route model binding
        }
        // Uses ProjectPolicy@asignarUsuarios
        return $this->user()->can('asignarUsuarios', $project);
    }

    public function rules(): array
    {
        return [
            'user_ids'   => 'present|array', // 'present' ensures key exists, even if empty for removing all
            'user_ids.*' => 'distinct|exists:users,id',
            'roles'      => 'present|array', // Roles for each user_id
            // Ensure that for every user_id sent, there's a corresponding role.
            // And that every role provided corresponds to a user_id.
            // This is tricky with basic validation rules if 'user_ids' and 'roles' keys don't match.
            // Assuming 'roles' is an associative array keyed by user_id: roles[user_id] = role_name
            // If 'roles' is an indexed array parallel to 'user_ids', a custom rule or 'after' hook is better.
            // The controller's original logic implies 'roles' is an array where index might not match user_id.
            // For 'roles.*' to validate against specific user_id in 'user_ids.*', it's complex.
            // The original controller logic was: $role = $request->input('roles')[$userId] ?? 'miembro';
            // This implies roles is an associative array keyed by user_id.
            'roles.*'    => ['required', 'string', Rule::in(['lider', 'miembro', 'viewer'])], // Adjust roles if different
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $userIds = $this->input('user_ids', []);
            $roles = $this->input('roles', []);

            if (count($userIds) !== count($roles)) {
                 // This check is problematic if roles is associative array keyed by user_id
                 // For now, we assume the controller logic will handle mapping.
                 // A better validation would ensure all user_ids have a role if roles are provided.
            }

            // Ensure all users being assigned belong to the same organization as the project.
            /** @var Project $project */
            $project = $this->route('project');
            if ($project && $project->organization_id) {
                $projectOrganizationId = $project->organization_id;
                $users = User::whereIn('id', $userIds)->get();
                foreach ($users as $user) {
                    if ($user->organization_id !== $projectOrganizationId) {
                        $validator->errors()->add('user_ids', "El usuario {$user->name} no pertenece a la organización del proyecto.");
                    }
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'user_ids.present' => 'Debe proporcionar un listado de usuarios (puede ser vacío para desasignar todos).',
            'user_ids.*.exists' => 'Uno o más usuarios seleccionados no son válidos.',
            'user_ids.*.distinct' => 'La lista de usuarios contiene duplicados.',
            'roles.present' => 'Debe proporcionar los roles para los usuarios.',
            'roles.*.required' => 'Se debe asignar un rol a cada usuario.',
            'roles.*.in' => 'El rol seleccionado no es válido. Roles permitidos: lider, miembro, viewer.',
        ];
    }
}
