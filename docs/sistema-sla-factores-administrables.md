# Sistema de Factores SLA Administrables

## Resumen de Cambios

Se ha modificado el sistema SLA para hacer los factores de prioridad y tipo completamente administrables desde la base de datos, manteniendo la compatibilidad con el código existente.

## Nuevas Tablas

### `sla_prioridad_factores`

-   `id`: ID único
-   `codigo`: Código identificador (ej: critico, alto, medio, bajo)
-   `nombre`: Nombre descriptivo (ej: Crítica, Alta, Media, Baja)
-   `descripcion`: Descripción detallada del factor
-   `factor`: Factor multiplicador decimal (ej: 0.20, 0.50, 1.00, 1.50)
-   `activo`: Boolean para activar/desactivar
-   `orden`: Orden de visualización en interfaces
-   `created_at`, `updated_at`: Timestamps

### `sla_tipo_factores`

-   `id`: ID único
-   `codigo`: Código identificador (ej: incidente, general, requerimiento, cambio)
-   `nombre`: Nombre descriptivo (ej: Incidente, General, Requerimiento, Cambio)
-   `descripcion`: Descripción detallada del factor
-   `factor`: Factor multiplicador decimal (ej: 0.60, 0.80, 1.20, 1.50)
-   `activo`: Boolean para activar/desactivar
-   `orden`: Orden de visualización en interfaces
-   `created_at`, `updated_at`: Timestamps

## Nuevos Modelos

### `SlaPrioridadFactor`

Modelo para gestionar factores de prioridad:

-   Scopes: `activos()`, `ordenados()`
-   Métodos estáticos: `obtenerFactorPorCodigo()`, `obtenerFactoresArray()`

### `SlaTipoFactor`

Modelo para gestionar factores de tipo:

-   Scopes: `activos()`, `ordenados()`
-   Métodos estáticos: `obtenerFactorPorCodigo()`, `obtenerFactoresArray()`

## Modificaciones al Modelo `Sla`

### Compatibilidad Hacia Atrás

-   Los arrays estáticos `$factoresPrioridadBase` y `$factoresTipoBase` se mantienen como fallback
-   Los métodos existentes siguen funcionando sin cambios

### Métodos Actualizados

-   `obtenerFactorPrioridad()`: Ahora usa primero la BD, fallback a array estático
-   `obtenerFactorTipo()`: Ahora usa primero la BD, fallback a array estático
-   `getFactoresPrioridad()`: Devuelve factores desde BD o array estático
-   `getFactoresTipo()`: Devuelve factores desde BD o array estático

## Recursos de Filament

### `SlaPrioridadFactorResource`

-   Formulario completo para CRUD de factores de prioridad
-   Tabla con filtros y ordenamiento
-   Agrupado en navegación "SLA"

### `SlaTipoFactorResource`

-   Formulario completo para CRUD de factores de tipo
-   Tabla con filtros y ordenamiento
-   Agrupado en navegación "SLA"

## Datos Iniciales

El seeder `SlaFactoresSeeder` pobla las tablas con:

### Factores de Prioridad

-   critico: 0.20 (Crítica - 20% del tiempo)
-   critica: 0.20 (Crítica Alt - 20% del tiempo)
-   urgente: 0.20 (Urgente - 20% del tiempo)
-   alto: 0.50 (Alta - 50% del tiempo)
-   alta: 0.50 (Alta Alt - 50% del tiempo)
-   medio: 1.00 (Media - 100% del tiempo)
-   media: 1.00 (Media Alt - 100% del tiempo)
-   bajo: 1.50 (Baja - 150% del tiempo)
-   baja: 1.50 (Baja Alt - 150% del tiempo)

### Factores de Tipo

-   incidente: 0.60 (Incidente - 60% del tiempo)
-   general: 0.80 (General - 80% del tiempo)
-   requerimiento: 1.20 (Requerimiento - 120% del tiempo)
-   cambio: 1.50 (Cambio - 150% del tiempo)

## Comando de Prueba

Se incluye el comando `test:sla-factores` para verificar el funcionamiento:

```bash
php artisan test:sla-factores
```

## Migración

Para aplicar los cambios:

```bash
# Ejecutar migraciones
php artisan migrate

# Poblar datos iniciales
php artisan db:seed --class=SlaFactoresSeeder
```

## Ventajas

1. **Administrable**: Los factores se pueden gestionar desde la interfaz de Filament
2. **Flexible**: Se pueden crear nuevos factores sin modificar código
3. **Compatible**: Todo el código existente sigue funcionando
4. **Escalable**: Fácil de mantener y extender
5. **Robusto**: Incluye fallbacks en caso de errores de BD

## Uso

El sistema automáticamente usa los factores de la base de datos. Si no están disponibles, usa los valores estáticos como fallback, garantizando que el sistema nunca falle.
