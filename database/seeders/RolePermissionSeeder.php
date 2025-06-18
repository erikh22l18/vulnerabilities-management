<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear los tres roles
        $roleAdmin = Role::firstOrCreate(['name' => 'admin']);
        $roleLeader = Role::firstOrCreate(['name' => 'lider']);
        $roleMember = Role::firstOrCreate(['name' => 'miembro']);

        // Definir todos los permisos del sistema
        $permissions = collect([
            // Usuarios
            'crear usuarios',
            'editar usuarios',
            'asignar usuarios',
            'ver usuarios',
            'eliminar usuarios',
            
            // Organizaciones
            'crear organizaciones',
            'editar organizaciones',
            'asignar organizaciones',
            'ver organizaciones',
            'eliminar organizaciones',
            
            // Proyectos
            'crear proyectos',
            'editar proyectos',
            'asignar proyectos',
            'ver proyectos',
            'eliminar proyectos',
            
            // Vulnerabilidades
            'ver vulnerabilidades',
            'crear vulnerabilidades',
            'editar vulnerabilidades',
            'asignar vulnerabilidades',
            'eliminar vulnerabilidades',
            'importar vulnerabilidades',
            
            // Tareas
            'crear tareas',
            'editar tareas',
            'asignar tareas',
            'ver tareas',
            'eliminar tareas',
            
            // Informes
            'ver informes',
            'exportar informes',
        ])->map(fn($name) => Permission::firstOrCreate(['name' => $name]));

        // El admin tiene todos los permisos
        $roleAdmin->syncPermissions($permissions);

        // El líder tiene la mayoría de los permisos pero no puede gestionar usuarios ni organizaciones a nivel avanzado
        $roleLeader->syncPermissions(
            $permissions->whereIn('name', [
                // Usuarios - solo ver y asignar
                'ver usuarios',
                'asignar usuarios',
                
                // Organizaciones - solo ver
                'ver organizaciones',
                
                // Proyectos - control total
                'crear proyectos',
                'editar proyectos',
                'asignar proyectos',
                'ver proyectos',
                'eliminar proyectos',
                
                // Vulnerabilidades - control total
                'crear vulnerabilidades',
                'editar vulnerabilidades',
                'asignar vulnerabilidades',
                'ver vulnerabilidades',
                'eliminar vulnerabilidades',
                'importar vulnerabilidades',
                
                // Tareas - control total
                'crear tareas',
                'editar tareas',
                'asignar tareas',
                'ver tareas',
                'eliminar tareas',
                
                // Informes
                'ver informes',
                'exportar informes',
            ])
        );

        // El miembro tiene permisos básicos de visualización y creación limitada
        $roleMember->syncPermissions(
            $permissions->whereIn('name', [
                // Permisos de visualización básicos
                'ver usuarios',         // Para saber a quién asignar o ver responsables
                'ver organizaciones',   // Para contexto
                'ver proyectos',        // Para contexto
                'ver tareas',           // Para ver tareas asociadas a sus vulnerabilidades
                'ver vulnerabilidades', // Ver las vulnerabilidades que le conciernen
                'ver informes',         // Poder ver informes generales o de sus proyectos/vulnerabilidades

                // Permisos de acción para Vulnerabilidades
                'crear vulnerabilidades', // Requisito: Registro de vulnerabilidades
                'editar vulnerabilidades',// Requisito: Control de estados de *sus* vulnerabilidades (requiere lógica de negocio adicional para restringir a "sus")
                'crear tareas',         // Permitir a miembros crear tareas para sus vulnerabilidades
                'editar tareas',        // Permitir a miembros editar tareas que les conciernen
            ])->unique() // Asegurar que no haya duplicados si la lista base los tuviera
        );
    }
}