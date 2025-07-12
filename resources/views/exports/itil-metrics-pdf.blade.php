<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Métricas ITIL</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            line-height: 1.4;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .header h1 {
            color: #1f2937;
            margin: 0;
            font-size: 24px;
        }

        .header .subtitle {
            color: #6b7280;
            margin: 5px 0;
            font-size: 14px;
        }

        .info-box {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .metrics-grid {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }

        .metric-card {
            display: table-cell;
            width: 25%;
            text-align: center;
            padding: 15px;
            border: 1px solid #e5e7eb;
            background-color: #fff;
        }

        .metric-card h3 {
            margin: 0 0 10px 0;
            font-size: 14px;
            color: #6b7280;
        }

        .metric-card .value {
            font-size: 28px;
            font-weight: bold;
            margin: 0;
        }

        .metric-card.blue .value { color: #3b82f6; }
        .metric-card.green .value { color: #10b981; }
        .metric-card.purple .value { color: #8b5cf6; }
        .metric-card.indigo .value { color: #6366f1; }

        .section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }

        .section-title {
            font-size: 18px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 15px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 5px;
        }

        .workload-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .workload-table th,
        .workload-table td {
            border: 1px solid #e5e7eb;
            padding: 8px;
            text-align: left;
            font-size: 12px;
        }

        .workload-table th {
            background-color: #f9fafb;
            font-weight: bold;
        }

        .resolution-metrics {
            display: table;
            width: 100%;
        }

        .resolution-item {
            display: table-row;
        }

        .resolution-label,
        .resolution-value {
            display: table-cell;
            padding: 8px;
            border-bottom: 1px solid #e5e7eb;
        }

        .resolution-label {
            font-weight: 500;
            color: #6b7280;
        }

        .resolution-value {
            text-align: right;
            font-weight: bold;
        }

        .satisfaction-grid {
            display: table;
            width: 100%;
        }

        .satisfaction-item {
            display: table-cell;
            width: 25%;
            text-align: center;
            padding: 15px;
            border: 1px solid #e5e7eb;
        }

        .satisfaction-value {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .satisfaction-label {
            font-size: 12px;
            color: #6b7280;
        }

        .satisfaction-item.blue .satisfaction-value { color: #3b82f6; }
        .satisfaction-item.green .satisfaction-value { color: #10b981; }
        .satisfaction-item.purple .satisfaction-value { color: #8b5cf6; }
        .satisfaction-item.indigo .satisfaction-value { color: #6366f1; }

        .footer {
            position: fixed;
            bottom: 20px;
            right: 20px;
            font-size: 10px;
            color: #9ca3af;
        }

        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte de Métricas ITIL</h1>
        <div class="subtitle">Sistema de Gestión de Incidencias</div>
        <div class="subtitle">Generado el {{ $generated_at }} por {{ $generated_by }}</div>
        <div class="subtitle">Período: {{ $period }}</div>
    </div>

    <div class="info-box">
        <strong>Resumen Ejecutivo:</strong> Este reporte contiene las métricas clave del marco ITIL v4 para el período especificado,
        incluyendo análisis de incidentes, cumplimiento de SLA, carga de trabajo del equipo y satisfacción del usuario.
    </div>

    <!-- Métricas Principales -->
    <div class="section">
        <div class="section-title">Métricas Principales</div>
        <div class="metrics-grid">
            <div class="metric-card blue">
                <h3>Total Incidentes</h3>
                <div class="value">{{ $incident_metrics['total_incidents'] ?? 0 }}</div>
            </div>
            <div class="metric-card green">
                <h3>Cumplimiento SLA</h3>
                <div class="value">{{ $incident_metrics['sla_compliance'] ?? 0 }}%</div>
            </div>
            <div class="metric-card purple">
                <h3>Tasa Resolución</h3>
                <div class="value">{{ $incident_metrics['resolution_rate'] ?? 0 }}%</div>
            </div>
            <div class="metric-card indigo">
                <h3>Disponibilidad</h3>
                <div class="value">{{ $service_availability['availability_percentage'] ?? 0 }}%</div>
            </div>
        </div>
    </div>

    <!-- Distribución de Incidentes -->
    <div class="section">
        <div class="section-title">Distribución de Incidentes</div>
        <div class="resolution-metrics">
            <div class="resolution-item">
                <div class="resolution-label">Incidentes Abiertos:</div>
                <div class="resolution-value">{{ $incident_metrics['open_incidents'] ?? 0 }}</div>
            </div>
            <div class="resolution-item">
                <div class="resolution-label">Incidentes Resueltos:</div>
                <div class="resolution-value">{{ $incident_metrics['resolved_incidents'] ?? 0 }}</div>
            </div>
            <div class="resolution-item">
                <div class="resolution-label">Incidentes Escalados:</div>
                <div class="resolution-value">{{ $incident_metrics['escalated_incidents'] ?? 0 }}</div>
            </div>
            @if(isset($incident_metrics['cancelled_incidents']))
            <div class="resolution-item">
                <div class="resolution-label">Incidentes Cancelados:</div>
                <div class="resolution-value">{{ $incident_metrics['cancelled_incidents'] }}</div>
            </div>
            @endif
        </div>
    </div>

    <!-- Métricas de Tiempo de Resolución -->
    <div class="section">
        <div class="section-title">Métricas de Tiempo de Resolución</div>
        <div class="resolution-metrics">
            <div class="resolution-item">
                <div class="resolution-label">Tiempo Promedio:</div>
                <div class="resolution-value">{{ round($resolution_metrics['mean_time_to_resolve'] ?? 0, 2) }} horas</div>
            </div>
            <div class="resolution-item">
                <div class="resolution-label">Tiempo Mediano:</div>
                <div class="resolution-value">{{ round($resolution_metrics['median_time_to_resolve'] ?? 0, 2) }} horas</div>
            </div>
            <div class="resolution-item">
                <div class="resolution-label">Tiempo Mínimo:</div>
                <div class="resolution-value">{{ round($resolution_metrics['min_time_to_resolve'] ?? 0, 2) }} horas</div>
            </div>
            <div class="resolution-item">
                <div class="resolution-label">Tiempo Máximo:</div>
                <div class="resolution-value">{{ round($resolution_metrics['max_time_to_resolve'] ?? 0, 2) }} horas</div>
            </div>
        </div>
    </div>

    <div class="page-break"></div>

    <!-- Carga de Trabajo del Equipo -->
    <div class="section">
        <div class="section-title">Carga de Trabajo del Equipo</div>
        <table class="workload-table">
            <thead>
                <tr>
                    <th>Técnico</th>
                    <th>Tickets Abiertos</th>
                    <th>Tickets Resueltos</th>
                    <th>Tickets Escalados</th>
                    <th>Total Asignados</th>
                    <th>Tasa Resolución</th>
                    <th>Tasa Escalación</th>
                </tr>
            </thead>
            <tbody>
                @foreach($workload_analysis as $analyst)
                <tr>
                    <td>{{ $analyst['user_name'] }}</td>
                    <td style="text-align: center;">{{ $analyst['open_tickets'] }}</td>
                    <td style="text-align: center;">{{ $analyst['resolved_tickets'] }}</td>
                    <td style="text-align: center;">{{ $analyst['escalated_tickets'] ?? 0 }}</td>
                    <td style="text-align: center;">{{ $analyst['total_tickets'] }}</td>
                    <td style="text-align: center;">{{ $analyst['resolution_rate'] }}%</td>
                    <td style="text-align: center;">{{ $analyst['escalation_rate'] ?? 0 }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Distribución por Categorías -->
    <div class="section">
        <div class="section-title">Top Categorías de Incidentes</div>
        <table class="workload-table">
            <thead>
                <tr>
                    <th>Categoría</th>
                    <th>Total de Tickets</th>
                </tr>
            </thead>
            <tbody>
                @foreach($category_distribution as $key => $category)
                <tr>
                    <td>{{ $category['name'] ?? 'N/A' }}</td>
                    <td style="text-align: center;">{{ $category['count'] ?? 0 }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Métricas de Satisfacción -->
    <div class="section">
        <div class="section-title">Métricas de Satisfacción del Usuario</div>
        <div class="satisfaction-grid">
            <div class="satisfaction-item blue">
                <div class="satisfaction-value">{{ $user_satisfaction['satisfaction_score'] ?? 0 }}%</div>
                <div class="satisfaction-label">Puntuación General</div>
            </div>
            <div class="satisfaction-item green">
                <div class="satisfaction-value">{{ $user_satisfaction['total_surveys'] ?? 0 }}</div>
                <div class="satisfaction-label">Total Encuestas</div>
            </div>
            <div class="satisfaction-item purple">
                <div class="satisfaction-value">{{ $user_satisfaction['response_rate'] ?? 0 }}%</div>
                <div class="satisfaction-label">Tasa Respuesta</div>
            </div>
            <div class="satisfaction-item indigo">
                <div class="satisfaction-value">{{ $user_satisfaction['net_promoter_score'] ?? 0 }}</div>
                <div class="satisfaction-label">Net Promoter Score</div>
            </div>
        </div>
    </div>

    <!-- Conclusiones -->
    <div class="section">
        <div class="section-title">Conclusiones y Recomendaciones</div>
        <div style="font-size: 12px; line-height: 1.6;">
            <p><strong>Análisis del Rendimiento:</strong></p>
            <ul>
                <li>El cumplimiento de SLA está en {{ $incident_metrics['sla_compliance'] ?? 0 }}%
                    @if(($incident_metrics['sla_compliance'] ?? 0) >= 95)
                        - Excelente rendimiento
                    @elseif(($incident_metrics['sla_compliance'] ?? 0) >= 90)
                        - Buen rendimiento, pero se puede mejorar
                    @else
                        - Requiere atención inmediata
                    @endif
                </li>
                <li>La tasa de resolución general es del {{ $incident_metrics['resolution_rate'] ?? 0 }}%</li>
                <li>Se procesaron {{ $incident_metrics['total_incidents'] ?? 0 }} incidentes en el período</li>
                <li>Tiempo promedio de resolución: {{ round($resolution_metrics['mean_time_to_resolve'] ?? 0, 2) }} horas</li>
            </ul>

            <p><strong>Recomendaciones:</strong></p>
            <ul>
                @if(($incident_metrics['sla_compliance'] ?? 0) < 90)
                <li>Implementar mejoras en los procesos para aumentar el cumplimiento de SLA</li>
                @endif
                @if(($incident_metrics['escalated_incidents'] ?? 0) > 0)
                <li>Revisar los casos escalados para identificar patrones y áreas de mejora</li>
                @endif
                <li>Continuar monitoreando las métricas clave semanalmente</li>
                <li>Evaluar la carga de trabajo del equipo para optimizar la distribución</li>
            </ul>
        </div>
    </div>

    <div class="footer">
        Generado automáticamente por el Sistema de Gestión de Incidencias - {{ now()->format('d/m/Y H:i:s') }}
    </div>
</body>
</html>
