# Resumen de Requisitos Implementados

## 1. Requerimientos Funcionales

### RQF1.1 - Registro y almacenamiento de vulnerabilidades
- **Permite registro manual mediante formulario con campos específicos:** IMPLEMENTADO.
- **Soporta carga masiva desde archivo Excel con plantilla predefinida:** IMPLEMENTADO (de forma asíncrona con Laravel Queues).
- **Valida duplicados y actualiza si corresponde (en carga masiva):** IMPLEMENTADO.
- **Utiliza campos tipo "select" para facilitar la usabilidad:** IMPLEMENTADO.

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
