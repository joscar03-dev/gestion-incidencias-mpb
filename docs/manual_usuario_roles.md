# Manual de Usuario por Rol

Este manual describe las funcionalidades disponibles para cada tipo de usuario en el sistema de gestión de incidencias, según los permisos y roles configurados.

---

## 1. Usuario Final (Empleado)

**Funcionalidades:**
- Crear un nuevo ticket de incidencia.
- Adjuntar archivos al ticket.
- Consultar el estado y detalles de sus tickets.
- Agregar comentarios a sus tickets.
- Recibir notificaciones sobre cambios en sus tickets (asignación, cierre, comentarios, etc.).
- Marcar notificaciones como leídas.
- Visualizar historial de notificaciones.
- Acceder a la información de los dispositivos asignados (si aplica).

**Restricciones:**
- No puede ver ni gestionar tickets de otros usuarios.
- No puede reasignar tickets ni cambiar estados avanzados.

---

## 2. Técnico

**Funcionalidades:**
- Ver los tickets asignados a su usuario o área.
- Cambiar el estado de los tickets (En Progreso, Cerrado, Escalado, etc.).
- Agregar comentarios y adjuntos a los tickets asignados.
- Recibir notificaciones automáticas sobre asignación, escalado, cierre, etc.
- Marcar notificaciones como leídas.
- Visualizar historial de notificaciones.
- Consultar información de dispositivos relacionados con los tickets.

**Restricciones:**
- No puede eliminar tickets.
- No puede reasignar tickets a otros técnicos (a menos que tenga permiso especial).
- No puede ver tickets de otros técnicos si no están asignados a su área.

---

## 3. Administrador

**Funcionalidades:**
- Acceso al panel de administración (Filament).
- Ver y gestionar todos los tickets del sistema.
- Asignar o reasignar tickets a técnicos o áreas.
- Cambiar estado, prioridad y tipo de cualquier ticket.
- Agregar comentarios y adjuntos a cualquier ticket.
- Visualizar y gestionar usuarios y roles.
- Consultar y gestionar dispositivos.
- Recibir notificaciones de todos los eventos importantes del sistema.
- Marcar notificaciones como leídas (individual o todas).
- Visualizar historial completo de notificaciones.
- Acceso a reportes y estadísticas (si está implementado).

**Restricciones:**
- No puede eliminar usuarios con rol de Super Admin.
- No puede modificar configuraciones críticas del sistema (solo Super Admin).

---

## 4. Super Administrador

**Funcionalidades:**
- Todas las funcionalidades del Administrador.
- Crear, editar y eliminar usuarios y roles.
- Modificar configuraciones avanzadas del sistema.
- Acceso total a todos los módulos, reportes y logs.
- Gestión avanzada de permisos y políticas de seguridad.

**Restricciones:**
- Ninguna. Es el rol con mayor nivel de acceso.

---

## Notas adicionales
- El acceso a cada funcionalidad puede estar sujeto a permisos específicos configurados por el administrador.
- El sistema puede mostrar u ocultar opciones en la interfaz según el rol del usuario autenticado.
- Para dudas o problemas, contactar al administrador del sistema.

---

**Fin del manual de usuario por roles.**
