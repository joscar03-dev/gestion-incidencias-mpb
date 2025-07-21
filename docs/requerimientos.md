# Requerimientos funcionales y no funcionales

## Requerimientos funcionales

| Código | Requerimiento                                                                                 |
|--------|----------------------------------------------------------------------------------------------|
| RF-01  | El sistema debe permitir crear, ver, editar y cerrar tickets/incidencias.                   |
| RF-02  | Los tickets pueden ser asignados a técnicos o áreas específicas.                             |
| RF-03  | El sistema debe permitir cambiar el estado, prioridad y tipo de los tickets.                 |
| RF-04  | Los usuarios pueden adjuntar archivos a los tickets.                                         |
| RF-05  | El sistema debe enviar notificaciones automáticas a usuarios y administradores por eventos.   |
| RF-06  | Los usuarios y administradores pueden visualizar notificaciones en tiempo real o por polling. |
| RF-07  | El sistema debe permitir marcar notificaciones como leídas (individual o todas).              |
| RF-08  | Los usuarios pueden agregar comentarios a los tickets y verlos en tiempo real.                |
| RF-09  | El sistema debe soportar diferentes roles (usuario, técnico, administrador, super admin).     |
| RF-10  | El acceso a funcionalidades debe estar controlado por permisos y roles.                       |
| RF-11  | Los tickets pueden asociarse a dispositivos y mostrar información relacionada.                |
| RF-12  | El panel de administración debe permitir gestión avanzada de tickets, usuarios y dispositivos.|
| RF-13  | El sistema debe contar con autenticación y protección de rutas según permisos.                |

## Requerimientos no funcionales

| Código | Requerimiento                                                                                 |
|--------|----------------------------------------------------------------------------------------------|
| RNF-01 | La interfaz debe ser intuitiva, responsiva y amigable para el usuario.                        |
| RNF-02 | El sistema debe actualizar automáticamente los datos relevantes (polling o websockets).        |
| RNF-03 | El sistema debe responder rápidamente a las acciones del usuario.                             |
| RNF-04 | El sistema debe manejar eficientemente la carga y descarga de archivos adjuntos.               |
| RNF-05 | El sistema debe ser escalable para soportar múltiples usuarios y tickets simultáneamente.      |
| RNF-06 | El sistema debe protegerse contra accesos no autorizados y validar los datos de entrada.       |
| RNF-07 | El código debe ser mantenible y seguir buenas prácticas de Laravel y Filament.                 |
| RNF-08 | El sistema debe ser compatible con los navegadores modernos.                                   |
| RNF-09 | El sistema debe manejar de forma segura las sesiones y los tokens CSRF.                        |
| RNF-10 | El sistema debe permitir integración futura con servicios de notificaciones en tiempo real.    |

---

**Nota:** Estos requerimientos pueden adaptarse o ampliarse según las necesidades específicas del proyecto.
