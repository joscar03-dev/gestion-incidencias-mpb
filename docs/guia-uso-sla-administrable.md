# Guía de Uso: Sistema SLA Administrable

## 📋 **Resumen del Sistema**

El sistema SLA administrable permite calcular automáticamente los tiempos de respuesta y resolución para tickets basándose en:

1. **Área del ticket** (cada área tiene un SLA base)
2. **Prioridad del ticket** (aplicada como factor multiplicador)
3. **Tipo de ticket** (aplicado como factor multiplicador)

## 🏗️ **Cómo Funciona**

### Flujo de Cálculo SLA:

```
SLA Final = SLA Base del Área × Factor Prioridad × Factor Tipo
```

**Ejemplo:**

-   SLA Base del Área IT: 120 minutos respuesta, 480 minutos resolución
-   Ticket con prioridad "crítica" (factor 0.20) y tipo "incidente" (factor 0.60)
-   SLA Final: 120 × 0.20 × 0.60 = **14.4 minutos respuesta**, 480 × 0.20 × 0.60 = **57.6 minutos resolución**

## 🎛️ **Administración de Factores**

### Acceso en Filament:

1. **Navegación:** SLA → Factores de Prioridad SLA / Factores de Tipo SLA
2. **Crear/Editar:** Formularios completos con validación
3. **Gestión:** Activar/desactivar, ordenar, editar factores

### Factores de Prioridad Predefinidos:

| Código  | Nombre  | Factor | Descripción                     |
| ------- | ------- | ------ | ------------------------------- |
| critico | Crítica | 0.20   | 20% del tiempo - MUY URGENTE    |
| alto    | Alta    | 0.50   | 50% del tiempo - URGENTE        |
| medio   | Media   | 1.00   | 100% del tiempo - NORMAL        |
| bajo    | Baja    | 1.50   | 150% del tiempo - MENOS URGENTE |

### Factores de Tipo Predefinidos:

| Código        | Nombre        | Factor | Descripción                          |
| ------------- | ------------- | ------ | ------------------------------------ |
| incidente     | Incidente     | 0.60   | 60% del tiempo - Respuesta rápida    |
| general       | General       | 0.80   | 80% del tiempo - Consulta importante |
| requerimiento | Requerimiento | 1.20   | 120% del tiempo - Planificación      |
| cambio        | Cambio        | 1.50   | 150% del tiempo - Requiere análisis  |

## 📝 **Configuración de SLA por Área**

### En el Formulario SLA:

1. **Tiempos Base:** Define tiempo_respuesta y tiempo_resolucion base
2. **Override por Prioridad:** Toggle para activar/desactivar factores dinámicos
3. **Escalamiento:** Configurar escalamiento automático

### Opciones Importantes:

-   **Override Activado:** Los tiempos se ajustan según prioridad y tipo
-   **Override Desactivado:** Todos los tickets usan los mismos tiempos base
-   **Escalamiento:** Configura cuándo escalar tickets automáticamente

## 🎫 **Uso en Tickets**

### Cálculo Automático:

Cuando se crea o actualiza un ticket, el sistema:

1. **Identifica el área** del ticket
2. **Obtiene el SLA** activo del área
3. **Aplica factores** de prioridad y tipo (si override está activo)
4. **Calcula tiempos finales** de respuesta y resolución

### Código de Ejemplo:

```php
// El ticket calcula automáticamente su SLA
$slaInfo = $ticket->calcularSla();

// Resultado ejemplo:
[
    'encontrado' => true,
    'tiempo_respuesta' => 72,  // minutos
    'tiempo_resolucion' => 288, // minutos
    'override_aplicado' => true,
    'factor_prioridad' => 0.5,
    'factor_tipo' => 0.8,
    'factor_combinado' => 0.4
]
```

## 🔧 **Métodos Disponibles**

### En el Modelo Ticket:

```php
// Calcular SLA para el ticket actual
$slaInfo = $ticket->calcularSla();

// Verificar si debe escalar
$debeEscalar = $ticket->debeEscalarAutomaticamente();
```

### En el Modelo Sla:

```php
// Calcular SLA para área específica
$slaInfo = Sla::calcularParaTicket($areaId, $prioridad, $tipo);

// Verificar escalamiento
$debeEscalar = Sla::verificarEscalamiento($areaId, $tiempoTranscurrido, $prioridad, $tipo);

// Obtener factores disponibles
$factoresPrioridad = Sla::getFactoresPrioridad();
$factoresTipo = Sla::getFactoresTipo();
```

## 📊 **Ejemplos Prácticos**

### Ejemplo 1: Ticket Normal

-   **Área:** Soporte IT (120 min respuesta, 480 min resolución)
-   **Prioridad:** media (factor 1.0)
-   **Tipo:** general (factor 0.8)
-   **Resultado:** 96 min respuesta, 384 min resolución

### Ejemplo 2: Emergencia Crítica

-   **Área:** Soporte IT (120 min respuesta, 480 min resolución)
-   **Prioridad:** critica (factor 0.2)
-   **Tipo:** incidente (factor 0.6)
-   **Resultado:** 14.4 min respuesta, 57.6 min resolución

### Ejemplo 3: Cambio Planificado

-   **Área:** Desarrollo (240 min respuesta, 1440 min resolución)
-   **Prioridad:** baja (factor 1.5)
-   **Tipo:** cambio (factor 1.5)
-   **Resultado:** 540 min respuesta, 3240 min resolución

## ⚠️ **Consideraciones Importantes**

### Compatibilidad:

-   **Sistema robusto:** Fallback a valores estáticos si hay problemas con BD
-   **Retrocompatible:** Todo el código existente sigue funcionando
-   **Flexible:** Se pueden modificar factores sin reiniciar la aplicación

### Escalamiento:

-   **Automático:** Basado en tiempo transcurrido y factor combinado
-   **Configurable:** Por área y tipo de SLA
-   **Inteligente:** Considera factores dinámicos para escalar

## 🚀 **Comandos Útiles**

```bash
# Probar el sistema de factores
php artisan test:sla-factores

# Poblar datos iniciales (si es necesario)
php artisan db:seed --class=SlaFactoresSeeder

# Ver todas las migraciones
php artisan migrate:status
```

## 🎯 **Ventajas del Sistema**

1. **Flexible:** Admins pueden ajustar factores sin código
2. **Escalable:** Fácil agregar nuevas prioridades/tipos
3. **Inteligente:** Cálculos automáticos basados en contexto
4. **Robusto:** Manejo de errores y fallbacks
5. **Auditable:** Historial de cambios en BD
6. **Configurable:** Activar/desactivar por área

Este sistema permite una gestión mucho más dinámica y precisa de los SLA, adaptándose automáticamente al contexto de cada ticket.
