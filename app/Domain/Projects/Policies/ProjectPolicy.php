<?php

namespace App\Domain\Projects\Policies;

use App\Models\User;
use App\Domain\Projects\Models\Project;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProjectPolicy
{
    use HandlesAuthorization;

    /**
     * Determina si el usuario puede ver listados de proyectos.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('ver proyectos');
    }

    /**
     * Determina si el usuario puede ver un proyecto específico.
     */
    public function view(User $user, Project $project): bool
    {
        // Permiso básico
        if (!$user->hasPermissionTo('ver proyectos')) {
            return false;
        }
        
        // Admin y líderes pueden ver todos los proyectos
        if ($user->hasRole(['admin', 'lider'])) {
            return true;
        }
        
        // Miembros solo pueden ver proyectos a los que pertenecen
        return $project->users->contains($user->id);
    }

    /**
     * Determina si el usuario puede crear proyectos.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('crear proyectos');
    }

    /**
     * Determina si el usuario puede actualizar un proyecto.
     */
    public function update(User $user, Project $project): bool
    {
        // Permiso básico
        if (!$user->hasPermissionTo('editar proyectos')) {
            return false;
        }
        
        // Admin y líderes pueden editar todos los proyectos
        if ($user->hasRole(['admin', 'lider'])) {
            return true;
        }
        
        // Miembros no pueden editar proyectos, incluso si pertenecen a ellos
        return false;
    }

    /**
     * Determina si el usuario puede eliminar un proyecto.
     */
    public function delete(User $user, Project $project): bool
    {
        // Solo usuarios con permiso específico pueden eliminar proyectos
        return $user->hasPermissionTo('eliminar proyectos');
    }

    /**
     * Determina si el usuario puede restaurar un proyecto eliminado.
     */
    public function restore(User $user, Project $project): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determina si el usuario puede eliminar permanentemente un proyecto.
     */
    public function forceDelete(User $user, Project $project): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determina si el usuario puede asignar miembros a un proyecto.
     */
    public function asignarUsuarios(User $user, Project $project): bool
    {
        return $user->hasPermissionTo('asignar proyectos');
    }

    /**
     * Determina si el usuario puede ver vulnerabilidades del proyecto.
     */
    public function verVulnerabilidades(User $user, Project $project): bool
    {
        // Verifica permiso básico
        if (!$user->hasPermissionTo('ver vulnerabilidades')) {
            return false;
        }
        
        // Admin y líderes pueden ver todas las vulnerabilidades de cualquier proyecto
        if ($user->hasRole(['admin', 'lider'])) {
            return true;
        }
        
        // Miembros solo pueden ver vulnerabilidades de proyectos a los que pertenecen
        return $project->users->contains($user->id);
    }

    /**
     * Determina si el usuario puede crear vulnerabilidades en el proyecto.
     */
    public function crearVulnerabilidades(User $user, Project $project): bool
    {
        // Verifica permiso básico
        if (!$user->hasPermissionTo('crear vulnerabilidades')) {
            return false;
        }

        // Verifica si el proyecto está activo
        if ($project->status !== 'active') {
            return false;
        }
        
        // Admin y líderes pueden crear vulnerabilidades en cualquier proyecto
        if ($user->hasRole(['admin', 'lider'])) {
            return true;
        }
        
        // Miembros solo pueden crear vulnerabilidades en proyectos a los que pertenecen
        return $project->users->contains($user->id);
    }

    /**
     * Determina si el usuario puede generar el informe PDF de un proyecto.
     */
    public function viewPdfReport(User $user, Project $project): bool
    {
        // El Gate::before en AuthServiceProvider ya permite a los 'admin'.
        // Aquí solo necesitamos verificar el rol 'lider'.
        // La pertenencia al proyecto no es necesaria si ya es líder,
        // y el middleware de ruta ya se encarga de la restricción a 'lider'.
        // Esta política es para la visibilidad del botón y una posible autorización directa.
        return $user->hasRole('lider');
    }
}
