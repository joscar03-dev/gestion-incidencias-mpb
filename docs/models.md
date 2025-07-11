# 🗄️ Documentación de Modelos y Base de Datos

Esta documentación detalla todos los modelos, sus relaciones, métodos y propiedades del sistema de gestión de incidencias.

## 📋 Índice de Modelos

1. [Ticket](#ticket) - Modelo principal del sistema
2. [User](#user) - Modelo de usuarios
3. [Area](#area) - Modelo de áreas/departamentos
4. [Sla](#sla) - Modelo de SLA (Service Level Agreement)
5. [Categoria](#categoria) - Modelo de categorías
6. [Dispositivo](#dispositivo) - Modelo de dispositivos
7. [Locale](#locale) - Modelo de ubicaciones
8. [CategoriaDispositivo](#categoriadispositivo) - Modelo de categorías de dispositivos
9. [DispositivoAsignacion](#dispositivoasignacion) - Modelo de asignaciones de dispositivos

---

## 🎫 Ticket

**Archivo:** `app/Models/Ticket.php`

### Descripción
Modelo principal que representa un ticket de soporte técnico. Implementa el sistema de comentarios y maneja toda la lógica de SLA, escalamiento y estados.

### Propiedades de Clase

```php
// Tabla asociada
protected $table = 'tickets';

// Campos asignables masivamente
protected $fillable = [
    'titulo',
    'descripcion',
    'estado',
    'prioridad',
    'comentario',
    'asignado_a',
    'asignado_por',
    'creado_por',
    'is_resolved',
    'attachment',
    'tiempo_respuesta',
    'tiempo_solucion',
    'fecha_cierre',
    'fecha_resolucion',
    'comentarios_resolucion',
    'escalado',
    'fecha_escalamiento',
    'sla_vencido',
    'area_id',
];

// Campos con tipo de dato específico
protected $casts = [
    'escalado' => 'boolean',
    'sla_vencido' => 'boolean',
    'is_resolved' => 'boolean',
    'fecha_escalamiento' => 'datetime',
    'fecha_cierre' => 'datetime',
    'fecha_resolucion' => 'datetime',
];
```

### Constantes

```php
// Estados disponibles para tickets
const ESTADOS = [
    'Abierto' => 'Abierto',
    'En Progreso' => 'En Progreso',
    'Escalado' => 'Escalado',
    'Cerrado' => 'Cerrado',
    'Cancelado' => 'Cancelado',
    'Archivado' => 'Archivado',
];

// Prioridades disponibles
const PRIORIDAD = [
    'Critica' => 'Critica',
    'Alta' => 'Alta',
    'Media' => 'Media',
    'Baja' => 'Baja',
];
```

### Relaciones

```php
// Relación con el usuario asignado
public function asignadoA()
{
    return $this->belongsTo(User::class, 'asignado_a');
}

// Relación con el usuario que asignó
public function asignadoPor()
{
    return $this->belongsTo(User::class, 'asignado_por');
}

// Relación con el usuario creador
public function creadoPor()
{
    return $this->belongsTo(User::class, 'creado_por');
}

// Relación con el área
public function area()
{
    return $this->belongsTo(Area::class, 'area_id');
}

// Relación con categorías (muchos a muchos)
public function categorias()
{
    return $this->belongsToMany(Categoria::class);
}

// Relación con SLA
public function sla()
{
    return $this->belongsTo(Sla::class, 'sla_id');
}
```

### Métodos Principales

#### `booted()`
```php
protected static function booted()
{
    static::creating(function ($ticket) {
        // Asignar automáticamente el área del usuario
        if (!$ticket->area_id && $ticket->creado_por) {
            $usuario = User::find($ticket->creado_por);
            if ($usuario && $usuario->area_id) {
                $ticket->area_id = $usuario->area_id;
            }
        }
        
        // Asignar automáticamente un técnico
        if (!$ticket->asignado_a && $ticket->area_id) {
            $tecnico = User::role('Tecnico')
                ->where('area_id', $ticket->area_id)
                ->inRandomOrder()
                ->first();
            
            if ($tecnico) {
                $ticket->asignado_a = $tecnico->id;
                $ticket->asignado_por = $tecnico->id;
            }
        }
    });
}
```

#### `getSlaActual()`
```php
public function getSlaActual()
{
    if ($this->area_id) {
        return Sla::where('area_id', $this->area_id)->first();
    }
    return null;
}
```

#### `getTiempoRestanteSla($tipo)`
```php
public function getTiempoRestanteSla($tipo = 'respuesta')
{
    $sla = $this->getSlaActual();
    if (!$sla) return null;
    
    // Calcular tiempo transcurrido
    $fechaCreacion = $this->created_at;
    $ahora = now();
    $tiempoTranscurrido = $fechaCreacion->diffInMinutes($ahora);
    
    // Obtener tiempo límite según tipo y prioridad
    $tiempoLimite = $sla->getTiempoLimite($this->prioridad, $tipo);
    
    return $tiempoLimite - $tiempoTranscurrido;
}
```

#### `getEstadoSla()`
```php
public function getEstadoSla()
{
    $tiempoRestante = $this->getTiempoRestanteSla('respuesta');
    
    if ($tiempoRestante === null) {
        return 'sin_sla';
    }
    
    if ($tiempoRestante <= 0) {
        return 'vencido';
    }
    
    if ($tiempoRestante <= 30) { // 30 minutos
        return 'advertencia';
    }
    
    return 'ok';
}
```

#### `debeEscalar()`
```php
public function debeEscalar()
{
    // No escalar si ya está escalado, cerrado o cancelado
    if ($this->escalado || 
        in_array($this->estado, ['Cerrado', 'Cancelado'])) {
        return false;
    }
    
    $sla = $this->getSlaActual();
    if (!$sla || !$sla->escalamiento_automatico) {
        return false;
    }
    
    // Verificar si se venció el SLA
    return $this->getTiempoRestanteSla('respuesta') <= 0;
}
```

#### `escalar($motivo = null)`
```php
public function escalar($motivo = null)
{
    if ($this->escalado) {
        return false;
    }
    
    $this->update([
        'escalado' => true,
        'fecha_escalamiento' => now(),
        'estado' => 'Escalado',
        'sla_vencido' => true,
    ]);
    
    // Crear notificación
    $this->crearNotificacionEscalamiento($motivo);
    
    return true;
}
```

#### `verificarSlaYEscalamiento()`
```php
public function verificarSlaYEscalamiento()
{
    if ($this->debeEscalar()) {
        return $this->escalar('Escalamiento automático por vencimiento de SLA');
    }
    
    return false;
}
```

### Scopes

```php
// Tickets activos (no archivados)
public function scopeActivos($query)
{
    return $query->where('estado', '!=', 'Archivado');
}

// Tickets abiertos
public function scopeAbiertos($query)
{
    return $query->where('estado', 'Abierto');
}

// Tickets por estado
public function scopePorEstado($query, $estado)
{
    return $query->where('estado', $estado);
}

// Tickets por prioridad
public function scopePorPrioridad($query, $prioridad)
{
    return $query->where('prioridad', $prioridad);
}

// Tickets del usuario
public function scopeDelUsuario($query, $userId)
{
    return $query->where('creado_por', $userId);
}

// Tickets asignados a usuario
public function scopeAsignadosA($query, $userId)
{
    return $query->where('asignado_a', $userId);
}
```

### Traits Utilizados

- **HasComments**: Implementa sistema de comentarios
- **HasFactory**: Soporte para factories de testing
- **Commentable**: Interfaz para comentarios

---

## 👤 User

**Archivo:** `app/Models/User.php`

### Descripción
Modelo de usuarios que extiende el modelo base de Laravel. Maneja autenticación, roles y permisos.

### Propiedades de Clase

```php
protected $fillable = [
    'name',
    'email',
    'password',
    'area_id',
    'email_verified_at',
];

protected $hidden = [
    'password',
    'remember_token',
];

protected $casts = [
    'email_verified_at' => 'datetime',
    'password' => 'hashed',
];
```

### Relaciones

```php
// Relación con área
public function area()
{
    return $this->belongsTo(Area::class);
}

// Tickets creados por el usuario
public function ticketsCreados()
{
    return $this->hasMany(Ticket::class, 'creado_por');
}

// Tickets asignados al usuario
public function ticketsAsignados()
{
    return $this->hasMany(Ticket::class, 'asignado_a');
}

// Dispositivos asignados
public function dispositivosAsignados()
{
    return $this->hasMany(DispositivoAsignacion::class, 'usuario_id');
}
```

### Traits Utilizados

- **HasFactory**: Soporte para factories
- **Notifiable**: Notificaciones Laravel
- **HasRoles**: Roles y permisos de Spatie

---

## 🏢 Area

**Archivo:** `app/Models/Area.php`

### Descripción
Modelo que representa áreas o departamentos organizacionales.

### Propiedades de Clase

```php
protected $fillable = [
    'nombre',
    'descripcion',
    'activo',
];

protected $casts = [
    'activo' => 'boolean',
];
```

### Relaciones

```php
// Usuarios del área
public function usuarios()
{
    return $this->hasMany(User::class);
}

// Tickets del área
public function tickets()
{
    return $this->hasMany(Ticket::class);
}

// SLA del área
public function slas()
{
    return $this->hasMany(Sla::class);
}

// Dispositivos del área
public function dispositivos()
{
    return $this->hasMany(Dispositivo::class);
}
```

### Métodos

```php
// Obtener técnicos del área
public function tecnicos()
{
    return $this->usuarios()
        ->role('Tecnico')
        ->where('activo', true);
}

// Obtener SLA activo
public function slaActivo()
{
    return $this->slas()
        ->where('activo', true)
        ->first();
}
```

---

## ⏱️ Sla

**Archivo:** `app/Models/Sla.php`

### Descripción
Modelo que maneja los Service Level Agreements (SLA) por área.

### Propiedades de Clase

```php
protected $fillable = [
    'area_id',
    'nombre',
    'descripcion',
    'tiempo_respuesta_critico',
    'tiempo_respuesta_alto',
    'tiempo_respuesta_medio',
    'tiempo_respuesta_bajo',
    'tiempo_resolucion_critico',
    'tiempo_resolucion_alto',
    'tiempo_resolucion_medio',
    'tiempo_resolucion_bajo',
    'escalamiento_automatico',
    'factor_escalamiento',
    'activo',
];

protected $casts = [
    'escalamiento_automatico' => 'boolean',
    'factor_escalamiento' => 'decimal:2',
    'activo' => 'boolean',
];
```

### Relaciones

```php
// Área asociada
public function area()
{
    return $this->belongsTo(Area::class);
}

// Tickets que usan este SLA
public function tickets()
{
    return $this->hasMany(Ticket::class);
}
```

### Métodos Principales

#### `getTiempoLimite($prioridad, $tipo)`
```php
public function getTiempoLimite($prioridad, $tipo = 'respuesta')
{
    $campo = "tiempo_{$tipo}_" . strtolower($prioridad);
    $tiempoBase = $this->$campo ?? 0;
    
    if ($this->escalamiento_automatico && $this->factor_escalamiento) {
        return $tiempoBase * $this->factor_escalamiento;
    }
    
    return $tiempoBase;
}
```

#### `calcularSlaEfectivo($prioridad)`
```php
public function calcularSlaEfectivo($prioridad)
{
    $tiempoRespuesta = $this->getTiempoLimite($prioridad, 'respuesta');
    $tiempoResolucion = $this->getTiempoLimite($prioridad, 'resolucion');
    
    return [
        'tiempo_respuesta' => $tiempoRespuesta,
        'tiempo_resolucion' => $tiempoResolucion,
        'override_aplicado' => $this->escalamiento_automatico,
        'factor_aplicado' => $this->factor_escalamiento,
    ];
}
```

---

## 📂 Categoria

**Archivo:** `app/Models/Categoria.php`

### Descripción
Modelo para categorización de tickets.

### Propiedades de Clase

```php
protected $fillable = [
    'nombre',
    'descripcion',
    'color',
    'activo',
];

protected $casts = [
    'activo' => 'boolean',
];
```

### Relaciones

```php
// Tickets de esta categoría
public function tickets()
{
    return $this->belongsToMany(Ticket::class);
}
```

---

## 💻 Dispositivo

**Archivo:** `app/Models/Dispositivo.php`

### Descripción
Modelo que representa dispositivos tecnológicos del inventario.

### Propiedades de Clase

```php
protected $fillable = [
    'nombre',
    'descripcion',
    'numero_serie',
    'modelo',
    'marca',
    'categoria_dispositivo_id',
    'area_id',
    'locale_id',
    'estado',
    'fecha_adquisicion',
    'valor_adquisicion',
    'proveedor',
    'garantia_meses',
    'observaciones',
];

protected $casts = [
    'fecha_adquisicion' => 'date',
    'valor_adquisicion' => 'decimal:2',
    'garantia_meses' => 'integer',
];
```

### Relaciones

```php
// Categoría del dispositivo
public function categoriaDispositivo()
{
    return $this->belongsTo(CategoriaDispositivo::class);
}

// Área del dispositivo
public function area()
{
    return $this->belongsTo(Area::class);
}

// Ubicación del dispositivo
public function locale()
{
    return $this->belongsTo(Locale::class);
}

// Asignaciones del dispositivo
public function asignaciones()
{
    return $this->hasMany(DispositivoAsignacion::class);
}

// Asignación activa
public function asignacionActiva()
{
    return $this->hasOne(DispositivoAsignacion::class)
        ->where('activo', true);
}
```

### Métodos

```php
// Verificar si está asignado
public function estaAsignado()
{
    return $this->asignacionActiva()->exists();
}

// Asignar a usuario
public function asignarA($userId, $observaciones = null)
{
    // Desactivar asignación anterior
    $this->asignaciones()->update(['activo' => false]);
    
    // Crear nueva asignación
    return $this->asignaciones()->create([
        'usuario_id' => $userId,
        'fecha_asignacion' => now(),
        'observaciones' => $observaciones,
        'activo' => true,
    ]);
}
```

---

## 📍 Locale

**Archivo:** `app/Models/Locale.php`

### Descripción
Modelo que representa ubicaciones físicas.

### Propiedades de Clase

```php
protected $fillable = [
    'nombre',
    'descripcion',
    'edificio',
    'piso',
    'direccion',
    'activo',
];

protected $casts = [
    'activo' => 'boolean',
];
```

### Relaciones

```php
// Dispositivos en esta ubicación
public function dispositivos()
{
    return $this->hasMany(Dispositivo::class);
}
```

---

## 🔧 CategoriaDispositivo

**Archivo:** `app/Models/CategoriaDispositivo.php`

### Descripción
Modelo para categorías de dispositivos.

### Propiedades de Clase

```php
protected $fillable = [
    'nombre',
    'descripcion',
    'icono',
    'activo',
];

protected $casts = [
    'activo' => 'boolean',
];
```

### Relaciones

```php
// Dispositivos de esta categoría
public function dispositivos()
{
    return $this->hasMany(Dispositivo::class);
}
```

---

## 📋 DispositivoAsignacion

**Archivo:** `app/Models/DispositivoAsignacion.php`

### Descripción
Modelo que maneja las asignaciones de dispositivos a usuarios.

### Propiedades de Clase

```php
protected $fillable = [
    'dispositivo_id',
    'usuario_id',
    'fecha_asignacion',
    'fecha_devolucion',
    'observaciones',
    'activo',
];

protected $casts = [
    'fecha_asignacion' => 'datetime',
    'fecha_devolucion' => 'datetime',
    'activo' => 'boolean',
];
```

### Relaciones

```php
// Dispositivo asignado
public function dispositivo()
{
    return $this->belongsTo(Dispositivo::class);
}

// Usuario asignado
public function usuario()
{
    return $this->belongsTo(User::class);
}
```

### Métodos

```php
// Devolver dispositivo
public function devolver($observaciones = null)
{
    return $this->update([
        'fecha_devolucion' => now(),
        'observaciones' => $observaciones,
        'activo' => false,
    ]);
}

// Verificar si está activo
public function esActivo()
{
    return $this->activo && !$this->fecha_devolucion;
}
```

---

## 🗄️ Estructura de Base de Datos

### Tablas Principales

1. **tickets** - Almacena los tickets de soporte
2. **users** - Usuarios del sistema
3. **areas** - Áreas/departamentos
4. **slas** - Configuraciones de SLA
5. **categorias** - Categorías de tickets
6. **dispositivos** - Inventario de dispositivos
7. **locales** - Ubicaciones físicas
8. **categoria_dispositivos** - Categorías de dispositivos
9. **dispositivo_asignacions** - Asignaciones de dispositivos

### Tablas de Relación

- **categoria_ticket** - Relación muchos a muchos entre categorías y tickets
- **model_has_roles** - Roles asignados a usuarios (Spatie)
- **model_has_permissions** - Permisos asignados a usuarios (Spatie)
- **role_has_permissions** - Permisos asignados a roles (Spatie)

### Índices Importantes

```sql
-- Índices para optimización de consultas
CREATE INDEX idx_tickets_estado ON tickets(estado);
CREATE INDEX idx_tickets_prioridad ON tickets(prioridad);
CREATE INDEX idx_tickets_creado_por ON tickets(creado_por);
CREATE INDEX idx_tickets_asignado_a ON tickets(asignado_a);
CREATE INDEX idx_tickets_area_id ON tickets(area_id);
CREATE INDEX idx_tickets_created_at ON tickets(created_at);
CREATE INDEX idx_users_area_id ON users(area_id);
```

---

## 🚀 Mejores Prácticas Implementadas

### 1. **Relaciones Eloquent**
- Uso consistente de relaciones `belongsTo`, `hasMany`, `belongsToMany`
- Lazy loading evitando N+1 queries con `with()`

### 2. **Scopes de Consulta**
- Métodos reutilizables para consultas comunes
- Filtros dinámicos en todos los modelos principales

### 3. **Mutators y Accessors**
- Formateo automático de datos
- Cálculos dinámicos de propiedades

### 4. **Eventos de Modelo**
- Lógica automática en `creating`, `updating`, `deleting`
- Asignación automática de relaciones

### 5. **Validación**
- Reglas de validación en Form Requests
- Validación a nivel de base de datos con constraints

### 6. **Cacheo**
- Estrategias de cacheo para consultas frecuentes
- Invalidación automática de cache

---

Esta documentación proporciona una visión completa de la arquitectura de datos del sistema. Para más detalles sobre implementación específica, consulta el código fuente de cada modelo.
