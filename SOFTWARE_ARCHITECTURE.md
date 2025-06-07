# Documento de Arquitectura de Software

## 1. Introducción

Este documento describe la arquitectura del sistema de Gestión de Vulnerabilidades. El objetivo es proporcionar una visión general de la estructura del software, sus componentes principales, las tecnologías utilizadas y los principios de diseño que guían su desarrollo. Esta arquitectura está diseñada para ser robusta, mantenible y escalable, facilitando la adición de nuevas funcionalidades y la adaptación a futuros requerimientos.

## 2. Framework Principal y Filosofía de Diseño

### 2.1. Framework Principal: Laravel (PHP)
El sistema está construido sobre **Laravel**, un framework de aplicación web PHP con una sintaxis expresiva y elegante. Laravel proporciona una base sólida con características como:
-   Sistema de enrutamiento robusto.
-   ORM Eloquent para la interacción con la base de datos.
-   Motor de plantillas Blade.
-   Mecanismos para colas de trabajos, manejo de eventos, autenticación, autorización, etc.
-   Una amplia comunidad y ecosistema de paquetes.

### 2.2. Influencia de Domain-Driven Design (DDD)
Si bien no es una implementación DDD purista en todos sus aspectos, la arquitectura del sistema está fuertemente influenciada por los principios de Domain-Driven Design. Esto se refleja en la organización del código, buscando separar las preocupaciones y aislar la lógica de dominio.
-   **Énfasis en el Dominio:** La lógica de negocio principal relacionada con vulnerabilidades, proyectos, tareas, usuarios, etc., reside en el `Domain Layer`.
-   **Separación de Capas:** Se busca una clara distinción entre la presentación, la aplicación, el dominio y la infraestructura.
-   **Lenguaje Ubicuo (Intento):** Se intenta utilizar un lenguaje consistente en el código que refleje el dominio del problema.

## 3. Capas Arquitectónicas

El sistema sigue una arquitectura de capas, que ayuda a organizar el código y reducir el acoplamiento entre diferentes partes de la aplicación.

### 3.1. Presentation Layer (Capa de Presentación)
Responsable de manejar las interacciones con el usuario (HTTP requests, respuestas, vistas).
-   **Vistas (Blade Templates):** Ubicadas en `resources/views/`. Definen la interfaz de usuario.
-   **Controladores (Controllers):** Ubicados en `app/Http/Controllers/` y también dentro de subdirectorios del Dominio (ej. `app/Domain/Projects/Controllers/`). Orquestan las solicitudes, interactúan con la capa de aplicación/dominio y devuelven respuestas (generalmente vistas).
-   **ViewModels:** Ubicados en `app/Domain/*/ViewModels/`. Clases PHP simples que preparan y estructuran los datos específicamente para una vista, eliminando lógica de las plantillas Blade y de los controladores.
-   **FormRequests:** Ubicados en `app/Domain/*/Requests/` (o `app/Http/Requests/`). Manejan la validación de las solicitudes HTTP antes de que lleguen a los controladores.
-   **Middleware:** Ubicado en `app/Http/Middleware/`. Filtra las solicitudes HTTP (autenticación, autorización, etc.).

### 3.2. Application Layer (Capa de Aplicación)
Contiene la lógica de la aplicación que no es estrictamente lógica de dominio. Orquesta tareas y coordina los servicios de dominio y de infraestructura.
-   **Servicios de Aplicación (Application Services):** Podrían existir para casos de uso complejos que coordinan múltiples servicios de dominio o infraestructura. (Actualmente, gran parte de esta lógica reside en Controladores o Servicios de Dominio directamente).
-   **Jobs (Trabajos en Cola):** Ubicados en `app/Jobs/` o `app/Domain/*/Jobs/`. Para tareas que pueden ejecutarse de forma asíncrona (ej. importación masiva de vulnerabilidades, envío de notificaciones).
-   **Event Listeners:** Manejan eventos disparados por el sistema.

### 3.3. Domain Layer (Capa de Dominio)
El corazón de la aplicación, contiene la lógica de negocio y las reglas.
-   **Modelos Eloquent (Entidades y Value Objects):** Ubicados en `app/Models/` (para modelos genéricos como User) y principalmente en `app/Domain/*/Models/` (ej. `App\Domain\Projects\Models\Project`). Representan las entidades del dominio y encapsulan su estado y comportamiento.
-   **Servicios de Dominio (Domain Services):** Ubicados en `app/Domain/*/Services/` (ej. `App\Domain\Vulnerabilities\Services\VulnerabilityStateService`, `App\Domain\Dashboard\Services\*DashboardService`). Encapsulan lógica de negocio que no pertenece naturalmente a un único modelo.
-   **Policies (Políticas de Autorización):** Ubicadas en `app/Domain/*/Policies/`. Definen las reglas de autorización para las acciones sobre los modelos del dominio.
-   **Observers (Observadores de Modelos):** Ubicados en `app/Domain/*/Observers/`. Permiten reaccionar a eventos del ciclo de vida de los modelos Eloquent (ej. para auditoría).
-   **Excepciones de Dominio:** Excepciones personalizadas para señalar errores específicos del negocio.

### 3.4. Infrastructure Layer (Capa de Infraestructura)
Contiene el código que interactúa con elementos externos como bases de datos, sistemas de archivos, servicios de terceros, etc.
-   **Configuración de Laravel:** Archivos en `config/`.
-   **Migraciones de Base de Datos:** Ubicadas en `database/migrations/`. Definen el esquema de la base de datos.
-   **Seeders (Sembradores de Datos):** Ubicados en `database/seeders/`. Para poblar la base de datos con datos iniciales o de prueba.
-   **Implementaciones de Interfaces/Repositorios (si se usan explícitamente):** Código que interactúa directamente con la base de datos (Eloquent ORM actúa como una capa de abstracción aquí).
-   **Integraciones con APIs Externas:** (No implementado prominentemente en este proyecto hasta ahora).

## 4. Componentes Clave y sus Roles

-   **Controladores:** Reciben las solicitudes HTTP, validan la entrada (a menudo delegando a FormRequests), invocan la lógica de aplicación o dominio (servicios, modelos), y preparan la respuesta (generalmente renderizando una vista con un ViewModel).
-   **Modelos (Eloquent):** Representan las entidades de datos (ej. `User`, `Project`, `Vulnerability`, `Task`). Manejan la persistencia y las relaciones. Pueden contener lógica de negocio directamente relacionada con la entidad que representan.
-   **Servicios:** Encapsulan lógica de negocio o de aplicación específica. Los servicios de dominio contienen reglas de negocio puras, mientras que los servicios de aplicación pueden coordinar tareas o interactuar con la infraestructura.
-   **Vistas (Blade):** Plantillas HTML para la interfaz de usuario. Reciben datos de los Controladores (a menudo a través de ViewModels).
-   **ViewModels:** Clases simples responsables de tomar datos crudos del dominio o aplicación y formatearlos/estructurarlos para una vista específica. Ayudan a mantener los controladores y las vistas más limpios.
-   **FormRequests:** Clases dedicadas a la validación de las solicitudes HTTP. Centralizan las reglas de validación y la lógica de autorización para la solicitud.
-   **Policies:** Clases que organizan la lógica de autorización para acciones específicas sobre los modelos. Determinan si un usuario autenticado puede realizar una acción determinada.
-   **Middleware:** Clases que pueden inspeccionar y filtrar las solicitudes HTTP entrantes. Se usan para autenticación, CORS, logging, etc.
-   **Providers (Service Providers):** El lugar central para registrar servicios, bindings en el contenedor de servicios, listeners de eventos, etc.
-   **Observers:** Clases que escuchan eventos del ciclo de vida de los modelos Eloquent (creating, created, updating, updated, deleting, deleted, etc.) para ejecutar lógica secundaria (ej. logging de auditoría).
-   **Jobs:** Clases que representan tareas que deben ejecutarse de forma asíncrona en una cola (ej. importación masiva de datos).

## 5. Ciclo de Vida de una Solicitud / Patrones de Interacción Comunes

Un flujo típico de solicitud podría ser:
1.  El usuario interactúa con la interfaz (Vista).
2.  Se envía una solicitud HTTP al servidor.
3.  El Enrutador de Laravel dirige la solicitud al Controlador apropiado.
4.  El Middleware se ejecuta (autenticación, etc.).
5.  Si es una solicitud de modificación de datos (POST, PUT, PATCH), un FormRequest valida los datos. Si falla, se devuelve una respuesta de error.
6.  El Controlador recibe la solicitud validada.
7.  El Controlador invoca una Política para autorizar la acción. Si falla, se devuelve una respuesta de error (ej. 403).
8.  El Controlador llama a un Servicio de Aplicación/Dominio o interactúa directamente con un Modelo para ejecutar la lógica de negocio.
9.  Los Modelos pueden disparar eventos que son escuchados por Observadores (ej. para auditoría).
10. El Servicio/Modelo devuelve datos al Controlador.
11. El Controlador instancia un ViewModel con los datos necesarios para la vista.
12. El Controlador renderiza una Vista Blade, pasándole el ViewModel.
13. La Vista Blade genera el HTML.
14. Se envía la respuesta HTTP al cliente.

Para tareas asíncronas:
1.  Una acción en un Controlador o Servicio puede despachar un Job a la cola.
2.  Un proceso worker separado toma el Job de la cola y ejecuta su método `handle()`.
3.  El Job realiza la tarea (ej. procesar un archivo, enviar un email).

## 6. Estructura de Directorios (Resumen Relevante)

```
app/
├── Domain/                 # Contiene la lógica de negocio principal, organizada por dominios
│   ├── Dashboard/
│   │   ├── Services/       # Servicios para la lógica del dashboard (Admin, Lider, Miembro)
│   ├── Organizations/
│   │   ├── Controllers/
│   │   ├── Models/
│   │   └── ...
│   ├── Projects/
│   │   ├── Controllers/
│   │   ├── Models/
│   │   ├── Policies/
│   │   └── ViewModels/
│   ├── Tasks/
│   │   ├── Controllers/
│   │   ├── Models/
│   │   ├── Policies/
│   │   └── ViewModels/
│   ├── Vulnerabilities/
│   │   ├── Controllers/
│   │   ├── Models/
│   │   ├── Policies/
│   │   ├── Requests/
│   │   ├── Services/       # Ej. VulnerabilityStateService
│   │   └── ViewModels/
│   └── ...                 # Otros dominios (Auth, Users si se mueven aquí)
├── Http/
│   ├── Controllers/        # Controladores generales (ej. DashboardController)
│   ├── Middleware/
│   └── Requests/           # FormRequests generales (si no están en Dominio)
├── Jobs/                   # Trabajos en cola (pueden estar también en Dominio/*_Jobs/)
├── Models/                 # Modelos Eloquent generales (ej. User, si no está en Dominio/Users)
├── Observers/              # Observadores de modelos (pueden estar también en Dominio/*/Observers)
├── Policies/               # Políticas generales (si no están en Dominio)
├── Providers/              # Service Providers (AuthServiceProvider, EventServiceProvider, etc.)
└── ...
config/                     # Archivos de configuración de la aplicación
database/
├── factories/
├── migrations/
└── seeders/                # Seeders de base de datos
resources/
├── css/
├── js/
└── views/
    ├── dashboard/
    │   └── partials/       # Vistas parciales para los dashboards por rol
    ├── layouts/            # Plantillas base (app.blade.php)
    ├── organizations/
    ├── projects/
    ├── tasks/
    ├── vulnerabilities/
    └── ...                 # Otras vistas
routes/
├── api.php
└── web.php                 # Definiciones de rutas web
tests/
├── Feature/                # Pruebas de Feature
└── Unit/                   # Pruebas Unitarias
```

## 7. Tecnologías y Librerías Clave

-   **PHP:** Lenguaje de programación principal.
-   **Laravel:** Framework PHP.
-   **MySQL (o compatible):** Sistema de gestión de base de datos.
-   **Eloquent ORM:** Para la interacción con la base de datos.
-   **Blade Templating Engine:** Para las vistas.
-   **Laravel Fortify & Jetstream (o Breeze):** Para autenticación y scaffolding de UI (Jetstream usado aquí).
-   **Spatie Laravel Permission:** Para la gestión de roles y permisos.
-   **Tailwind CSS:** Framework CSS para el diseño de la interfaz (a través de Jetstream).
-   **Alpine.js:** Framework JavaScript ligero (a través de Jetstream).
-   **Livewire (opcional, si se usa):** Para componentes dinámicos en el frontend. (No prominentemente usado en este resumen).
-   **Maatwebsite/Laravel-Excel:** Para importación/exportación de Excel.
-   **Barryvdh/laravel-dompdf:** Para generación de PDF.
-   **Laravel Queues (con Redis/Database driver):** Para procesamiento de tareas en segundo plano.
-   **PHPUnit:** Para pruebas unitarias y de feature.
-   **Composer:** Manejador de dependencias PHP.
-   **NPM/Yarn & Vite/Webpack:** Para la gestión de assets frontend y compilación.

## 8. Real-time Vulnerability Import Progress

### 8.1. Overview
To provide users with immediate feedback during bulk vulnerability imports, the system utilizes Laravel Reverb for WebSocket communication, coupled with Laravel Queues (driven by Redis) for background job processing. This allows the frontend to display real-time progress updates as import files are processed.

### 8.2. Broadcasting Events
A series of events are dispatched during the import lifecycle. These events are defined in `app/Events/` and implement `ShouldBroadcastNow` for immediate broadcasting. They are broadcast on a private channel specific to each import batch: `import-batch.{batchId}`.

-   **`VulnerabilityImportStarted`**:
    -   **Purpose**: Signals the beginning of an import batch processing.
    -   **Payload**: `batchId` (string), `totalRows` (int - number of data rows to process).
-   **`VulnerabilityImportProgress`**:
    -   **Purpose**: Provides updates on the number of rows processed.
    -   **Payload**: `batchId` (string), `processedRows` (int).
-   **`VulnerabilityImportFailedRow`**:
    -   **Purpose**: Indicates a specific row failed validation or processing.
    -   **Payload**: `batchId` (string), `rowNumber` (int - actual row number in the file), `errors` (array of error messages for the row).
-   **`VulnerabilityImportCompleted`**:
    -   **Purpose**: Signals the completion of an import batch.
    -   **Payload**: `batchId` (string), `status` (string - e.g., 'completed_successfully', 'completed_with_errors', 'failed'), `importedCount` (int), `failedCount` (int), `message` (string - summary message).

### 8.3. Backend Implementation

-   **Event Dispatching**:
    -   The `app/Imports/VulnerabilityImport.php` class is responsible for row-by-row processing within the import job. It dispatches:
        -   `VulnerabilityImportStarted` once, after initial setup and row counting.
        -   `VulnerabilityImportProgress` after each row (or a chunk of rows) is attempted.
        -   `VulnerabilityImportFailedRow` if a row encounters validation or processing errors.
        -   It also updates `successful_rows` and `failed_rows` counts directly on the `ImportBatch` model.
    -   The `app/Jobs/ProcessVulnerabilityImportJob.php` class (which uses `VulnerabilityImport`) dispatches the final `VulnerabilityImportCompleted` event from its `handle()` method's `finally` block (for normal completion) or from its `failed()` method (for catastrophic job failures). This event uses the final counts and status from the `ImportBatch` model.
-   **Channel Authorization**:
    -   Authorization for the private channel `import-batch.{batchId}` is defined in `routes/channels.php`.
    -   The logic typically allows the user who initiated the import batch (`ImportBatch->user_id`) or any user with an 'admin' role to listen to the channel.
-   **Queue Configuration**:
    -   The `.env` file is configured with `QUEUE_CONNECTION=redis` to ensure import jobs are processed via Redis.
    -   Laravel Reverb itself uses Redis for some of its internal operations if configured to do so, but its primary role here is as the WebSocket server.

### 8.4. Frontend Implementation

-   **Echo & Reverb Setup**:
    -   Laravel Echo is configured in `resources/js/bootstrap.js`.
    -   It's set up to use `reverb` as the broadcaster, with connection details (app key, host, port, scheme) sourced from Vite environment variables (e.g., `import.meta.env.VITE_REVERB_APP_KEY`), which are in turn populated from the `.env` file.
-   **Listening for Events**:
    -   The `resources/views/vulnerabilities/imports/index.blade.php` view contains JavaScript that:
        -   Iterates through the displayed import batches.
        -   For batches in a "processing" or "pending" state, it uses Laravel Echo to subscribe to their specific `import-batch.{batchId}` private channel.
        -   It listens for the `.import.started`, `.import.progress`, `.import.rowFailed`, and `.import.completed` events (note the dot prefix for event names when listening).
        -   Upon receiving these events, the JavaScript dynamically updates the DOM to reflect progress (e.g., updating progress bars, status messages, displaying row-specific errors, and updating final counts in the table).
        -   Once an `import.completed` event is received, Echo disconnects from that specific batch channel.

### 8.5. Runtime Components
For the real-time import progress feature to function correctly, the following services must be running:

-   **Queue Worker**: `php artisan queue:work` (configured to use the Redis driver). This process picks up and executes the `ProcessVulnerabilityImportJob`.
-   **Reverb Server**: `php artisan reverb:start`. This starts the WebSocket server that handles broadcasting.
-   **Redis Server**: The Redis service itself must be running, as it's used for both Laravel Queues and potentially by Reverb.

---
Este documento proporciona una instantánea de la arquitectura. Puede evolucionar a medida que el sistema crece y se adapta a nuevos requerimientos.
