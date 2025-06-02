# Resumen de Requisitos Implementados

## 1. Requerimientos Funcionales

### RQF1.1 - Registro y almacenamiento de vulnerabilidades
- **Permite registro manual mediante formulario con campos específicos:** IMPLEMENTADO.
- **Soporta carga masiva desde archivo Excel con plantilla predefinida:** IMPLEMENTADO (de forma asíncrona con Laravel Queues).
- **Valida duplicados y actualiza si corresponde (en carga masiva):** IMPLEMENTADO.
- **Utiliza campos tipo "select" para facilitar la usabilidad:** IMPLEMENTADO.

#### RQF1.1.1 - Reporte Detallado de Errores en Importación de Vulnerabilidades
- **Descripción:** Para mejorar la retroalimentación al usuario durante el proceso de carga masiva de vulnerabilidades, el sistema proporciona un informe detallado de errores en lugar de alertas genéricas.
- **Especificaciones:**
    - Cada intento de importación se registra como un lote (`import_batches`) con información del archivo original, usuario, estado del proceso (ej: pendiente, procesando, completado con errores, completado exitosamente, fallido), conteo total de filas, filas procesadas exitosamente y filas fallidas.
    - Los errores específicos encontrados a nivel de fila durante el procesamiento en segundo plano (por `ProcessVulnerabilityImportJob`) se registran en una tabla dedicada (`import_row_errors`), incluyendo el número de fila original, los mensajes de error y, opcionalmente, los datos de la fila problemática.
    - Al finalizar un trabajo de importación, el usuario recibe una notificación (ej: email, base de datos) que resume el resultado: total de filas, número de éxitos y número de fallos.
    - Si se produjeron fallos, la notificación incluye un enlace a una página donde se pueden visualizar los errores detallados del lote de importación correspondiente.
    - Se implementa una interfaz de usuario donde los usuarios pueden ver el historial de sus lotes de importación y acceder a los detalles de los errores de cada fila fallida. Los administradores tienen la capacidad de ver todos los lotes de importación.
- **Estado:** IMPLEMENTADO.

### RQF2 - Gestión de usuarios, proyectos y organizaciones
- **Registro de usuarios con campos como nombre, correo, área, organización y contraseña:** IMPLEMENTADO (campo 'área' añadido).
- **Asignación de roles y permisos según estructura organizacional:** IMPLEMENTADO (roles Admin, Líder, Miembro con permisos definidos en `RolePermissionSeeder`). Se requiere lógica de Policies para restricciones más finas en `miembro`.
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
- **No se puede modificar una vez cerrada (con matices):** IMPLEMENTADO (modificación de campos principales impedida; cambio de estado a reapertura requiere justificación).
- **Se requiere justificación textual para cambios de estado:** IMPLEMENTADO (guardada en `VulnerabilityComment` con estado anterior/posterior).
- **Registro del autor y resumen de cada acción:** IMPLEMENTADO (autor en `VulnerabilityComment`, detalles en `AuditLog`).
- **Lógica de transición en servicio dedicado:** IMPLEMENTADO (`VulnerabilityStateService`).

### RQF7 - Historial de cambios y auditoría
- **Registro de cada acción sobre una vulnerabilidad:** IMPLEMENTADO (usando `AuditLog` con `VulnerabilityObserver` y eventos de pivote para creación, actualización de campos, eliminación, asignación/desasignación de usuarios. Cambios de estado en `VulnerabilityComment`).
- **Contenido informativo no editable, asociado a proyecto y organización:** IMPLEMENTADO.

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
- **Control de acceso basado en roles (RBAC):** IMPLEMENTADO (`RolePermissionSeeder` ajustado). Se requiere lógica de Policies para la restricción fina del rol `miembro` sobre *sus* vulnerabilidades.
- **Autenticación en dos pasos:** IMPLEMENTADO (provisto por Laravel Fortify, funcionalidad confirmada por el usuario).

### RQS2 - Cifrado de datos sensibles
- **Uso obligatorio de HTTPS y TLS 1.2 o superior:** CONFIRMADO (requisito de entorno de servidor).
- **Cifrado en almacenamiento y transmisión de datos:**
    - Transmisión: Cubierta por HTTPS.
    - Almacenamiento: Contraseñas hasheadas, secretos 2FA cifrados por Fortify. No se identificaron otros campos que requieran cifrado reversible en BD en esta etapa.

### RQS3 - Integridad de la información
- **Implementación de auditoría o bitácora para asegurar trazabilidad:** IMPLEMENTADO (mediante `AuditLog` y `VulnerabilityComment`).
- **Toda modificación queda registrada:** IMPLEMENTADO.

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
