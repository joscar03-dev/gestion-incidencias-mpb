# Guía de Factores SLA Administrables

## Resumen del Sistema

El sistema de gestión de incidencias ahora cuenta con **factores SLA administrables** a través de la interfaz administrativa, manteniendo compatibilidad total con el sistema existente.

## Características Principales

### 🔧 **Sistema Híbrido**

-   **Base de datos primaria**: Los factores se obtienen primero desde las tablas `sla_prioridad_factores` y `sla_tipo_factores`
-   **Fallback estático**: Si no se encuentra en BD, se usa el array estático como respaldo
-   **Sin interrupciones**: El sistema existente continúa funcionando normalmente

### 🎯 **Compatibilidad Automática**

-   **Conversión automática**: Los valores con mayúscula ('Critica', 'Incidente') se buscan automáticamente en minúscula ('critica', 'incidente')
-   **Sin cambios de código**: Los tickets existentes funcionan sin modificaciones
-   **Mantiene lógica**: Todas las relaciones y cálculos SLA permanecen iguales

## Gestión Administrativa

### Acceso a la Administración

1. Ir al panel administrativo: `/admin`
2. En el menú lateral, encontrarás:
    - **"Factores SLA Prioridad"** → Gestión de factores por prioridad
    - **"Factores SLA Tipo"** → Gestión de factores por tipo

### Factores de Prioridad

**Ubicación**: Admin → Factores SLA Prioridad

| Código    | Nombre  | Factor | Descripción                              |
| --------- | ------- | ------ | ---------------------------------------- |
| `critico` | Crítica | 0.10   | Prioridad más alta - 10% del tiempo base |
| `alto`    | Alta    | 0.50   | Prioridad alta - 50% del tiempo base     |
| `medio`   | Media   | 1.00   | Prioridad normal - 100% del tiempo base  |
| `bajo`    | Baja    | 1.50   | Prioridad baja - 150% del tiempo base    |

**Códigos adicionales de compatibilidad**:

-   `critica` → mapea a `critico`
-   `alta` → mapea a `alto`
-   `media` → mapea a `medio`
-   `baja` → mapea a `bajo`

### Factores de Tipo

**Ubicación**: Admin → Factores SLA Tipo

| Código          | Nombre        | Factor | Descripción                          |
| --------------- | ------------- | ------ | ------------------------------------ |
| `incidente`     | Incidente     | 0.60   | Problema que afecta el servicio      |
| `general`       | General       | 0.80   | Consulta o solicitud general         |
| `requerimiento` | Requerimiento | 1.20   | Solicitud de nueva funcionalidad     |
| `cambio`        | Cambio        | 1.50   | Solicitud de cambio de configuración |

## Mapeo Automático

### Desde el Modelo Ticket

```php
// El modelo Ticket define:
const PRIORIDAD = [
    'Critica' => 'Critica',   // → busca 'critico'
    'Alta' => 'Alta',         // → busca 'alto'
    'Media' => 'Media',       // → busca 'medio'
    'Baja' => 'Baja'          // → busca 'bajo'
];

const TIPOS = [
    'Incidente' => 'Incidente',         // → busca 'incidente'
    'General' => 'General',             // → busca 'general'
    'Requerimiento' => 'Requerimiento', // → busca 'requerimiento'
    'Cambio' => 'Cambio'                // → busca 'cambio'
];
```

### Proceso de Búsqueda

1. **Búsqueda exacta**: Se busca el código tal como viene ('Critica')
2. **Búsqueda en minúscula**: Si no encuentra, busca en minúscula ('critica')
3. **Fallback estático**: Si no está en BD, usa el array estático
4. **Valor por defecto**: Si no existe, retorna 1.0

## Cálculo del SLA

### Fórmula

```
tiempo_sla = tiempo_base × factor_prioridad × factor_tipo
```

### Ejemplos

```php
// Ejemplo 1: Ticket Crítico + Incidente
// Factor prioridad: 0.10, Factor tipo: 0.60
// SLA = tiempo_base × 0.10 × 0.60 = tiempo_base × 0.06

// Ejemplo 2: Ticket Medio + General
// Factor prioridad: 1.00, Factor tipo: 0.80
// SLA = tiempo_base × 1.00 × 0.80 = tiempo_base × 0.80
```

## Comandos de Prueba

### Verificar Factores

```bash
php artisan test:sla-factores
```

### Verificar en Tinker

```bash
php artisan tinker

# Probar factores individuales
App\Models\SlaPrioridadFactor::obtenerFactorPorCodigo('critico');
App\Models\SlaTipoFactor::obtenerFactorPorCodigo('incidente');

# Probar cálculo completo
App\Models\Sla::calcularSla('Critica', 'Incidente');
```

## Administración de Factores

### Agregar Nuevo Factor de Prioridad

1. Ir a Admin → Factores SLA Prioridad
2. Clic en "Crear"
3. Llenar campos:
    - **Código**: Identificador único (ej: 'urgente')
    - **Nombre**: Nombre descriptivo (ej: 'Urgente')
    - **Descripción**: Explicación del factor
    - **Factor**: Multiplicador decimal (ej: 0.30)
    - **Orden**: Posición en listados
    - **Activo**: Habilitado/Deshabilitado

### Modificar Factor Existente

1. Ir a Admin → Factores SLA Prioridad/Tipo
2. Clic en el factor a editar
3. Modificar valores necesarios
4. Guardar cambios

### Desactivar Factor

-   Cambiar el campo "Activo" a NO
-   El factor permanece en BD pero no se usa en cálculos
-   Se mantiene compatibilidad con registros históricos

## Migración y Seeding

### Estructura de Tablas

```sql
-- Tabla sla_prioridad_factores
id, codigo (unique), nombre, descripcion, factor, orden, activo, timestamps

-- Tabla sla_tipo_factores
id, codigo (unique), nombre, descripcion, factor, orden, activo, timestamps
```

### Datos Iniciales

Los factores base se cargan automáticamente con:

```bash
php artisan db:seed --class=SlaFactoresSeeder
```

## Consideraciones Técnicas

### Compatibilidad

-   ✅ **Tickets existentes**: Funcionan sin cambios
-   ✅ **APIs existentes**: Mantienen misma respuesta
-   ✅ **Cálculos SLA**: Lógica idéntica
-   ✅ **Reportes**: No requieren modificación

### Rendimiento

-   **Cache automático**: Los factores se cachean en memoria durante la ejecución
-   **Consultas optimizadas**: Una sola consulta por tipo de factor
-   **Fallback rápido**: Arrays estáticos como respaldo inmediato

### Seguridad

-   **Validaciones**: Códigos únicos, factores numéricos válidos
-   **Permisos**: Solo administradores pueden modificar factores
-   **Auditoría**: Timestamps automáticos en cambios

## Troubleshooting

### Problema: Factor no se encuentra

```php
// Verificar si existe en BD
App\Models\SlaPrioridadFactor::where('codigo', 'critico')->first();

// Verificar si está activo
App\Models\SlaPrioridadFactor::activos()->where('codigo', 'critico')->first();
```

### Problema: Cálculo incorrecto

```php
// Debug paso a paso
$prioridad = 'Critica';
$tipo = 'Incidente';

$factorP = App\Models\Sla::obtenerFactorPrioridad($prioridad);
$factorT = App\Models\Sla::obtenerFactorTipo($tipo);

echo "Prioridad: {$prioridad} → Factor: {$factorP}";
echo "Tipo: {$tipo} → Factor: {$factorT}";
```

### Resetear a Valores por Defecto

```bash
# Volver a ejecutar seeder
php artisan db:seed --class=SlaFactoresSeeder
```

## Conclusión

El sistema de factores SLA administrables proporciona flexibilidad total para ajustar los cálculos de SLA desde la interfaz administrativa, mientras mantiene 100% de compatibilidad con el sistema existente. Los administradores pueden ahora:

-   ✅ Modificar factores sin tocar código
-   ✅ Agregar nuevos tipos de prioridad o tipo
-   ✅ Deshabilitar factores temporalmente
-   ✅ Mantener histórico de cambios
-   ✅ Usar interfaz visual intuitiva

El sistema híbrido garantiza que nunca falle el cálculo de SLA, siempre teniendo el respaldo de los valores estáticos predefinidos.
