# Manual de Usuario Detallado por Recursos

Este manual explica detalladamente cada recurso y funcionalidad del sistema de gestión de incidencias "SISTEMA DE TICKETS", organizado por rol de usuario y con instrucciones específicas para cada módulo.

---

## USUARIOS FINALES (EMPLEADOS)

### 1. Panel de Usuario

**Acceso al sistema**
- Dirígete a la URL del sistema: `http://tudominio.com` o `http://localhost` (en entorno local)
- Ingresa tus credenciales (correo y contraseña)
- Selecciona "Recordar sesión" si deseas mantener la sesión activa
- Haz clic en el botón "Ingresar"

![Pantalla de login](img/login.png) *(imagen representativa)*

**Navegación principal**
- **Tablero principal**: Muestra un resumen de tickets y dispositivos asignados
- **Menú lateral**: Permite navegar entre secciones
- **Perfil de usuario**: Accesible desde la esquina superior derecha

### 2. Gestión de Tickets

#### 2.1 Crear nuevo ticket

1. En el menú lateral, haz clic en "**Reportar incidencia**" o en el botón "+ Nuevo ticket"
2. Completa el formulario con estos campos:
   - **Título**: Breve descripción del problema (máximo 100 caracteres)
   - **Descripción**: Detalle completo de la incidencia (incluye pasos para reproducir, errores visualizados, etc.)
   - **Tipo**: Selecciona entre:
     * Incidencia técnica
     * Solicitud de servicio
     * Consulta
     * Petición de cambio
   - **Prioridad**: Selecciona entre:
     * Baja
     * Media 
     * Alta
     * Crítica
   - **Área**: Selecciona el área a la que pertenece la incidencia
   - **Dispositivo**: (Opcional) Selecciona el dispositivo involucrado en la lista desplegable
   - **Archivos adjuntos**: Haz clic en "Seleccionar archivos" (puedes adjuntar hasta 5 archivos de máximo 10MB cada uno)
3. Haz clic en "Enviar ticket"
4. Recibirás una notificación de confirmación con el número de ticket asignado

#### 2.2 Lista de tickets

1. Accede a "**Mis tickets**" en el menú lateral
2. Verás una tabla con todos tus tickets que incluye:
   - **ID**: Número identificador único
   - **Título**: Resumen del ticket
   - **Estado**: Abierto, En progreso, Cerrado, Cancelado
   - **Prioridad**: Indicada por colores (rojo=crítica, amarillo=alta, etc.)
   - **Fecha**: Fecha de creación
   - **Técnico**: Nombre del técnico asignado
   - **Acciones**: Botón para ver detalles

*Nota: Esta vista se actualiza automáticamente cada 30 segundos para mostrar cambios recientes.*

#### 2.3 Detalle del ticket

1. Haz clic en "Ver" en la lista de tickets o en el número del ticket
2. La pantalla de detalles muestra:
   - **Información general**: Título, estado, prioridad, fechas, etc.
   - **Descripción completa**: Texto original de la incidencia
   - **Historial de actualizaciones**: Cambios de estado o asignación
   - **Comentarios**: Comunicación entre usuarios y técnicos
   - **Archivos adjuntos**: Documentos o imágenes asociados

#### 2.4 Agregar comentarios

1. En la pantalla de detalle del ticket, desplázate hasta la sección "Comentarios"
2. Escribe tu mensaje en el campo de texto
3. (Opcional) Para adjuntar archivos adicionales, haz clic en "Adjuntar"
4. Haz clic en "Enviar comentario"
5. El comentario aparecerá inmediatamente en el historial de la conversación

*Nota: La sección de comentarios se actualiza automáticamente cada 5 segundos.*

### 3. Dispositivos

#### 3.1 Ver dispositivos asignados

1. En el menú lateral, selecciona "**Mis dispositivos**"
2. Visualizarás una tabla con todos los dispositivos asignados a tu nombre
3. La información incluye:
   - Nombre del dispositivo
   - Categoría
   - Número de serie
   - Estado
   - Marca y modelo
   - Fecha de asignación

#### 3.2 Reportar problema con dispositivo

1. En la lista de dispositivos, localiza el equipo con problemas
2. Haz clic en el botón "Reportar problema"
3. Se abrirá el formulario de nuevo ticket con el dispositivo ya seleccionado
4. Completa los campos restantes y envía el ticket

### 4. Sistema de Notificaciones

#### 4.1 Campanita de notificaciones

- Ubicada en la esquina superior derecha de la pantalla
- Muestra un contador con el número de notificaciones no leídas
- El icono cambia de color cuando hay notificaciones nuevas
- Reproduce un sonido al recibir nuevas notificaciones

#### 4.2 Ver y gestionar notificaciones

1. Haz clic en la campanita para desplegar el menú de notificaciones
2. Las notificaciones no leídas aparecen resaltadas
3. Haz clic en cualquier notificación para:
   - Marcarla como leída
   - Navegar al contenido relacionado (ej. ticket actualizado)
4. Para marcar todas como leídas, haz clic en "Marcar todas como leídas"

---

## TÉCNICOS

### 1. Panel de Técnico

El panel de técnico incluye todas las funcionalidades del usuario final, más:

**Acceso extendido**
- Sección "Tickets asignados" en el menú principal
- Vista de tickets por área (si está habilitado)
- Acciones de gestión de tickets

### 2. Gestión de Tickets Asignados

#### 2.1 Lista de tickets asignados

1. Accede a "**Tickets asignados**" en el menú principal
2. Visualizarás una tabla con filtros avanzados:
   - Por estado (Abiertos, En progreso, Cerrados, Todos)
   - Por prioridad
   - Por tipo de ticket
   - Por fecha
3. Opciones de ordenación por cualquier columna

#### 2.2 Actualizar estado de ticket

1. Desde la lista de tickets, haz clic en "Gestionar" o en el ID del ticket
2. En la pantalla de detalles, localiza el menú desplegable "Estado"
3. Selecciona el nuevo estado:
   - **Abierto**: Recién creado o pendiente de revisión
   - **En progreso**: Cuando estás trabajando activamente en el ticket
   - **Cerrado**: Cuando la incidencia ha sido resuelta
   - **Cancelado**: Cuando se cancela sin resolución
   - **Escalado**: Cuando requiere atención de un nivel superior
4. (Opcional) Agrega un comentario explicando el cambio de estado
5. Haz clic en "Actualizar estado"

*Nota: Cada cambio de estado genera una notificación automática para el usuario.*

#### 2.3 Gestión de comentarios

1. Los técnicos pueden ver todos los comentarios del ticket
2. Para responder:
   - Escribe la respuesta en el campo de texto
   - Haz clic en "Responder"
3. Los comentarios de técnicos aparecen destacados para diferenciarlos

#### 2.4 Consulta técnica de dispositivos

1. Desde el detalle del ticket, si hay un dispositivo asociado, haz clic en "Ver dispositivo"
2. Accede a información técnica detallada:
   - Especificaciones técnicas
   - Historial de mantenimiento
   - Garantía y fecha de compra
   - Historial de incidencias previas

#### 2.5 Adjuntar soluciones o documentación

1. En la sección de comentarios, haz clic en "Adjuntar archivo"
2. Selecciona el archivo de tu equipo (diagramas, capturas, manuales, etc.)
3. (Opcional) Agrega una descripción del archivo
4. Haz clic en "Subir archivo"
5. El adjunto aparecerá en la lista de archivos del ticket

---

## ADMINISTRADORES

### 1. Acceso al Panel de Administración (Filament)

1. Ingresa a la URL de administración: `http://tudominio.com/admin` o `http://localhost/admin`
2. Inicia sesión con tus credenciales de administrador
3. Visualizarás el dashboard administrativo con:
   - Estadísticas generales
   - Gráficos de tickets por estado/prioridad
   - Actividad reciente
   - Acceso rápido a todos los recursos

### 2. Gestión Completa de Tickets

#### 2.1 Recurso de Tickets

Accede a través del menú lateral "**Tickets**" para visualizar la lista completa con opciones avanzadas:

- **Búsqueda global**: Filtra por cualquier campo (ID, título, descripción, usuario, etc.)
- **Filtros avanzados**:
  * Por estado, prioridad, tipo
  * Por técnico asignado
  * Por dispositivo
  * Por área
  * Por fecha de creación/actualización
- **Acciones masivas**:
  * Cambio de estado a múltiples tickets
  * Asignación masiva
  * Exportación a Excel/CSV

#### 2.2 Ver ticket detallado (ViewTicket)

1. Haz clic en el ID o título del ticket para acceder a la vista detallada
2. La interfaz muestra:
   - **Información del Ticket**: Todos los campos y datos
   - **Información del Dispositivo**: Si está asociado
   - **Archivos Adjuntos**: Con vista previa y descarga
   - **Comentarios**: Sección integrada con el sistema de comentarios
   - **Historial de Cambios**: Log de todas las modificaciones

#### 2.3 Editar ticket (EditTicket)

1. En la vista detallada, haz clic en el botón "Editar"
2. Modifica cualquier campo según necesites:
   - Título y descripción
   - Estado, prioridad, tipo
   - Técnico asignado (menú desplegable con búsqueda)
   - Dispositivo asociado
   - Archivos adjuntos (agregar/eliminar)
3. Haz clic en "Guardar" para aplicar los cambios

#### 2.4 Comentar ticket desde panel admin

1. En la vista detallada, ubica la sección "Comentarios"
2. Escribe tu comentario en el campo de texto
3. Selecciona si es un comentario interno o visible para el usuario
4. Haz clic en "Comentar"

### 3. Gestión de Usuarios

#### 3.1 Recurso de Usuarios

Accede desde el menú lateral "**Usuarios**" para:

- **Lista completa de usuarios** con filtros y búsqueda
- **Crear nuevo usuario**:
  1. Haz clic en "Crear usuario"
  2. Completa los campos obligatorios:
     - Nombre y apellidos
     - Email (será el nombre de usuario)
     - Contraseña (o generar automáticamente)
     - Rol (selecciona de la lista)
     - Área asignada
  3. Haz clic en "Crear"

- **Editar usuario existente**:
  1. Localiza el usuario en la lista
  2. Haz clic en el botón "Editar"
  3. Modifica los campos necesarios
  4. Actualiza la contraseña si es necesario
  5. Haz clic en "Guardar"

- **Desactivar usuario**:
  1. Localiza el usuario en la lista
  2. Haz clic en "Desactivar" (o cambia su estado)
  3. Confirma la acción

### 4. Gestión de Áreas

#### 4.1 Recurso de Áreas

Accede desde el menú lateral "**Áreas**" para:

- **Ver todas las áreas** de la organización
- **Crear nueva área**:
  1. Haz clic en "Crear área"
  2. Ingresa el nombre y descripción
  3. (Opcional) Asigna un responsable
  4. Haz clic en "Crear"

- **Administrar usuarios por área**:
  1. Selecciona un área
  2. Ve a la pestaña "Usuarios"
  3. Agrega o elimina usuarios asociados al área

- **Administrar dispositivos por área**:
  1. Selecciona un área
  2. Ve a la pestaña "Dispositivos"
  3. Visualiza y gestiona los dispositivos asignados al área

### 5. Gestión de Dispositivos

#### 5.1 Recurso de Dispositivos

Accede desde el menú lateral "**Dispositivos**" para:

- **Lista completa de dispositivos** con filtros avanzados
- **Crear nuevo dispositivo**:
  1. Haz clic en "Crear dispositivo"
  2. Completa la información:
     - Nombre y descripción
     - Categoría (selecciona del menú)
     - Número de serie
     - Marca y modelo
     - Estado (Disponible, Asignado, Reparación, etc.)
     - Área y/o usuario asignado
     - Fecha de adquisición
     - Observaciones
  3. Haz clic en "Crear"

- **Editar dispositivo**:
  1. Localiza el dispositivo en la lista
  2. Haz clic en "Editar"
  3. Actualiza los campos necesarios
  4. Haz clic en "Guardar"

### 6. Sistema de Notificaciones Administrativas

- **Campanita de notificaciones** (similar al usuario final pero con eventos administrativos)
- **Panel de notificaciones** con filtros avanzados
- **Configuración de notificaciones**:
  1. Ve a tu perfil de usuario
  2. Selecciona "Preferencias de notificaciones"
  3. Configura qué eventos deseas recibir

### 7. Reportes y Exportación

#### 7.1 Exportar datos

1. En cualquier lista (tickets, usuarios, dispositivos), haz clic en "Exportar"
2. Selecciona el formato (Excel, CSV, PDF)
3. (Opcional) Configura campos a exportar
4. Haz clic en "Descargar"

---

## SUPER ADMINISTRADORES

El Super Administrador tiene todas las capacidades del Administrador, más:

### 1. Gestión de Roles y Permisos

#### 1.1 Recurso de Roles

1. Accede a "**Roles**" en el menú lateral
2. Visualiza los roles existentes:
   - Super Admin
   - Administrador
   - Técnico
   - Usuario
3. Para crear un nuevo rol:
   1. Haz clic en "Crear rol"
   2. Asigna nombre y descripción
   3. Selecciona permisos específicos
   4. Guarda el nuevo rol

#### 1.2 Asignar permisos específicos

1. Selecciona un rol existente
2. Haz clic en "Editar"
3. En la sección "Permisos", marca o desmarca los permisos:
   - Crear tickets
   - Editar tickets
   - Ver tickets (propios/todos)
   - Gestionar usuarios
   - Gestionar dispositivos
   - Gestionar áreas
   - Acceso a reportes
   - etc.

### 2. Configuración Avanzada del Sistema

#### 2.1 Configuración general

Accede a "**Configuración**" para:

- Personalización de la interfaz
- Ajustes de notificaciones
- Configuración de correos electrónicos
- Parámetros del sistema SLA
- Integración con servicios externos

#### 2.2 Logs del sistema

1. Accede a "**Logs**" para:
   - Ver actividad de usuarios
   - Revisar errores del sistema
   - Monitorear rendimiento
   - Auditar cambios sensibles

---

## ATAJOS Y CONSEJOS RÁPIDOS

- **Barra de búsqueda global**: Busca rápidamente tickets, usuarios o dispositivos desde cualquier pantalla
- **Notificaciones**: Haz clic derecho en una notificación para ver opciones adicionales
- **Filtros guardados**: Guarda tus filtros frecuentes para acceso rápido
- **Modo oscuro**: Activa/desactiva el modo oscuro desde tu perfil de usuario
- **Atajos de teclado**:
  * `Ctrl+N`: Nuevo ticket (en pantallas aplicables)
  * `Ctrl+F`: Búsqueda rápida
  * `Esc`: Cerrar diálogos

---

**Fin del manual detallado por recursos**
