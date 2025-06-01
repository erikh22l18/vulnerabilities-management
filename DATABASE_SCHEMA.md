# Database Schema Documentation

## Introducción

Este documento detalla el esquema de la base de datos para la aplicación de Gestión de Vulnerabilidades. Describe cada tabla, sus columnas, tipos de datos, restricciones y relaciones importantes.

## Tabla de Contenidos

1.  [`users`](#users-table)
2.  [`password_reset_tokens`](#password_reset_tokens-table)
3.  [`sessions`](#sessions-table)
4.  [`organizations`](#organizations-table)
5.  [`projects`](#projects-table)
6.  Spatie Permission Tables
    *   [`permissions`](#permissions-table)
    *   [`roles`](#roles-table)
    *   [`model_has_permissions`](#model_has_permissions-table)
    *   [`model_has_roles`](#model_has_roles-table)
    *   [`role_has_permissions`](#role_has_permissions-table)
7.  [`vulnerability_types`](#vulnerability_types-table)
8.  [`vulnerability_categories`](#vulnerability_categories-table)
9.  [`vulnerability_statuses`](#vulnerability_statuses-table)
10. [`areas`](#areas-table)
11. [`assignments`](#assignments-table)
12. [`equipment`](#equipment-table)
13. [`vulnerabilities`](#vulnerabilities-table)
14. [`vulnerability_comments`](#vulnerability_comments-table)
15. [`vulnerability_files`](#vulnerability_files-table)
16. [`vulnerability_user`](#vulnerability_user-table) (Pivot)
17. [`project_user`](#project_user-table) (Pivot)
18. [`tasks`](#tasks-table)
19. [`audit_logs`](#audit_logs-table)

---

## `users` table
Almacena la información de los usuarios del sistema.

| Column Name             | Data Type     | Constraints & Notes                                       |
|-------------------------|---------------|-----------------------------------------------------------|
| `id`                    | `BIGINT(20) UNSIGNED` | `AUTO_INCREMENT`, `PRIMARY KEY`                           |
| `name`                  | `VARCHAR(255)`| `NOT NULL`                                                |
| `email`                 | `VARCHAR(255)`| `NOT NULL`, `UNIQUE`                                      |
| `email_verified_at`     | `TIMESTAMP`   | `NULLABLE`                                                |
| `password`              | `VARCHAR(255)`| `NOT NULL`                                                |
| `two_factor_secret`     | `TEXT`        | `NULLABLE` (Cifrado)                                      |
| `two_factor_recovery_codes` | `TEXT`    | `NULLABLE` (Cifrado)                                      |
| `two_factor_confirmed_at` | `TIMESTAMP` | `NULLABLE`                                                |
| `remember_token`        | `VARCHAR(100)`| `NULLABLE`                                                |
| `current_team_id`       | `BIGINT(20) UNSIGNED` | `NULLABLE` (Relacionado con Jetstream Teams si se usa)    |
| `profile_photo_path`    | `VARCHAR(2048)`| `NULLABLE`                                               |
| `organization_id`       | `BIGINT(20) UNSIGNED` | `NULLABLE`, `FOREIGN KEY` (references `organizations.id`) |
| `area`                  | `VARCHAR(255)`| `NULLABLE` (Área/departamento del usuario)                |
| `created_at`            | `TIMESTAMP`   | `NULLABLE`                                                |
| `updated_at`            | `TIMESTAMP`   | `NULLABLE`                                                |

**Indexes:**
*   `users_email_unique`

---

## `password_reset_tokens` table
Almacena tokens para la funcionalidad de reseteo de contraseñas.

| Column Name | Data Type     | Constraints & Notes         |
|-------------|---------------|-----------------------------|
| `email`     | `VARCHAR(255)`| `PRIMARY KEY` (o parte de)  |
| `token`     | `VARCHAR(255)`| `NOT NULL`                  |
| `created_at`| `TIMESTAMP`   | `NULLABLE`                  |

**Indexes:**
*   `password_reset_tokens_email_index` (o primary key on email)

---

## `sessions` table
Almacena información de las sesiones de usuario activas.

| Column Name     | Data Type     | Constraints & Notes           |
|-----------------|---------------|-------------------------------|
| `id`            | `VARCHAR(255)`| `PRIMARY KEY`                 |
| `user_id`       | `BIGINT(20) UNSIGNED` | `NULLABLE`, `INDEX`   |
| `ip_address`    | `VARCHAR(45)` | `NULLABLE`                    |
| `user_agent`    | `TEXT`        | `NULLABLE`                    |
| `payload`       | `LONGTEXT`    | `NOT NULL`                    |
| `last_activity` | `INT`         | `NOT NULL`, `INDEX`           |

---

## `organizations` table
Almacena información sobre las organizaciones/entidades.

| Column Name     | Data Type     | Constraints & Notes         |
|-----------------|---------------|-----------------------------|
| `id`            | `BIGINT(20) UNSIGNED` | `AUTO_INCREMENT`, `PRIMARY KEY` |
| `name`          | `VARCHAR(255)`| `NOT NULL`, `UNIQUE`        |
| `location`      | `VARCHAR(255)`| `NULLABLE`                  |
| `business_model`| `VARCHAR(255)`| `NULLABLE`                  |
| `created_at`    | `TIMESTAMP`   | `NULLABLE`                  |
| `updated_at`    | `TIMESTAMP`   | `NULLABLE`                  |

---

## `projects` table
Almacena información sobre los proyectos.

| Column Name         | Data Type     | Constraints & Notes                                    |
|---------------------|---------------|--------------------------------------------------------|
| `id`                | `BIGINT(20) UNSIGNED` | `AUTO_INCREMENT`, `PRIMARY KEY`                        |
| `identifier`        | `VARCHAR(255)`| `NOT NULL`, `UNIQUE`                                   |
| `name`              | `VARCHAR(255)`| `NOT NULL`                                             |
| `general_objective` | `TEXT`        | `NULLABLE`                                             |
| `status`            | `VARCHAR(255)`| `NOT NULL`, `DEFAULT 'active'` (e.g., active, inactive)|
| `lider_id`          | `BIGINT(20) UNSIGNED` | `NULLABLE`, `FOREIGN KEY` (references `users.id`) (Nota: puede estar en desuso en favor de `project_user` para roles) |
| `organization_id`   | `BIGINT(20) UNSIGNED` | `NOT NULL`, `FOREIGN KEY` (references `organizations.id`)|
| `created_by`        | `BIGINT(20) UNSIGNED` | `NULLABLE`, `FOREIGN KEY` (references `users.id`)      |
| `created_at`        | `TIMESTAMP`   | `NULLABLE`                                             |
| `updated_at`        | `TIMESTAMP`   | `NULLABLE`                                             |

---

## Spatie Permission Tables
Tablas utilizadas por el paquete `spatie/laravel-permission` para gestionar roles y permisos.

### `permissions` table
| Column Name  | Data Type     | Constraints & Notes         |
|--------------|---------------|-----------------------------|
| `id`         | `BIGINT(20) UNSIGNED` | `AUTO_INCREMENT`, `PRIMARY KEY` |
| `name`       | `VARCHAR(255)`| `NOT NULL`, `UNIQUE`        |
| `guard_name` | `VARCHAR(255)`| `NOT NULL`, `INDEX`         |
| `created_at` | `TIMESTAMP`   | `NULLABLE`                  |
| `updated_at` | `TIMESTAMP`   | `NULLABLE`                  |

### `roles` table
| Column Name  | Data Type     | Constraints & Notes         |
|--------------|---------------|-----------------------------|
| `id`         | `BIGINT(20) UNSIGNED` | `AUTO_INCREMENT`, `PRIMARY KEY` |
| `name`       | `VARCHAR(255)`| `NOT NULL`, `UNIQUE`        |
| `guard_name` | `VARCHAR(255)`| `NOT NULL`, `INDEX`         |
| `created_at` | `TIMESTAMP`   | `NULLABLE`                  |
| `updated_at` | `TIMESTAMP`   | `NULLABLE`                  |

### `model_has_permissions` table
| Column Name     | Data Type     | Constraints & Notes         |
|-----------------|---------------|-----------------------------|
| `permission_id` | `BIGINT(20) UNSIGNED` | `PRIMARY KEY` (composite), `FOREIGN KEY` |
| `model_type`    | `VARCHAR(255)`| `PRIMARY KEY` (composite), `NOT NULL` |
| `model_id`      | `BIGINT(20) UNSIGNED` | `PRIMARY KEY` (composite), `NOT NULL` |

**Indexes:** `model_has_permissions_model_id_model_type_index`

### `model_has_roles` table
| Column Name  | Data Type     | Constraints & Notes         |
|--------------|---------------|-----------------------------|
| `role_id`    | `BIGINT(20) UNSIGNED` | `PRIMARY KEY` (composite), `FOREIGN KEY` |
| `model_type` | `VARCHAR(255)`| `PRIMARY KEY` (composite), `NOT NULL` |
| `model_id`   | `BIGINT(20) UNSIGNED` | `PRIMARY KEY` (composite), `NOT NULL` |

**Indexes:** `model_has_roles_model_id_model_type_index`

### `role_has_permissions` table
| Column Name     | Data Type     | Constraints & Notes         |
|-----------------|---------------|-----------------------------|
| `permission_id` | `BIGINT(20) UNSIGNED` | `PRIMARY KEY` (composite), `FOREIGN KEY` |
| `role_id`       | `BIGINT(20) UNSIGNED` | `PRIMARY KEY` (composite), `FOREIGN KEY` |

---

## `vulnerability_types` table
Catálogo de tipos de vulnerabilidades.

| Column Name | Data Type     | Constraints & Notes         |
|-------------|---------------|-----------------------------|
| `id`        | `BIGINT(20) UNSIGNED` | `AUTO_INCREMENT`, `PRIMARY KEY` |
| `name`      | `VARCHAR(255)`| `NOT NULL`, `UNIQUE`        |
| `created_at`| `TIMESTAMP`   | `NULLABLE`                  |
| `updated_at`| `TIMESTAMP`   | `NULLABLE`                  |

---

## `vulnerability_categories` table
Catálogo de categorías de vulnerabilidades.

| Column Name | Data Type     | Constraints & Notes         |
|-------------|---------------|-----------------------------|
| `id`        | `BIGINT(20) UNSIGNED` | `AUTO_INCREMENT`, `PRIMARY KEY` |
| `name`      | `VARCHAR(255)`| `NOT NULL`, `UNIQUE`        |
| `created_at`| `TIMESTAMP`   | `NULLABLE`                  |
| `updated_at`| `TIMESTAMP`   | `NULLABLE`                  |

---

## `vulnerability_statuses` table
Catálogo de estados posibles para una vulnerabilidad (usado para referencia, el estado se almacena en la tabla `vulnerabilities`).

| Column Name | Data Type     | Constraints & Notes         |
|-------------|---------------|-----------------------------|
| `id`        | `BIGINT(20) UNSIGNED` | `AUTO_INCREMENT`, `PRIMARY KEY` |
| `name`      | `VARCHAR(255)`| `NOT NULL`, `UNIQUE`        |
| `description`| `TEXT`       | `NULLABLE`                  |
| `created_at`| `TIMESTAMP`   | `NULLABLE`                  |
| `updated_at`| `TIMESTAMP`   | `NULLABLE`                  |

---

## `areas` table
(Definición mínima, según migraciones)

| Column Name | Data Type     | Constraints & Notes         |
|-------------|---------------|-----------------------------|
| `id`        | `BIGINT(20) UNSIGNED` | `AUTO_INCREMENT`, `PRIMARY KEY` |
| `name`      | `VARCHAR(255)`| `NOT NULL`                  |
| `created_at`| `TIMESTAMP`   | `NULLABLE`                  |
| `updated_at`| `TIMESTAMP`   | `NULLABLE`                  |

---

## `assignments` table
(Definición mínima, según migraciones)

| Column Name | Data Type     | Constraints & Notes         |
|-------------|---------------|-----------------------------|
| `id`        | `BIGINT(20) UNSIGNED` | `AUTO_INCREMENT`, `PRIMARY KEY` |
| `name`      | `VARCHAR(255)`| `NOT NULL`                  |
| `created_at`| `TIMESTAMP`   | `NULLABLE`                  |
| `updated_at`| `TIMESTAMP`   | `NULLABLE`                  |

---

## `equipment` table
(Definición mínima, según migraciones)

| Column Name | Data Type     | Constraints & Notes         |
|-------------|---------------|-----------------------------|
| `id`        | `BIGINT(20) UNSIGNED` | `AUTO_INCREMENT`, `PRIMARY KEY` |
| `name`      | `VARCHAR(255)`| `NOT NULL`                  |
| `created_at`| `TIMESTAMP`   | `NULLABLE`                  |
| `updated_at`| `TIMESTAMP`   | `NULLABLE`                  |

---

## `vulnerabilities` table
Almacena los detalles de cada vulnerabilidad reportada.

| Column Name             | Data Type     | Constraints & Notes                                    |
|-------------------------|---------------|--------------------------------------------------------|
| `id`                    | `BIGINT(20) UNSIGNED` | `AUTO_INCREMENT`, `PRIMARY KEY`                        |
| `title`                 | `TEXT`        | `NOT NULL` (Modificado para TEXT)                      |
| `detection_date`        | `DATE`        | `NULLABLE`                                             |
| `description`           | `TEXT`        | `NULLABLE` (Ya era TEXT)                               |
| `component`             | `TEXT`        | `NULLABLE` (Modificado para TEXT)                      |
| `type_id`               | `BIGINT(20) UNSIGNED` | `NULLABLE`, `FOREIGN KEY` (references `vulnerability_types.id`), `ON DELETE SET NULL` |
| `owasp_classification`  | `VARCHAR(255)`| `NULLABLE`                                             |
| `cvss_vector`           | `VARCHAR(255)`| `NULLABLE`                                             |
| `cvss_score`            | `DECIMAL(4,2)`| `NULLABLE`                                             |
| `severity_level`        | `VARCHAR(255)`| `NULLABLE`                                             |
| `exploit_probability`   | `VARCHAR(255)`| `NULLABLE`                                             |
| `estimated_impact`      | `VARCHAR(255)`| `NULLABLE`                                             |
| `state`                 | `ENUM(...)`   | `DEFAULT 'Detectada'`, Posibles: 'Detectada', 'Asignada', 'En tratamiento', 'Resuelta', 'Cerrada' |
| `detection_source`      | `VARCHAR(255)`| `NULLABLE`                                             |
| `documentation_url`     | `VARCHAR(255)`| `NULLABLE`                                             |
| `project_id`            | `BIGINT(20) UNSIGNED` | `NOT NULL`, `FOREIGN KEY` (references `projects.id`), `ON DELETE CASCADE` |
| `assigned_user_id`      | `BIGINT(20) UNSIGNED` | `NULLABLE`, `FOREIGN KEY` (references `users.id`), `ON DELETE SET NULL` |
| `category_id`           | `BIGINT(20) UNSIGNED` | `NULLABLE`, `FOREIGN KEY` (references `vulnerability_categories.id`), `ON DELETE SET NULL` |
| `resolution_deadline`   | `TIMESTAMP`   | `NULLABLE`                                             |
| `priority`              | `VARCHAR(255)`| `NULLABLE`                                             |
| `created_by`            | `BIGINT(20) UNSIGNED` | `NOT NULL`, `FOREIGN KEY` (references `users.id`), `ON DELETE CASCADE` |
| `created_at`            | `TIMESTAMP`   | `NULLABLE`                                             |
| `updated_at`            | `TIMESTAMP`   | `NULLABLE`                                             |

**Indexes:**
*   `vulnerabilities_state_index`
*   (Index on `title` was removed)

---

## `vulnerability_comments` table
Almacena comentarios y el historial de cambios de estado para las vulnerabilidades.

| Column Name     | Data Type     | Constraints & Notes                                    |
|-----------------|---------------|--------------------------------------------------------|
| `id`            | `BIGINT(20) UNSIGNED` | `AUTO_INCREMENT`, `PRIMARY KEY`                        |
| `vulnerability_id`| `BIGINT(20) UNSIGNED` | `NOT NULL`, `FOREIGN KEY` (references `vulnerabilities.id`), `ON DELETE CASCADE` |
| `user_id`       | `BIGINT(20) UNSIGNED` | `NOT NULL`, `FOREIGN KEY` (references `users.id`)      |
| `comment`       | `TEXT`        | `NOT NULL`                                             |
| `state_before`  | `VARCHAR(255)`| `NULLABLE` (Estado anterior al cambio)                   |
| `state_after`   | `VARCHAR(255)`| `NULLABLE` (Estado nuevo tras el cambio)                 |
| `created_at`    | `TIMESTAMP`   | `NULLABLE`                                             |
| `updated_at`    | `TIMESTAMP`   | `NULLABLE`                                             |

---

## `vulnerability_files` table
Almacena rutas a archivos adjuntos para las vulnerabilidades.

| Column Name     | Data Type     | Constraints & Notes                                    |
|-----------------|---------------|--------------------------------------------------------|
| `id`            | `BIGINT(20) UNSIGNED` | `AUTO_INCREMENT`, `PRIMARY KEY`                        |
| `vulnerability_id`| `BIGINT(20) UNSIGNED` | `NOT NULL`, `FOREIGN KEY` (references `vulnerabilities.id`), `ON DELETE CASCADE` |
| `uploaded_by`   | `BIGINT(20) UNSIGNED` | `NOT NULL`, `FOREIGN KEY` (references `users.id`)      |
| `path`          | `VARCHAR(255)`| `NOT NULL` (Ruta en el storage)                        |
| `original_name` | `VARCHAR(255)`| `NOT NULL` (Nombre original del archivo)               |
| `created_at`    | `TIMESTAMP`   | `NULLABLE`                                             |
| `updated_at`    | `TIMESTAMP`   | `NULLABLE`                                             |

---

## `vulnerability_user` table
Tabla pivote para la relación muchos-a-muchos entre vulnerabilidades y usuarios asignados.

| Column Name     | Data Type     | Constraints & Notes                                    |
|-----------------|---------------|--------------------------------------------------------|
| `id`            | `BIGINT(20) UNSIGNED` | `AUTO_INCREMENT`, `PRIMARY KEY`                        |
| `vulnerability_id`| `BIGINT(20) UNSIGNED` | `NOT NULL`, `FOREIGN KEY` (references `vulnerabilities.id`), `ON DELETE CASCADE` |
| `user_id`       | `BIGINT(20) UNSIGNED` | `NOT NULL`, `FOREIGN KEY` (references `users.id`), `ON DELETE CASCADE` |
| `created_at`    | `TIMESTAMP`   | `NULLABLE`                                             |
| `updated_at`    | `TIMESTAMP`   | `NULLABLE`                                             |

**Indexes:**
*   `vulnerability_user_vulnerability_id_user_id_unique` (UNIQUE composite)

---

## `project_user` table
Tabla pivote para la relación muchos-a-muchos entre proyectos y usuarios. Puede incluir un rol específico del usuario dentro del proyecto.

| Column Name  | Data Type     | Constraints & Notes                                    |
|--------------|---------------|--------------------------------------------------------|
| `id`         | `BIGINT(20) UNSIGNED` | `AUTO_INCREMENT`, `PRIMARY KEY`                        |
| `project_id` | `BIGINT(20) UNSIGNED` | `NOT NULL`, `FOREIGN KEY` (references `projects.id`), `ON DELETE CASCADE` |
| `user_id`    | `BIGINT(20) UNSIGNED` | `NOT NULL`, `FOREIGN KEY` (references `users.id`), `ON DELETE CASCADE` |
| `role`       | `VARCHAR(255)`| `NULLABLE` (Ej: 'leader', 'member', 'viewer')        |
| `created_at` | `TIMESTAMP`   | `NULLABLE`                                             |
| `updated_at` | `TIMESTAMP`   | `NULLABLE`                                             |

**Indexes:**
*   `project_user_project_id_user_id_unique` (UNIQUE composite)

---

## `tasks` table
Almacena información sobre las tareas asociadas a vulnerabilidades.

| Column Name      | Data Type     | Constraints & Notes                                    |
|------------------|---------------|--------------------------------------------------------|
| `id`             | `BIGINT(20) UNSIGNED` | `AUTO_INCREMENT`, `PRIMARY KEY`                        |
| `title`          | `VARCHAR(255)`| `NOT NULL`                                             |
| `description`    | `TEXT`        | `NULLABLE`                                             |
| `status`         | `VARCHAR(255)`| `NOT NULL`, `DEFAULT 'pendiente'` (e.g., pendiente, en_progreso, completada) |
| `priority`       | `ENUM(...)`   | `NOT NULL`, `DEFAULT 'media'`. Posibles: 'baja', 'media', 'alta', 'critica' (Actualizado) |
| `due_date`       | `DATE`        | `NULLABLE`                                             |
| `project_id`     | `BIGINT(20) UNSIGNED` | `NOT NULL`, `FOREIGN KEY` (references `projects.id`), `ON DELETE CASCADE` |
| `vulnerability_id`| `BIGINT(20) UNSIGNED` | `NOT NULL`, `FOREIGN KEY` (references `vulnerabilities.id`), `ON DELETE CASCADE` |
| `assigned_to`    | `BIGINT(20) UNSIGNED` | `NULLABLE`, `FOREIGN KEY` (references `users.id`), `ON DELETE SET NULL` |
| `created_by`     | `BIGINT(20) UNSIGNED` | `NOT NULL`, `FOREIGN KEY` (references `users.id`), `ON DELETE CASCADE` |
| `created_at`     | `TIMESTAMP`   | `NULLABLE`                                             |
| `updated_at`     | `TIMESTAMP`   | `NULLABLE`                                             |

---

## `audit_logs` table
Registra eventos de auditoría en el sistema.

| Column Name    | Data Type     | Constraints & Notes         |
|----------------|---------------|-----------------------------|
| `id`           | `BIGINT(20) UNSIGNED` | `AUTO_INCREMENT`, `PRIMARY KEY` |
| `user_id`      | `BIGINT(20) UNSIGNED` | `NULLABLE`, `FOREIGN KEY` (references `users.id`), `ON DELETE SET NULL` |
| `event`        | `VARCHAR(255)`| `NOT NULL`                  |
| `auditable_type`| `VARCHAR(255)`| `NOT NULL`                  |
| `auditable_id` | `BIGINT(20) UNSIGNED` | `NOT NULL`            |
| `old_values`   | `TEXT`        | `NULLABLE` (JSON)           |
| `new_values`   | `TEXT`        | `NULLABLE` (JSON)           |
| `url`          | `TEXT`        | `NULLABLE`                  |
| `ip_address`   | `VARCHAR(45)` | `NULLABLE`                  |
| `user_agent`   | `VARCHAR(1023)`| `NULLABLE`                 |
| `tags`         | `VARCHAR(255)`| `NULLABLE`                  |
| `created_at`   | `TIMESTAMP`   | `NULLABLE`                  |
| `updated_at`   | `TIMESTAMP`   | `NULLABLE`                  |

**Indexes:**
*   `audit_logs_auditable_type_auditable_id_index`
*   `audit_logs_user_id_index`

---
