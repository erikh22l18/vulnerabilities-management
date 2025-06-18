# Resumen de Requisitos Implementados

## 1. Requerimientos Funcionales

### RQF1.1 - Carga Masiva de Vulnerabilidades (Importación Excel)
- **Descripción General:** El sistema permitirá la carga masiva de vulnerabilidades mediante un archivo Excel (XLSX). Este proceso se realizará de forma asíncrona para no afectar el rendimiento de la aplicación y proporcionará retroalimentación detallada al usuario sobre el resultado de la importación.
- **Referencia ISO 27001:** A.12.5.1, A.12.6.1, A.14.2.1, A.18.1.3; ISO/IEC 27034-1 (Application Security - Secure data input)
- **Estado:** IMPLEMENTADO. La lógica de importación en `VulnerabilityImport` ha sido refactorizada para mejorar claridad y mantenibilidad. Los usuarios con rol 'Líder' también pueden importar vulnerabilidades.

#### RQF1.1.1 - Formato y Plantilla del Archivo
- **Descripción:** Se definirá una plantilla Excel (`plantilla_vulnerabilidades.xlsx`) que los usuarios deberán utilizar para la carga masiva. La plantilla contendrá columnas predefinidas correspondientes a los campos de una vulnerabilidad.
- **Especificaciones:**
    - La plantilla estará disponible para descarga desde la aplicación.
    - Incluirá todas las columnas necesarias mapeadas a los campos del modelo `Vulnerability` (ej. Título, Descripción, Proyecto Asociado, Tipo, Estado, Severidad, CVSS, Responsable, etc.).
    - Columnas con valores predefinidos (ej. Estado, Severidad, Prioridad) deben indicar los valores permitidos (posiblemente en una hoja de ayuda o comentarios de celda).
    - Campos de fecha deben especificar el formato esperado (ej. YYYY-MM-DD).
    - Proyecto y Usuario Responsable se identificarán por nombres/correos únicos que deben existir en el sistema.
- **Estado:** IMPLEMENTADO (Plantilla definida y descargable).

#### RQF1.1.2 - Proceso de Carga Asíncrona
- **Descripción:** La carga del archivo Excel y su procesamiento se realizarán en segundo plano utilizando colas de trabajos (Laravel Queues).
- **Especificaciones:**
    - El usuario sube el archivo a través de una interfaz dedicada.
    - El archivo se almacena temporalmente y se despacha un job (`ProcessVulnerabilityImportJob`) a la cola.
    - El job procesa cada fila del archivo para crear o actualizar registros de vulnerabilidades.
    - El usuario es notificado al inicio y finalización del proceso.
- **Estado:** IMPLEMENTADO.

#### RQF1.1.3 - Validación de Cabeceras y Datos
- **Descripción:** Antes del procesamiento en segundo plano, se realizarán validaciones iniciales del archivo. Durante el procesamiento del job, cada fila será validada.
- **Especificaciones:**
    - **Validación de Cabeceras (Sincrónica):** Al subir el archivo, el sistema verifica que las cabeceras coincidan con la plantilla esperada. Si no coinciden, se informa al usuario inmediatamente y no se encola el job.
    - **Validación de Filas (Asíncrona, en el Job):**
        - Cada fila es validada contra las reglas definidas para los campos de vulnerabilidad (ej. tipo de dato, longitud, valores permitidos para enums, existencia de entidades relacionadas como Proyecto, Usuario, Tipo de Vulnerabilidad).
        - Se valida que el Proyecto y Usuario Responsable (si se proveen) existan en el sistema.
        - Se valida que el usuario asignado como responsable pertenezca a la misma organización que el proyecto de la vulnerabilidad.
    - **Manejo de Duplicados:** (Ver RQF1.1.5)
- **Estado:** IMPLEMENTADO (Validación de cabeceras y validación de filas básicas en `VulnerabilityImport`). Lógica de validación de pertenencia a organización para usuario responsable robustecida en `VulnerabilityImport`.
- **Referencia ISO 27001:** (Existente); ISO/IEC 27034-1 (Application Security - Input validation)

#### RQF1.1.4 - Reporte Detallado de Errores y Tracking de Lotes (Batch)
- **Descripción:** Se proporciona un informe detallado de errores y se rastrea cada intento de importación.
- **Especificaciones:**
    - Cada importación se registra en `import_batches` (ID ULID, usuario, archivo, estado, conteos, timestamps, resumen de error).
    - Errores a nivel de fila se registran en `import_row_errors` (ID ULID, batch_id, #fila, mensajes de error JSON, datos de fila JSON).
    - Notificación al usuario al finalizar (éxito, error parcial, fallo total) con resumen y enlace a detalles si hay errores.
    - Interfaz para ver historial de importaciones (`vulnerabilities.imports.index`) y detalles de errores por lote (`vulnerabilities.imports.errors`).
    - Administradores ven todos los lotes; otros usuarios solo los suyos.
    - **Actualizaciones de Progreso en Tiempo Real:** Durante el procesamiento de un lote de importación, la página de historial de importaciones (`vulnerabilities.imports.index`) mostrará actualizaciones en tiempo real del progreso. Esto incluye:
        - Una barra de progreso visual que indica el porcentaje de filas procesadas.
        - Mensajes de estado actualizados (ej. "Procesando X de Y filas").
        - Visualización inmediata de errores a nivel de fila si ocurren durante la importación.
        - Estado final del lote una vez completado (ej. "Completado Exitosamente", "Completado con Errores", "Fallido").
    - **Estado:** IMPLEMENTADO
- **Estado:** IMPLEMENTADO.

#### RQF1.1.5 - Manejo de Duplicados y Actualizaciones
- **Descripción:** La estrategia actual es actualizar el registro existente si se encuentra por **título, componente y proyecto**. Se han implementado opciones configurables (por lote de importación) para Omitir o Marcar como Error las filas duplicadas.
- **Especificaciones:**
    - **Opción 1 (Actualizar):** Si se encuentra una vulnerabilidad duplicada (basado en campos clave), se actualizan sus datos con la información del Excel. Se registra la acción. (IMPLEMENTADO por defecto en `VulnerabilityImport`)
    - **Opción 2 (Omitir):** Si se encuentra un duplicado, se omite la fila del Excel y se registra. (IMPLEMENTADO)
    - **Opción 3 (Marcar como Error):** Si se encuentra un duplicado, se marca como un error para esa fila. (IMPLEMENTADO)
- **Estado:** IMPLEMENTADO (Identificación de duplicados por título, componente y proyecto. Estrategias 'Actualizar', 'Omitir', 'Error' implementadas en la lógica de importación. Requiere UI para selección de estrategia y migración para `import_batches` para almacenar la estrategia y conteo de omitidos).
- **Referencia ISO 27001:** (Existente); ISO/IEC 25010 (Software Quality - Functional Suitability - Functional Correctness)

#### RQF1.1.6 - Asignación de Campos y Valores por Defecto
- **Descripción:** Ciertos campos pueden tener valores por defecto o ser derivados si no se proveen en el Excel.
- **Especificaciones:**
    - `created_by`: Usuario que sube el archivo. (IMPLEMENTADO)
    - `project_id`: Obtenido del nombre del proyecto en el Excel. (IMPLEMENTADO)
    - `assigned_user_id` (Responsable): Obtenido del email/nombre del usuario en el Excel. (IMPLEMENTADO)
    - `state`: Puede tener un valor por defecto como 'Detectada' si no se especifica y es válido. (IMPLEMENTADO, por defecto en modelo/DB)
- **Estado:** IMPLEMENTADO.

#### RQF1.1.7 - Proceso de Importación Asistida por Pasos (Multi-Step Form)
- **Descripción General:** Para mejorar la experiencia de usuario y la fiabilidad de la carga masiva, el sistema ofrece una interfaz de importación asistida por pasos. Este proceso guía al usuario a través de validaciones progresivas antes de encolar el archivo para su procesamiento final en segundo plano.
- **Referencia ISO 27001:** A.12.5.1, A.14.2.1
- **Estado:** IMPLEMENTADO

##### RQF1.1.7.1 - Flujo del Proceso Multi-Pasos
- **Descripción:** El usuario interactúa con un formulario que se divide en varias etapas:
    1.  **Paso 1: Carga de Archivo:**
        *   El usuario selecciona o arrastra su archivo Excel (`.xlsx`, `.xls`).
        *   El sistema realiza una validación inicial del tipo y tamaño del archivo.
        *   El archivo se carga y se almacena temporalmente en el servidor.
    2.  **Paso 2: Validación de Cabeceras:**
        *   El sistema extrae y muestra las cabeceras del archivo cargado.
        *   Se realiza una validación automática de las cabeceras contra la plantilla esperada.
        *   El usuario confirma las cabeceras para proceder. Si hay errores, se informa al usuario, quien puede optar por cancelar y corregir su archivo.
    3.  **Paso 3: Validación de Datos y Confirmación Final:**
        *   El usuario puede solicitar una validación de todas las filas de datos del archivo.
        *   El sistema procesa el archivo temporal y ejecuta las validaciones de datos (similares a las descritas en RQF1.1.3 para el job asíncrono, como existencia de proyectos, tipos de vulnerabilidad, usuarios, formatos de fecha, valores permitidos para estados, etc.).
        *   Se presenta un resumen de los errores encontrados (si los hay) a nivel de fila, o un mensaje de éxito si los datos son válidos.
        *   Si la validación de datos es corporativa (o si el usuario decide proceder a pesar de advertencias, según se defina la política), el usuario puede presionar el botón final "Importar".
- **Estado:** IMPLEMENTADO

##### RQF1.1.7.2 - Enlace con Procesamiento Asíncrono
- **Descripción:** Una vez que el usuario completa el último paso y confirma la importación:
    *   El archivo validado (almacenado temporalmente) se envía al job `ProcessVulnerabilityImportJob` para su procesamiento en segundo plano, tal como se describe en RQF1.1.2.
    *   El archivo temporal se elimina del servidor.
    *   El usuario es redirigido al historial de lotes de importación (`vulnerabilities.imports.index`) donde puede seguir el progreso del procesamiento asíncrono.
- **Estado:** IMPLEMENTADO

##### RQF1.1.7.3 - Gestión de Archivos Temporales
- **Descripción:** Los archivos cargados durante el proceso multi-pasos se almacenan temporalmente.
    *   Se eliminan automáticamente después de que el job de importación es despachado.
    *   Se planea implementar una tarea programada (Artisan command) para limpiar archivos temporales huérfanos que puedan quedar si el usuario abandona el proceso a mitad de camino.
- **Estado:** IMPLEMENTADO (Eliminación post-despacho implementada. Comando Artisan 'import:cleanup-orphaned-files' creado para limpieza de huérfanos).
- **Referencia ISO 27001:** (Existente); ISO 27001 (A.12.3.1 Information backup - though this is more about preventing data loss from orphaned files than backup)

### RQF2 - Gestión de usuarios, proyectos y organizaciones
- **Registro de usuarios con campos como nombre, correo, área, organización y contraseña:** IMPLEMENTADO (campo 'área' añadido).
- **Asignación de roles y permisos según estructura organizacional:** IMPLEMENTADO (roles Admin, Líder, Miembro con permisos definidos en `RolePermissionSeeder`). Lógica de Policies para restricciones finas del rol 'miembro' implementada y permisos de 'crear/editar tareas' actualizados en RolePermissionSeeder.
- **Referencia ISO 27001 (RBAC):** (Existente); ISO/IEC 25010 (Software Quality - Security - Access control)
- **Registro de proyectos con identificación, nombre y organización asociada:** IMPLEMENTADO.
- **Registro de organizaciones con nombre, ubicación y modelo de negocio:** IMPLEMENTADO.

### RQF3 - Asignación de tareas para tratamiento de vulnerabilidades
- **Las vulnerabilidades pueden asignarse a uno o varios usuarios de una misma organización:** IMPLEMENTADO (restringido a usuarios de la misma organización del proyecto).
- **Un usuario puede participar en varios proyectos dentro de su organización:** IMPLEMENTADO (verificado).

### RQF4 - Generación de informes por proyecto
- **Descarga de informes PDF con seguimiento y estado de tratamiento de vulnerabilidades:** IMPLEMENTADO (PDF por vulnerabilidad individual mejorado, PDF consolidado por proyecto implementado).
- **Informes generados automáticamente bajo demanda del usuario:** IMPLEMENTADO.

### RQF5 - Dashboard con métricas de seguridad
- **Visualización del número de proyectos por organización:** IMPLEMENTADO.
- **Visualización del total de vulnerabilidades por proyecto y su porcentaje de tratamiento:** IMPLEMENTADO (cálculo de % de tratamiento corregido para usar estados 'Resuelta'/'Cerrada').

### RQF5-A - Adecuación del Dashboard por Perfil
**Nombre:** Adecuación del Dashboard por Perfil
**Descripción:**
El sistema debe adaptar dinámicamente la visualización del dashboard según el rol del usuario autenticado (Admin, Líder de Proyecto, Miembro de Proyecto), mostrando únicamente la información relevante, autorizada y necesaria para su función.

**Especificaciones:**
- **Admin:**
    - Accede a todas las métricas globales y detalladas por organización y proyecto.
    - Visualiza indicadores de rendimiento por usuario (global).
    - Puede filtrar por cualquier organización, proyecto, usuario y rango de tiempo.
    - Acceso a alertas globales (riesgos críticos, retrasos, reabrimientos, etc.).
- **Líder de Proyecto:**
    - Visualiza métricas globales y detalladas para los proyectos y organizaciones a los que está asociado.
    - Visualiza indicadores de rendimiento por usuario dentro de su ámbito de proyectos/organización.
    - Puede filtrar por sus organizaciones, proyectos y rango de tiempo.
    - Acceso a alertas relevantes para sus proyectos/organización.
- **Miembro de Proyecto:**
    - Visualiza únicamente los proyectos donde está asignado.
    *   Visualiza sus tareas asignadas y su avance.
    *   Visualiza vulnerabilidades asignadas y su estado.
    *   No tiene acceso a métricas organizacionales ni datos de otros usuarios (excepto información compartida en tareas/vulnerabilidades).
    *   Puede recibir alertas personales (vulnerabilidad por vencer, nuevas asignaciones).
- El contenido del dashboard estará filtrado por el perfil y los permisos del usuario.
- Los datos sensibles o estratégicos se ocultarán para roles que no tengan privilegios de acceso.
- El diseño del dashboard será responsive y modular para facilitar la personalización por tipo de usuario.
- Se incluirá lógica de control de acceso en el backend para garantizar la separación de visibilidad.
- **Estado:** IMPLEMENTADO (Estructura base con servicios y vistas parciales por rol; métricas iniciales y placeholders para alertas).

### RQF6 - Control de estados de una vulnerabilidad
- **Flujo de estados:** `Detectada` -> `En tratamiento` -> `Resuelta` -> `Cerrada` (estado "Asignada" eliminado del flujo principal según feedback). IMPLEMENTADO.
- **No se puede modificar una vez cerrada (con matices):** IMPLEMENTADO (modificación de campos principales impedida; cambio de estado a reapertura requiere justificación). Política `VulnerabilityPolicy::changeStatus` ajustada para permitir que `VulnerabilityStateService` gestione la reapertura de vulnerabilidades cerradas (con justificación).
- **Se requiere justificación textual para cambios de estado:** IMPLEMENTADO (guardada en `VulnerabilityComment` con estado anterior/posterior).
- **Registro del autor y resumen de cada acción:** IMPLEMENTADO (autor en `VulnerabilityComment`, detalles en `AuditLog`).
- **Lógica de transición en servicio dedicado:** IMPLEMENTADO (`VulnerabilityStateService`).
- **Referencia ISO 27001:** (Existente); ISO/IEC 12207 (Software Lifecycle Processes - Maintenance Process); ISO/IEC 25010 (Software Quality - Reliability - Maturity)

### RQF7 - Historial de cambios y auditoría
- **Registro de cada acción sobre una vulnerabilidad:** IMPLEMENTADO (usando `AuditLog` con `VulnerabilityObserver` para creación, actualización de campos principales, y eliminación. Eventos de pivote para asignación/desasignación de usuarios. Cambios de estado en `VulnerabilityComment`).
- **Contenido informativo no editable, asociado a proyecto y organización:** IMPLEMENTADO.
- **Estado:** IMPLEMENTADO
- **Referencia ISO 27001:** (Existente); ISO/IEC 27034-1 (Application Security - Audit trails)

### RQF8 - Notificaciones automáticas (Opcional)
- **OMITIDO** (por decisión del usuario).

### RQF1.9 - Comportamiento de Entidades Inactivas/Cerradas y sus Elementos Asociados

#### RQF1.9.1 - Proyectos Inactivos
- **Gestión de Vulnerabilidades:** No se permite la creación, edición, eliminación, cambio de estado o asignación de usuarios para vulnerabilidades pertenecientes a un proyecto inactivo.
    - El botón "Crear Vulnerabilidad" (en el contexto de un proyecto específico, como en la lista de vulnerabilidades de ese proyecto) se oculta si el proyecto está inactivo.
- **Gestión de Tareas:** No se permite la creación, edición o eliminación de tareas asociadas a vulnerabilidades dentro de un proyecto inactivo.
- **Visualización:**
    - Los usuarios *pueden* ver la lista de vulnerabilidades de un proyecto inactivo (si tienen los permisos generales para ver vulnerabilidades y están asociados al proyecto según su rol).
    - Los usuarios *pueden* ver los detalles de una vulnerabilidad específica en un proyecto inactivo.
    - Los usuarios *pueden* ver la lista de tareas de una vulnerabilidad que pertenece a un proyecto inactivo (si tienen los permisos generales para ver tareas y están asociados al proyecto de la vulnerabilidad según su rol).
    - Los usuarios *pueden* ver los detalles de una tarea específica cuya vulnerabilidad asociada pertenece a un proyecto inactivo.
    - Los enlaces para acceder a la visualización de vulnerabilidades y tareas (de vulnerabilidades en proyectos inactivos) permanecen visibles y funcionales si el usuario tiene los permisos de visualización correspondientes.

#### RQF1.9.2 - Vulnerabilidades Cerradas
- **Gestión de Vulnerabilidad:** No se permite la edición de campos principales, eliminación, cambio de estado (excepto reapertura si el flujo de estados lo permite y con la debida justificación) o asignación de usuarios para una vulnerabilidad en estado "Cerrada".
- **Gestión de Tareas:** No se permite la creación, edición o eliminación de tareas asociadas a una vulnerabilidad en estado "Cerrada".
- **Visualización:**
    - Los usuarios *pueden* ver los detalles de una vulnerabilidad cerrada.
    - Los usuarios *pueden* ver la lista de tareas asociadas a una vulnerabilidad cerrada.
    - Los usuarios *pueden* ver los detalles de una tarea específica cuya vulnerabilidad asociada está cerrada.
    - Los enlaces para acceder a la visualización de tareas (de vulnerabilidades cerradas) permanecen visibles y funcionales si el usuario tiene los permisos de visualización correspondientes.

#### RQF1.9.3 - Botón Global "Crear Tarea"
- **Visibilidad Condicional en Índice Principal de Tareas:** El botón "Nueva Tarea" en la página principal del listado de tareas (`tasks.index`) se oculta si no existen vulnerabilidades válidas para las cuales el usuario autenticado pueda actualmente crear una tarea. Una vulnerabilidad se considera un objetivo válido para la creación de tareas si cumple con todas las siguientes condiciones:
    - Pertenece a un proyecto en estado "activo".
    - No está en estado "Cerrada".
    - El usuario autenticado tiene permiso explícito para crear tareas en ella (verificado mediante la política `VulnerabilityPolicy@crearTareas`).

## 2. Requerimientos de Seguridad

### RQS1 - Autenticación y control de acceso
- **Login con usuario y contraseña cifrada:** IMPLEMENTADO (provisto por Laravel Fortify/Jetstream, contraseñas hasheadas).
- **Control de acceso basado en roles (RBAC):** IMPLEMENTADO (`RolePermissionSeeder` ajustado). Lógica de Policies para la restricción fina del rol 'miembro' sobre *sus* vulnerabilidades implementada.
- **Autenticación en dos pasos:** IMPLEMENTADO (provisto por Laravel Fortify, funcionalidad confirmada por el usuario).

### RQS2 - Cifrado de datos sensibles
- **Uso obligatorio de HTTPS y TLS 1.2 o superior:** CONFIRMADO (requisito de entorno de servidor).
- **Cifrado en almacenamiento y transmisión de datos:**
    - Transmisión: Cubierta por HTTPS.
    - Almacenamiento: Contraseñas hasheadas, secretos 2FA cifrados por Fortify. Adicionalmente, campos como identificación del usuario, y título, descripción y componente de las vulnerabilidades utilizan cifrado reversible.
- **Referencia ISO 27001:** (Existente); ISO/IEC 27034-1 (Application Security - Data security)

### RQS3 - Integridad de la información
- **Implementación de auditoría o bitácora para asegurar trazabilidad:** IMPLEMENTADO (mediante `AuditLog` y `VulnerabilityComment`). (Estado: IMPLEMENTADO)
- **Toda modificación queda registrada:** IMPLEMENTADO (core CRUD on vulnerabilities, assignments, and state changes are logged). (Estado: IMPLEMENTADO)

### RQS4 - Protección contra vulnerabilidades comunes
- **Validación de entrada y lógica contra OWASP Top 10 (SQLi, XSS, CSRF):** REVISADO. Laravel provee una base sólida (Eloquent para SQLi, Blade para XSS, middleware CSRF). Se confirmó uso adecuado de estas protecciones y validaciones de entrada.
- **Aplicación de medidas de mitigación:** IMPLEMENTADO (a través de las características de Laravel y prácticas de codificación).

### RQS5 - Política de contraseñas seguras
- **Requiere contraseñas complejas:** IMPLEMENTADO (longitud mínima 10, mayúsculas/minúsculas, números, símbolos, y verificación contra brechas de datos).
- **Almacenamiento mediante algoritmos de hashing (ej. bcrypt):** IMPLEMENTADO (bcrypt por defecto en Laravel).

## 3. Requerimientos No Funcionales

### RQ1_NF - Rendimiento y escalabilidad
- **Soporta registros masivos sin pérdida de rendimiento:** MEJORADO SIGNIFICATIVAMENTE (carga masiva de Excel refactorizada a proceso asíncrono con Laravel Queues).
- **Escalable horizontalmente:** El uso de colas facilita la escalabilidad de los workers de importación. La aplicación Laravel puede escalarse horizontalmente con balanceadores de carga.
- **Eficiencia de Consultas:** REVISADO (Dashboard y listados principales usan eager loading; paginación implementada donde es crítico).

### RQ2_NF - Portabilidad y accesibilidad
- **Accesible desde navegadores modernos:** CONFIRMADO (stack tecnológico moderno).
- **Diseño responsivo para móviles:** REVISADO (buen nivel de responsividad, con manejo de tablas anchas y ajustes menores realizados).

### RQ3_NF - Usabilidad
- **Interfaz intuitiva y amigable para usuarios no técnicos:** REVISADO (en general buena usabilidad, con corrección en visualización de estado de vulnerabilidades).

### RQ4_NF - Mantenibilidad
- **Código estructurado y documentado para facilitar mantenimiento:** SEGUIDO (se intentó seguir la estructura DDD existente, se añadieron comentarios).
- **Pruebas Unitarias y de Feature:** IMPLEMENTADO (para 3 áreas críticas: `VulnerabilityStateService`, `ProcessVulnerabilityImportJob`, y `VulnerabilityAuditing`).

### RQ5_NF - Disponibilidad
- **Sistema disponible al menos el 95% del tiempo en producción:** Requisito de infraestructura y monitoreo, fuera del alcance directo de la implementación de código, pero la robustez mejorada (ej. colas) contribuye indirectamente.

### RQ6_NF - Compatibilidad tecnológica
- **Basado en tecnologías comunes (PHP Laravel, Node.js, SQL estándar):** CONFIRMADO.

## 4. Tipos de Usuarios

### 4.1. Líder de proyecto
- **Accede a todas las funcionalidades:** IMPLEMENTADO (según definición en `RolePermissionSeeder`).
- **Tiene privilegios de seguridad altos:** IMPLEMENTADO.

### 4.2. Miembro de proyecto
- **Acceso limitado a funcionalidades específicas (registro, dashboard, control de estados, historial):** IMPLEMENTADO (según definición en `RolePermissionSeeder`, con la advertencia de que el control de estados de *sus* vulnerabilidades requiere Policies adicionales para ser efectivo).
- **Privilegios de seguridad altos:** El término "altos" es relativo. Tienen los permisos necesarios para sus funciones, pero no son administradores.
