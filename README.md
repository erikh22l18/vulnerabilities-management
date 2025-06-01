# Project Vulnerability Management

## Project Overview

This project is a comprehensive web application designed for efficient management of security vulnerabilities within multiple organizations and their associated projects. It provides a centralized platform for identifying, tracking, assigning, resolving, and reporting on vulnerabilities throughout their lifecycle. The system aims to streamline security workflows and improve overall cybersecurity posture.

## Features

*   **Vulnerability Management:**
    *   Manual registration of vulnerabilities with detailed information.
    *   Bulk import of vulnerabilities via Excel, including robust validation of headers and row data.
    *   Lifecycle tracking with states: 'Detectada', 'En tratamiento', 'Resuelta', 'Cerrada'.
    *   Detailed information capture: CVSS score & vector, OWASP classification, severity, priority, exploit probability, estimated impact, etc.
    *   Commenting system for collaboration on vulnerabilities.
    *   File attachments for providing evidence or related documentation.
*   **User Management:**
    *   User registration with fields for identification, area, and organization assignment.
    *   Role-Based Access Control (RBAC) with predefined roles (e.g., Líder de proyecto, Miembro de proyecto, Admin).
    *   Integration with Laravel Fortify for authentication features, including Two-Factor Authentication (2FA).
*   **Organization & Project Management:**
    *   CRUD operations for organizations (attributes: name, location, business model).
    *   CRUD operations for projects (attributes: identifier, name, general objective, organization association).
    *   Assignment of users to specific projects with defined roles within the project.
*   **Task Management:**
    *   Creation and assignment of tasks linked directly to vulnerabilities to facilitate remediation efforts.
*   **Reporting:**
    *   Generation of PDF reports for individual vulnerabilities, providing a shareable summary.
    *   Project-level PDF reports summarizing all associated vulnerabilities.
*   **Dashboard:**
    *   Visual metrics including projects per organization.
    *   Vulnerability statistics per project: total count and percentage of treated vulnerabilities.
*   **Auditing:**
    *   Detailed audit logs tracking changes made to vulnerabilities, including state transitions and field modifications.

## Tech Stack

*   **Backend:** PHP / Laravel Framework
    *   Authentication: Laravel Jetstream (with Fortify)
    *   Authorization: Spatie/Laravel-Permission for roles and permissions
*   **Database:** SQL-based (e.g., MySQL, PostgreSQL). Designed with standard SQL practices.
*   **Frontend:**
    *   Templating: Blade
    *   Styling: Tailwind CSS
    *   JavaScript: Alpine.js (via Jetstream)
*   **Development & Build Tools:**
    *   Dependency Management (PHP): Composer
    *   Asset Compilation: Node.js with Vite
*   **Background Processing:**
    *   Queues for handling time-consuming tasks like Excel imports.

## Getting Started

### Prerequisites

*   PHP (version as per `composer.json`, e.g., ^8.1)
*   Composer
*   Node.js & npm (or yarn)
*   A compatible SQL database server (e.g., MySQL, PostgreSQL)

### Setup Instructions

1.  **Clone the repository:**
    ```bash
    git clone <repository-url>
    cd <project-directory>
    ```

2.  **Install PHP dependencies:**
    ```bash
    composer install
    ```

3.  **Environment Configuration:**
    *   Copy the example environment file:
        ```bash
        cp .env.example .env
        ```
    *   Generate an application key:
        ```bash
        php artisan key:generate
        ```
    *   Configure your `.env` file with the following:
        *   `APP_NAME`: Your application's name.
        *   `APP_URL`: The base URL for your application (e.g., `http://localhost:8000`).
        *   `DB_CONNECTION`: Database type (e.g., `mysql`).
        *   `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`: Your database credentials.
        *   `MAIL_MAILER`, `MAIL_HOST`, etc.: Mail server details for email functionalities.
        *   `QUEUE_CONNECTION`: Set to `database` or `redis` for background jobs (default `sync` is not recommended for production).

4.  **Database Migration and Seeding:**
    *   Run database migrations:
        ```bash
        php artisan migrate
        ```
    *   Seed the database with initial data (roles, permissions, admin user, etc.):
        ```bash
        php artisan db:seed
        ```
        (Note: Specific seeders like `RolePermissionSeeder` will be run if included in `DatabaseSeeder.php`)

5.  **Install frontend dependencies:**
    ```bash
    npm install
    # or
    yarn install
    ```

6.  **Compile frontend assets:**
    *   For development (with hot reloading):
        ```bash
        npm run dev
        # or
        yarn dev
        ```
    *   For production:
        ```bash
        npm run build
        # or
        yarn build
        ```

7.  **Queue Worker:**
    *   For development, you can run the queue worker directly:
        ```bash
        php artisan queue:work
        ```
    *   For production, configure a process manager like Supervisor to keep `php artisan queue:work` running. Refer to Laravel documentation for details.

8.  **Web Server Configuration:**
    *   Configure your web server (e.g., Nginx, Apache) to point its document root to the project's `public/` directory.
    *   Ensure URL rewriting is enabled (e.g., `public/.htaccess` for Apache, or corresponding Nginx configuration).
    *   Example Nginx site configuration snippet:
        ```nginx
        server {
            listen 80;
            server_name yourdomain.com;
            root /path/to/your/project/public;

            add_header X-Frame-Options "SAMEORIGIN";
            add_header X-XSS-Protection "1; mode=block";
            add_header X-Content-Type-Options "nosniff";

            index index.php;

            charset utf-8;

            location / {
                try_files $uri $uri/ /index.php?$query_string;
            }

            location = /favicon.ico { access_log off; log_not_found off; }
            location = /robots.txt  { access_log off; log_not_found off; }

            error_page 404 /index.php;

            location ~ \.php$ {
                fastcgi_pass unix:/var/run/php/php8.x-fpm.sock; // Adjust PHP version
                fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
                include fastcgi_params;
            }

            location ~ /\.(?!well-known).* {
                deny all;
            }
        }
        ```

## User Roles

The application defines several user roles with distinct responsibilities:

*   **Admin:** (Assumed to be created by seeders, e.g., via `RolePermissionSeeder`) Has full access to the system, including user management, organization setup, and system-wide configurations.
*   **Líder de proyecto (Project Leader):** Responsible for managing specific projects, assigning vulnerabilities, overseeing tasks, and potentially managing project members. Can typically create, update, and delete vulnerabilities within their projects.
*   **Miembro de proyecto (Project Member):** Can view and update vulnerabilities and tasks assigned to them within projects they are part of. Their creation and deletion capabilities might be restricted based on project policies.

## Key Functionalities

### Excel Vulnerability Import
The system supports bulk importing of vulnerabilities using a predefined Excel template. This feature includes validation of headers and row data, and processes valid files in the background using queues.
For detailed instructions, please refer to the [Excel Vulnerability Import Guide](./docs/excel_import_guide.md).

## Contributing

Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change. Please make sure to update tests as appropriate.

Alternatively, please contact the project maintainers for detailed contribution guidelines.

## License

This project is licensed under the MIT License.
