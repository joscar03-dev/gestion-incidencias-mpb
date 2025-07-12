# 📊 Módulo ITIL Dashboard

## Descripción

El Módulo ITIL Dashboard es una implementación completa del framework ITIL v4 (Information Technology Infrastructure Library) integrado en el sistema de gestión de incidencias. Proporciona un conjunto comprehensivo de herramientas, métricas, dashboards y reportes que siguen las mejores prácticas de gestión de servicios de TI.

## 🎯 Características Principales

### 📈 Dashboard y Métricas ITIL
- **Dashboard Principal**: Vista consolidada con métricas clave
- **Métricas de Incidentes**: Total, resueltos, escalados, SLA
- **Análisis de Disponibilidad**: Uptime, downtime, MTTR, MTBF
- **Satisfacción del Usuario**: Puntuaciones y encuestas
- **Carga de Trabajo**: Distribución entre técnicos

### 📊 Visualizaciones Avanzadas
- **Gráficos de Tendencias**: Análisis temporal de incidentes
- **Distribución por Categorías**: Pie charts y bar charts
- **Cumplimiento SLA**: Indicadores radiales
- **Análisis de Performance**: Métricas comparativas

### 📋 Categorización ITIL
- **Gestión de Incidentes**: Hardware, Software, Red, Seguridad
- **Solicitudes de Servicio**: Accesos, Instalaciones, Información
- **Gestión de Cambios**: Normal, Estándar, Emergencia
- **Niveles de Servicio**: Oro, Plata, Bronce

### 📄 Sistema de Reportes
- **Exportación Excel**: Múltiples hojas con datos detallados
- **Reportes PDF**: Documentos comprehensivos profesionales
- **Reportes Programables**: Generación automática vía comando
- **Filtros Avanzados**: Por fecha, tipo, estado, prioridad

## 🏗️ Arquitectura del Sistema

### Modelos
- `ItilDashboard`: Modelo principal con métrica y análisis
- `Ticket`: Modelo extendido con clasificación ITIL

### Recursos Filament
- `ItilDashboardResource`: Resource principal con tabla y acciones
- **Páginas Especializadas**:
  - `ListItilDashboard`: Lista principal con filtros
  - `ItilMetrics`: Métricas avanzadas con widgets
  - `ItilAnalytics`: Analytics y tendencias
  - `ItilServiceCatalog`: Catálogo de servicios

### Widgets
- `ItilOverviewWidget`: Métricas principales en el dashboard
- `ItilCategoryDistributionWidget`: Distribución por categorías
- `ItilTrendAnalysisWidget`: Análisis de tendencias
- `ItilWorkloadTableWidget`: Tabla de carga de trabajo

### Exports
- `ItilReportExport`: Exportación multi-hoja a Excel
- **Hojas Incluidas**:
  - Tickets ITIL
  - Métricas ITIL
  - Distribución por Categoría
  - Análisis SLA
  - Carga de Trabajo

## 🚀 Instalación y Configuración

### 1. Archivos Principales
```
app/
├── Models/ItilDashboard.php
├── Filament/Resources/ItilDashboardResource.php
├── Filament/Resources/ItilDashboardResource/Pages/
├── Filament/Widgets/
├── Exports/ItilReportExport.php
└── Console/Commands/GenerateItilReport.php

resources/views/
├── filament/resources/itil-dashboard-resource/pages/
└── exports/

config/itil.php
```

### 2. Configuración
El sistema utiliza el archivo `config/itil.php` para configurar:
- Tiempos SLA por prioridad
- Configuración de horarios laborales
- Umbrales de escalamiento
- Configuración de métricas y KPIs

### 3. Comandos Artisan
```bash
# Generar reporte ITIL básico
php artisan itil:report

# Generar reporte con filtros
php artisan itil:report --type=sla --from=2024-01-01 --to=2024-12-31

# Generar reporte específico
php artisan itil:report --type=metricas --format=excel --output=reporte-mensual.xlsx
```

## 📊 Métricas ITIL Implementadas

### 🎯 KPIs Principales
1. **Tasa de Resolución**: Porcentaje de tickets resueltos
2. **Cumplimiento SLA**: Porcentaje de tickets dentro del SLA
3. **Tasa de Escalamiento**: Porcentaje de tickets escalados
4. **Disponibilidad del Servicio**: Uptime porcentual
5. **MTTR**: Mean Time To Resolve (Tiempo promedio de resolución)
6. **MTBF**: Mean Time Between Failures

### 📈 Análisis de Tendencias
- Incidentes creados vs resueltos por día
- Tendencias de escalamiento
- Patrones de carga de trabajo
- Análisis estacional de incidentes

### 🏷️ Categorización ITIL
- **Hardware**: Servidores, equipos de red, estaciones de trabajo
- **Software**: Aplicaciones, sistemas operativos, licencias
- **Red**: Conectividad, VPN, firewall
- **Seguridad**: Accesos, vulnerabilidades, políticas
- **Datos**: Backup, recuperación, integridad
- **Disponibilidad**: Servicios críticos, caídas

## 🎨 Interfaz de Usuario

### Dashboard Principal
- Cards con métricas principales
- Widgets interactivos con ApexCharts
- Filtros dinámicos por período
- Navegación intuitiva entre secciones

### Páginas Especializadas
1. **Métricas ITIL**: Vista detallada con gráficos avanzados
2. **Analytics**: Análisis de tendencias y patrones
3. **Catálogo de Servicios**: Marco de referencia ITIL

### Exportación de Datos
- **Excel Multi-hoja**: Datos estructurados listos para análisis
- **PDF Comprehensivo**: Reportes ejecutivos profesionales
- **Filtros Flexibles**: Personalización de reportes

## 🔧 Configuración Avanzada

### SLA Personalizado
```php
// config/itil.php
'sla' => [
    'priority_times' => [
        'critica' => [
            'response_minutes' => 15,
            'resolution_hours' => 2,
        ],
        // ... más configuraciones
    ],
],
```

### Métricas Personalizadas
```php
// Extender ItilDashboard con nuevas métricas
public static function getCustomMetrics()
{
    // Implementar métricas específicas
}
```

## 📋 Casos de Uso

### Para Gerentes de TI
- Dashboard ejecutivo con KPIs principales
- Reportes automáticos de cumplimiento
- Análisis de carga de trabajo del equipo
- Métricas de satisfacción del usuario

### Para Supervisores
- Monitoreo en tiempo real de SLA
- Distribución de carga de trabajo
- Identificación de cuellos de botella
- Reportes de escalamiento

### Para Técnicos
- Vista de tickets asignados con priorización ITIL
- Métricas individuales de performance
- Acceso a catálogo de servicios
- Herramientas de análisis de incidentes

### Para Auditores
- Reportes de cumplimiento detallados
- Trazabilidad completa de incidentes
- Métricas de disponibilidad
- Documentación de procesos ITIL

## 🔍 Análisis y Reportes

### Tipos de Reportes Disponibles
1. **General**: Vista comprehensiva del estado ITIL
2. **SLA**: Análisis detallado de cumplimiento
3. **Métricas**: KPIs y tendencias específicas
4. **Tendencias**: Análisis temporal avanzado

### Formatos de Exportación
- **Excel**: Múltiples hojas con datos estructurados
- **PDF**: Reportes ejecutivos formateados
- **Programático**: Via comando artisan para automatización

## 🚀 Mejores Prácticas

### Configuración Inicial
1. Definir SLAs apropiados para el negocio
2. Configurar categorías específicas del entorno
3. Establecer umbrales de escalamiento
4. Configurar notificaciones automáticas

### Uso Operacional
1. Revisar dashboards diariamente
2. Generar reportes semanales/mensuales
3. Analizar tendencias para mejora continua
4. Mantener catálogo de servicios actualizado

### Optimización Continua
1. Monitorear KPIs regularmente
2. Ajustar SLAs basado en capacidad real
3. Analizar patrones de escalamiento
4. Implementar mejoras basadas en datos

## 📝 Próximas Mejoras

- [ ] Integración con herramientas de monitoreo
- [ ] Análisis predictivo con ML
- [ ] API REST para integración externa
- [ ] Dashboard personalizable por usuario
- [ ] Alertas proactivas avanzadas
- [ ] Integración con CMDB
- [ ] Workflow automatizado de cambios

## 📞 Soporte

Para soporte técnico del módulo ITIL:
- Documentación interna del proyecto
- Issues en el repositorio
- Contacto con el equipo de desarrollo

---

**Versión ITIL**: v4.0
**Última Actualización**: Diciembre 2024
**Compatibilidad**: Laravel 11, Filament 3.x
