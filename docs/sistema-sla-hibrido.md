# 🎯 Guía Completa: Sistema SLA Híbrido

## 📋 Como Funciona el Sistema

### 1. **El Usuario Crea un Ticket**
Cuando un usuario crea un ticket en el sistema, debe seleccionar:
- ✅ **Prioridad**: Crítica, Alta, Media, Baja
- ✅ **Tipo**: Incidente, General, Requerimiento, Cambio
- ✅ **Área**: Se toma automáticamente del área del usuario

### 2. **El Sistema Calcula el SLA Automáticamente**
```php
// Ejemplo: Usuario del área IT crea un ticket
$ticket = new Ticket([
    'titulo' => 'Servidor caído',
    'prioridad' => 'Critica',     // Del formulario del usuario
    'tipo' => 'Incidente',        // Del formulario del usuario
    'area_id' => 1                // Área IT del usuario
]);

// El sistema calcula automáticamente:
$sla = $ticket->calcularSlaEfectivo();

/*
Resultado:
- Tiempo base IT: 60min respuesta, 480min resolución
- Factor prioridad (Crítica): 0.2 (20%)
- Factor tipo (Incidente): 0.6 (60%) 
- Factor combinado: 0.2 × 0.6 = 0.12 (12%)
- Tiempo final: 60×0.12 = 7.2min respuesta, 480×0.12 = 57.6min resolución
*/
```

### 3. **Ejemplos de Cálculo Real**

#### Área IT (Base: 60min resp, 480min resol)
| Prioridad | Tipo | Factor | Respuesta | Resolución |
|-----------|------|--------|-----------|------------|
| Crítica | Incidente | 0.12 | 7.2min | 57.6min |
| Alta | General | 0.40 | 24min | 192min |
| Media | Requerimiento | 1.20 | 72min | 576min |
| Baja | Cambio | 2.25 | 135min | 1080min |

#### Área RRHH (Base: 120min resp, 960min resol)
| Prioridad | Tipo | Factor | Respuesta | Resolución |
|-----------|------|--------|-----------|------------|
| Crítica | Incidente | 0.12 | 14.4min | 115.2min |
| Alta | General | 0.40 | 48min | 384min |
| Media | Requerimiento | 1.20 | 144min | 1152min |
| Baja | Cambio | 2.25 | 270min | 2160min |

### 4. **Como Usar en el Código**

```php
// Método 1: Desde el ticket
$ticket = Ticket::find(1);
$sla = $ticket->calcularSlaEfectivo();

if ($sla['encontrado']) {
    echo "Tiempo de respuesta: {$sla['tiempo_respuesta']} minutos";
    echo "Tiempo de resolución: {$sla['tiempo_resolucion']} minutos";
    echo "Factor aplicado: {$sla['factor_combinado']}";
}

// Método 2: Directamente con los parámetros
$sla = Sla::calcularParaTicket(
    areaId: 1,
    prioridadTicket: 'Critica', 
    tipoTicket: 'Incidente'
);

// Método 3: Verificar escalamiento
$debeEscalar = $ticket->debeEscalarAutomaticamente();
if ($debeEscalar) {
    // Lógica de escalamiento automático
}
```

### 5. **Interfaz de Usuario**

#### Formulario de Creación:
- **Campo Prioridad**: Dropdown con opciones y colores
- **Campo Tipo**: Dropdown con iconos descriptivos
- **SLA Calculado**: Se actualiza en tiempo real cuando cambian prioridad/tipo
- **Preview**: "⏱️ Respuesta: 7.2min | 🔧 Resolución: 57.6min"

#### Tabla de Tickets:
- **Columna Prioridad**: Badge con color e icono
- **Columna Tipo**: Badge con color e icono específico
- **Columna SLA**: Estado actual (OK, Advertencia, Vencido)

### 6. **Factores del Sistema**

#### Por Prioridad:
- 🔴 **Crítica**: 20% (0.2) - Para emergencias
- 🟠 **Alta**: 50% (0.5) - Urgente pero no crítico
- 🟡 **Media**: 100% (1.0) - Normal, tiempo completo
- 🟢 **Baja**: 150% (1.5) - Menos urgente, más tiempo

#### Por Tipo:
- 🚨 **Incidente**: 60% (0.6) - Algo está roto, respuesta rápida
- 💬 **General**: 80% (0.8) - Consulta o soporte general
- 📋 **Requerimiento**: 120% (1.2) - Solicitud nueva, necesita planificación
- ⚙️ **Cambio**: 150% (1.5) - Modificación que requiere análisis

### 7. **Configuración por Área**

Cada área tiene UN solo SLA con tiempos base:
- **Área IT**: 60min respuesta, 480min resolución
- **Área RRHH**: 120min respuesta, 960min resolución
- **Área Finanzas**: 240min respuesta, 1440min resolución

Los factores se aplican automáticamente según prioridad y tipo del ticket.

### 8. **Escalamiento Automático**

El sistema puede escalar automáticamente si:
- El SLA tiene habilitado escalamiento automático
- Ha transcurrido el tiempo de escalamiento configurado
- Se aplican los mismos factores al tiempo de escalamiento

```php
// Verificar si debe escalar
if ($ticket->debeEscalarAutomaticamente()) {
    // Escalar a supervisor o siguiente nivel
}
```

## 🎯 Ventajas del Sistema

1. **Simple para Admins**: Un SLA por área
2. **Flexible para Usuarios**: 16 combinaciones automáticas
3. **Dinámico**: Cálculo en tiempo real
4. **Escalable**: Fácil agregar nuevos tipos/prioridades
5. **Transparente**: El usuario ve el SLA antes de crear el ticket
