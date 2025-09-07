# 🛡️ Sistema de Permisos y Políticas - Guía Completa

## 📋 Resumen del Sistema

Has implementado un sistema completo de autorización con:

-   ✅ **68 permisos** generados automáticamente (4 acciones × 17 modelos)
-   ✅ **4 roles** con permisos específicos (Super Admin, Admin, Técnico, Usuario)
-   ✅ **17 políticas** para cada modelo
-   ✅ **Middleware personalizado** para rutas
-   ✅ **Trait helper** para controladores

## 🎭 Roles y Permisos

### Super Admin

-   ✅ **Todos los permisos** (68/68)
-   Acceso completo al sistema

### Admin

-   ✅ **55 permisos**
-   Gestión completa de áreas, categorías, dispositivos, SLA, factores SLA, monitor SLA, encuestas, dashboard ITIL, tickets, usuarios, locales
-   ❌ No puede gestionar roles ni permisos

### Técnico

-   ✅ **14 permisos** (RESTRINGIDO)
-   Ver áreas, categorías, dispositivos, usuarios
-   Crear/editar dispositivos y asignaciones
-   Crear/editar tickets
-   ❌ **SIN ACCESO A:** SLA, factores de prioridad/tipo SLA, monitor SLA, encuestas de satisfacción, dashboard ITIL

### Usuario

-   ✅ **5 permisos**
-   Ver áreas, categorías, dispositivos, tickets
-   Crear tickets

## 🛠️ Formas de Usar el Sistema

### 1. Middleware en Rutas (Recomendado para rutas simples)

```php
// routes/web.php
Route::middleware(['auth', 'permission:ver-area'])->group(function() {
    Route::get('/areas', [AreaController::class, 'index']);
});

// O individual
Route::get('/areas/create', [AreaController::class, 'create'])
    ->middleware('permission:crear-area');
```

### 2. Políticas con Gate::authorize() (Recomendado por Laravel)

```php
// En el controlador
public function show(Area $area) {
    Gate::authorize('view', $area);
    return view('areas.show', compact('area'));
}

public function update(Request $request, Area $area) {
    Gate::authorize('update', $area);
    // ... lógica de actualización
}
```

### 3. Trait HasPermissions (Para verificaciones manuales)

```php
// En el controlador
use App\Traits\HasPermissions;

class AreaController extends Controller {
    use HasPermissions;

    public function index() {
        $this->requireViewPermission('area');
        // ... lógica
    }

    public function create() {
        $this->requireCreatePermission('area');
        // ... lógica
    }
}
```

### 4. Verificaciones en Blade (Para mostrar/ocultar elementos)

```blade
{{-- Usando las directivas de Spatie --}}
@can('crear-area')
    <a href="{{ route('areas.create') }}" class="btn btn-primary">
        Crear Área
    </a>
@endcan

@can('editar-area')
    <a href="{{ route('areas.edit', $area) }}" class="btn btn-warning">
        Editar
    </a>
@endcan

{{-- Usando políticas --}}
@can('update', $area)
    <button>Editar Área</button>
@endcan
```

### 5. API con Verificación JSON

```php
public function apiAreas() {
    if (!auth()->user()->can('ver-area')) {
        return response()->json([
            'error' => 'Sin permisos',
            'required' => 'ver-area'
        ], 403);
    }

    return response()->json(Area::all());
}
```

## 📊 Permisos Disponibles por Modelo

### Áreas

-   `ver-area` - Ver listado y detalles
-   `crear-area` - Crear nuevas áreas
-   `editar-area` - Modificar áreas existentes
-   `borrar-area` - Eliminar áreas

### Tickets

-   `ver-ticket` - Ver tickets
-   `crear-ticket` - Crear tickets
-   `editar-ticket` - Modificar tickets
-   `borrar-ticket` - Eliminar tickets
-   `ver-ticket-administrador` - Vista administrativa
-   `crear-ticket-administrador` - Creación administrativa
-   `editar-ticket-administrador` - Edición administrativa
-   `borrar-ticket-administrador` - Eliminación administrativa

### Usuarios

-   `ver-user` - Ver usuarios
-   `crear-user` - Crear usuarios
-   `editar-user` - Modificar usuarios
-   `borrar-user` - Eliminar usuarios

### Dispositivos

-   `ver-dispositivo` - Ver dispositivos
-   `crear-dispositivo` - Crear dispositivos
-   `editar-dispositivo` - Modificar dispositivos
-   `borrar-dispositivo` - Eliminar dispositivos
-   `ver-dispositivo-asignacion` - Ver asignaciones
-   `crear-dispositivo-asignacion` - Crear asignaciones
-   `editar-dispositivo-asignacion` - Modificar asignaciones
-   `borrar-dispositivo-asignacion` - Eliminar asignaciones

### [+ Otros modelos con el mismo patrón]

## 🔧 Comandos Útiles

```bash
# Ejecutar seeders de permisos
php artisan db:seed --class=PermissionSeeder

# Verificar permisos del sistema
php artisan permisos:verificar

# Verificar permisos de un usuario específico
php artisan permisos:verificar usuario@email.com

# Limpiar caché de permisos
php artisan permission:cache-reset
```

## 🚀 Ejemplos de Implementación

### Controlador con Autorización Completa

```php
class AreaController extends Controller {
    use HasPermissions;

    public function index() {
        $this->requireViewPermission('area');
        $areas = Area::all();
        $permissions = $this->getPermissionsForView('area');
        return view('areas.index', compact('areas', 'permissions'));
    }

    public function store(Request $request) {
        Gate::authorize('create', Area::class);
        // ... validación y creación
    }
}
```

### Vista con Permisos Condicionales

```blade
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Áreas</h1>

        @can('crear-area')
            <a href="{{ route('areas.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nueva Área
            </a>
        @endcan
    </div>

    <div class="table-responsive">
        <table class="table">
            @foreach($areas as $area)
                <tr>
                    <td>{{ $area->nombre }}</td>
                    <td>
                        @can('ver-area')
                            <a href="{{ route('areas.show', $area) }}">Ver</a>
                        @endcan

                        @can('editar-area')
                            <a href="{{ route('areas.edit', $area) }}">Editar</a>
                        @endcan

                        @can('borrar-area')
                            <form method="POST" action="{{ route('areas.destroy', $area) }}">
                                @csrf @method('DELETE')
                                <button type="submit" onclick="return confirm('¿Seguro?')">
                                    Eliminar
                                </button>
                            </form>
                        @endcan
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
</div>
@endsection
```

## 🎯 Mejores Prácticas

1. **Usa Gate::authorize()** para verificaciones de políticas en controladores
2. **Usa middleware** para proteger rutas completas
3. **Usa @can()** en vistas para mostrar/ocultar elementos
4. **Usa el trait HasPermissions** para lógica compleja en controladores
5. **Mantén consistencia** en el naming: `accion-modelo`
6. **Documenta permisos especiales** para casos de uso específicos

## 🔍 Debugging y Troubleshooting

```php
// Verificar si un usuario tiene un permiso
$user->can('ver-area') // true/false

// Ver todos los permisos de un usuario
$user->getAllPermissions()->pluck('name')

// Ver roles de un usuario
$user->roles->pluck('name')

// Verificar si un usuario tiene un rol
$user->hasRole('Admin') // true/false
```

---

**✅ Sistema completamente implementado y listo para usar**

Tu sistema de permisos está configurado y funcionando. Puedes empezar a proteger tus rutas y controladores usando cualquiera de los métodos mostrados arriba.
