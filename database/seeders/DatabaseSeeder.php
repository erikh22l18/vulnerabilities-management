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
    

        // $member->projects()->attach($project); // This line seems out of context or erroneous, removing.

        // --- Start of Targeted Data Seeding for Dashboards ---

        $adminUser = User::where('email', 'admin@example.com')->first();
        $leaderUser = User::where('email', 'leader@example.com')->first();
        $memberUser = User::where('email', 'member@example.com')->first();

        $firstVulnerabilityType = VulnerabilityType::first();
        $firstVulnerabilityCategoryId = 1; // Assuming category with ID 1 exists from VulnerabilityCategorySeeder

        // Ensure a specific organization for targeted seeding
        $org1 = Organization::firstOrCreate(
            ['name' => 'Organización Dashboard Target'],
            ['location' => 'Locación Específica']
        );

        if ($leaderUser && $org1) {
            $leaderUser->organization_id = $org1->id;
            $leaderUser->save();
        }
        if ($memberUser && $org1) {
            $memberUser->organization_id = $org1->id;
            $memberUser->save();
        }

        // Create a specific active project for the leader and member
        $project1_org1 = null;
        if ($org1 && $leaderUser) {
            $project1_org1 = Project::updateOrCreate(
                ['name' => 'Proyecto Activo (Dashboard)', 'organization_id' => $org1->id],
                [
                    'identifier' => 'ORG1-PAD',
                    'general_objective' => 'Proyecto activo para dashboard de líder y miembro.',
                    'status' => 'active',
                    // 'lider_id' => $leaderUser->id, // Removed direct lider_id assignment
                    // 'created_by' => $adminUser->id, // Removed created_by assignment
                ]
            );
            $project1_org1->users()->syncWithoutDetaching([$leaderUser->id, $memberUser->id]);
        }

        // Create a specific inactive project for the leader and member
        $project2_org1 = null;
        if ($org1 && $leaderUser) {
            $project2_org1 = Project::updateOrCreate(
                ['name' => 'Proyecto Inactivo (Dashboard)', 'organization_id' => $org1->id],
                [
                    'identifier' => 'ORG1-PID',
                    'general_objective' => 'Proyecto inactivo para dashboard de líder y miembro.',
                    'status' => 'inactive',
                    // 'lider_id' => $leaderUser->id, // Removed direct lider_id assignment
                    // 'created_by' => $adminUser->id, // Removed created_by assignment
                ]
            );
            $project2_org1->users()->syncWithoutDetaching([$leaderUser->id, $memberUser->id]);
        }

        // Vulnerabilities for Project 1 (Active)
        if ($project1_org1 && $leaderUser && $memberUser && $adminUser && $firstVulnerabilityType) {
            // Open vuln for member
            $vuln1_p1 = Vulnerability::create([
                'title' => 'VULN-P1-MEMBER-OPEN', 'project_id' => $project1_org1->id, 'state' => 'Detectada',
                'assigned_user_id' => $memberUser->id, 'created_by' => $adminUser->id,
                'type_id' => $firstVulnerabilityType->id, 'category_id' => $firstVulnerabilityCategoryId,
                'description' => 'Desc', 'severity_level' => 'Alto', 'priority' => 'Alta'
            ]);
            // Open vuln for leader
            $vuln2_p1 = Vulnerability::create([
                'title' => 'VULN-P1-LEADER-OPEN', 'project_id' => $project1_org1->id, 'state' => 'En tratamiento',
                'assigned_user_id' => $leaderUser->id, 'created_by' => $adminUser->id,
                'type_id' => $firstVulnerabilityType->id, 'category_id' => $firstVulnerabilityCategoryId,
                'description' => 'Desc', 'severity_level' => 'Medio', 'priority' => 'Media'
            ]);
            // Closed vuln
            $vuln3_p1 = Vulnerability::create([
                'title' => 'VULN-P1-CLOSED', 'project_id' => $project1_org1->id, 'state' => 'Cerrada',
                'assigned_user_id' => $memberUser->id, 'created_by' => $adminUser->id,
                'type_id' => $firstVulnerabilityType->id, 'category_id' => $firstVulnerabilityCategoryId,
                'description' => 'Desc', 'severity_level' => 'Bajo', 'priority' => 'Baja'
            ]);

            // Tasks for Project 1
            Task::create([
                'title' => 'Tarea P1-V1 (Miembro)', 'vulnerability_id' => $vuln1_p1->id, 'project_id' => $project1_org1->id,
                'assigned_to' => $memberUser->id, 'created_by' => $adminUser->id, 'status' => 'pendiente', 'priority' => 'alta'
            ]);
            Task::create([
                'title' => 'Tarea P1-V2 (Líder)', 'vulnerability_id' => $vuln2_p1->id, 'project_id' => $project1_org1->id,
                'assigned_to' => $leaderUser->id, 'created_by' => $adminUser->id, 'status' => 'en_progreso', 'priority' => 'media'
            ]);
            Task::create([ // Task for closed vulnerability
                'title' => 'Tarea P1-V3 (Cerrada)', 'vulnerability_id' => $vuln3_p1->id, 'project_id' => $project1_org1->id,
                'assigned_to' => $memberUser->id, 'created_by' => $adminUser->id, 'status' => 'completada', 'priority' => 'baja'
            ]);
        }

        // Vulnerabilities for Project 2 (Inactive)
        if ($project2_org1 && $memberUser && $adminUser && $firstVulnerabilityType) {
            $vuln1_p2 = Vulnerability::create([
                'title' => 'VULN-P2-MEMBER-OPEN (Inactive Proj)', 'project_id' => $project2_org1->id, 'state' => 'Detectada',
                'assigned_user_id' => $memberUser->id, 'created_by' => $adminUser->id,
                'type_id' => $firstVulnerabilityType->id, 'category_id' => $firstVulnerabilityCategoryId,
                'description' => 'Desc', 'severity_level' => 'Alto', 'priority' => 'Alta'
            ]);

            // Task for Project 2 (Inactive)
            Task::create([
                'title' => 'Tarea P2-V1 (Miembro, Inactive Proj)', 'vulnerability_id' => $vuln1_p2->id, 'project_id' => $project2_org1->id,
                'assigned_to' => $memberUser->id, 'created_by' => $adminUser->id, 'status' => 'pendiente', 'priority' => 'alta'
            ]);
        }
        // --- End of Targeted Data Seeding ---
    }
}
