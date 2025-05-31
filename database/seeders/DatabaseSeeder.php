<?php

namespace Database\Seeders;

use App\Models\User;
use App\Domain\Organizations\Models\Organization;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;
use App\Domain\Projects\Models\Project;
use App\Domain\Vulnerabilities\Models\Vulnerability;
use App\Domain\Vulnerabilities\Models\VulnerabilityType;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            RolePermissionSeeder::class,
            UserSeeder::class,
            VulnerabilityTypeSeeder::class,
            VulnerabilityCategorySeeder::class,
            VulnerabilityStatusSeeder::class,
        ]);

        // a admin darle el rol de admin
        $admin = User::where('email', 'admin@example.com')->first();
        $admin->assignRole('admin');

        // a leader darle el rol de lider
        $leader = User::where('email', 'leader@example.com')->first();
        $leader->assignRole('lider');

        // a member darle el rol de miembro
        $member = User::where('email', 'member@example.com')->first();
        $member->assignRole('miembro');


        // Create organizations
        $organizations = [
            'Organización Alpha' => ['ALPHA', 'Sede Central, Ciudad A'],
            'Organización Beta' => ['BETA', 'Sede Principal, Ciudad B'],
            'Organización Gamma' => ['GAMMA', 'Oficina Central, Ciudad C'],
        ];
        $admin = User::where('email', 'admin@example.com')->first();
        $states = ['Detectada', 'Asignada', 'En tratamiento', 'Resuelta', 'Cerrada'];
        $severityLevels = ['Crítico', 'Alto', 'Medio', 'Bajo'];
        $types = VulnerabilityType::pluck('id')->toArray();

        // Create 15 users
        $users = [];
        for ($i = 1; $i <= 21; $i++) {
            $users[] = User::create([
                'name' => "Usuario {$i}",
                'email' => "usuario{$i}@example.com",
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'remember_token' => Str::random(10),
            ]);
        }

        $availableUsers = collect($users);

        foreach ($organizations as $orgName => [$prefix, $location]) {
            $organization = Organization::create([
                'name' => $orgName,
                'location' => $location
            ]);

            // Assign random users to this organization (between 3 and 5 users)
            $organizationUsers = $availableUsers->random(rand(5, 7));
            $availableUsers = $availableUsers->diff($organizationUsers);
            foreach ($organizationUsers as $user) {
                $user->update(['organization_id' => $organization->id]);
                $user->assignRole('miembro');
            }

            // Create 3 projects for each organization
            $numProjects = rand(3, 5);
            for ($i = 1; $i <= $numProjects; $i++) {
                $project = Project::create([
                    'name' => "Proyecto {$i} - {$organization->name}",
                    'identifier' => "{$prefix}-{$i}",
                    'organization_id' => $organization->id,
                    'general_objective' => "Objetivo general del proyecto {$i} para {$organization->name}"
                ]);

                // Assign random users from the same organization to this project (between 1 and 3 users)
                $numUserProject = rand(2, 5);
                $projectUsers = $organizationUsers->random($numUserProject);
                foreach ($projectUsers as $user) {
                    // Assuming you have a project_user pivot table
                    $project->users()->attach($user->id);
                }

                // Create 5 vulnerabilities for each project
                $numVulnerabilities = rand(3, 5);
                for ($j = 1; $j <= $numVulnerabilities; $j++) {
                    $vulnerability = Vulnerability::create([
                        'title' => "Vulnerabilidad {$j} - {$project->identifier}",
                        'description' => "Descripción detallada de la vulnerabilidad {$j} encontrada en el proyecto {$project->name}",
                        'state' => $states[array_rand($states)],
                        'detection_date' => now()->subDays(rand(1, 30)),
                        'project_id' => $project->id,
                        'category_id' => 1, // Assuming you have a default category
                        'created_by' => $admin->id,
                        'type_id' => $types[array_rand($types)],
                        'severity_level' => $severityLevels[array_rand($severityLevels)],
                        'component' => "Componente " . rand(1, 3),
                        'cvss_score' => rand(1, 10),
                        'priority' => ['Alta', 'Media', 'Baja'][rand(0, 2)],
                        'detection_source' => ['Pentesting', 'Análisis de código', 'Reporte externo'][rand(0, 2)],
                        'resolution_deadline' => now()->addDays(rand(7, 30)),
                        'documentation_url' => 'https://example.com/doc-' . rand(1000, 9999),
                    ]);

                    // Assign random users from the same project to this vulnerability (between 1 and 3 users)
                    $numUserVulnerability = min(3, $numUserProject); // Ensure we don't try to select more users than available
                    $vulnerabilityUsers = $projectUsers->random(rand(1, $numUserVulnerability));

                    // Attach the selected users to the vulnerability
                    $vulnerability->assignedUsers()->attach($vulnerabilityUsers->pluck('id'));
                }
            }
        }
    

        $member->projects()->attach($project);
    }
}
