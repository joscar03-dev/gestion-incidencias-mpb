<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte Comprehensivo ITIL</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #0066cc;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #0066cc;
            margin: 0;
            font-size: 24px;
        }
        .header p {
            color: #666;
            margin: 5px 0;
        }
        .section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        .section-title {
            background: #0066cc;
            color: white;
            padding: 10px;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 15px;
        }
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }
        .metric-box {
            border: 1px solid #ddd;
            padding: 15px;
            text-align: center;
            background: #f9f9f9;
        }
        .metric-value {
            font-size: 24px;
            font-weight: bold;
            color: #0066cc;
            margin-bottom: 5px;
        }
        .metric-label {
            color: #666;
            font-size: 11px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .status-badge {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
            color: white;
        }
        .status-abierto { background-color: #2196F3; }
        .status-en-progreso { background-color: #FF9800; }
        .status-cerrado { background-color: #4CAF50; }
        .status-escalado { background-color: #F44336; }
        .priority-critica { background-color: #F44336; }
        .priority-alta { background-color: #FF9800; }
        .priority-media { background-color: #2196F3; }
        .priority-baja { background-color: #9E9E9E; }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10px;
            color: #666;
            padding: 10px;
            border-top: 1px solid #ddd;
        }
        .chart-placeholder {
            height: 200px;
            border: 2px dashed #ddd;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #999;
            margin-bottom: 20px;
        }
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📊 Reporte Comprehensivo ITIL</h1>
        <p>Análisis detallado del sistema de gestión de servicios de TI</p>
        <p><strong>Tipo de Reporte:</strong> {{ ucfirst($tipo_reporte) }}</p>
        <p><strong>Período:</strong> 
            @if($fecha_desde && $fecha_hasta)
                {{ \Carbon\Carbon::parse($fecha_desde)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($fecha_hasta)->format('d/m/Y') }}
            @else
                Todos los registros
            @endif
        </p>
        <p><strong>Generado:</strong> {{ $generated_at->format('d/m/Y H:i:s') }}</p>
    </div>

    @if($tipo_reporte === 'general' || $tipo_reporte === 'metricas')
    <!-- Sección de Métricas Principales -->
    <div class="section">
        <div class="section-title">📈 Métricas Principales ITIL</div>
        <div class="metrics-grid">
            <div class="metric-box">
                <div class="metric-value">{{ $metrics['total_incidents'] }}</div>
                <div class="metric-label">Total Incidentes</div>
            </div>
            <div class="metric-box">
                <div class="metric-value">{{ $metrics['resolution_rate'] }}%</div>
                <div class="metric-label">Tasa de Resolución</div>
            </div>
            <div class="metric-box">
                <div class="metric-value">{{ $metrics['sla_compliance'] }}%</div>
                <div class="metric-label">Cumplimiento SLA</div>
            </div>
            <div class="metric-box">
                <div class="metric-value">{{ $service_availability['availability_percentage'] }}%</div>
                <div class="metric-label">Disponibilidad</div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Métrica</th>
                    <th>Valor</th>
                    <th>Descripción</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Incidentes Abiertos</td>
                    <td>{{ $metrics['open_incidents'] }}</td>
                    <td>Incidentes pendientes de resolución</td>
                </tr>
                <tr>
                    <td>Incidentes Escalados</td>
                    <td>{{ $metrics['escalated_incidents'] }}</td>
                    <td>Incidentes que requirieron escalamiento</td>
                </tr>
                <tr>
                    <td>SLA Incumplidos</td>
                    <td>{{ $metrics['sla_breached'] }}</td>
                    <td>Incidentes que excedieron el tiempo acordado</td>
                </tr>
                <tr>
                    <td>Tiempo Promedio Resolución</td>
                    <td>{{ round($resolution_metrics['mean_time_to_resolve'] ?? 0, 2) }} horas</td>
                    <td>MTTR - Mean Time To Resolve</td>
                </tr>
                <tr>
                    <td>Tiempo Mediano Resolución</td>
                    <td>{{ round($resolution_metrics['median_time_to_resolve'] ?? 0, 2) }} horas</td>
                    <td>Tiempo mediano de resolución</td>
                </tr>
            </tbody>
        </table>
    </div>
    @endif

    @if($tipo_reporte === 'general' || $tipo_reporte === 'sla')
    <!-- Sección de Análisis SLA -->
    <div class="section page-break">
        <div class="section-title">⏱️ Análisis de Niveles de Servicio (SLA)</div>
        
        <div class="metrics-grid">
            <div class="metric-box">
                <div class="metric-value">{{ $service_availability['availability_percentage'] }}%</div>
                <div class="metric-label">Disponibilidad del Servicio</div>
            </div>
            <div class="metric-box">
                <div class="metric-value">{{ round($service_availability['mttr'], 2) }}h</div>
                <div class="metric-label">MTTR</div>
            </div>
            <div class="metric-box">
                <div class="metric-value">{{ $service_availability['mtbf'] }}h</div>
                <div class="metric-label">MTBF</div>
            </div>
            <div class="metric-box">
                <div class="metric-value">{{ $user_satisfaction['satisfaction_score'] }}%</div>
                <div class="metric-label">Satisfacción Usuario</div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Prioridad</th>
                    <th>SLA Objetivo (hrs)</th>
                    <th>Tickets Totales</th>
                    <th>Cumplidos</th>
                    <th>Vencidos</th>
                    <th>% Cumplimiento</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $priorities = ['Critica' => 2, 'Alta' => 4, 'Media' => 24, 'Baja' => 72];
                @endphp
                @foreach($priorities as $priority => $sla_hours)
                    @php
                        $priority_tickets = $tickets->where('prioridad', $priority);
                        $total = $priority_tickets->count();
                        $vencidos = $priority_tickets->where('sla_vencido', true)->count();
                        $cumplidos = $total - $vencidos;
                        $percentage = $total > 0 ? round(($cumplidos / $total) * 100, 2) : 100;
                    @endphp
                    <tr>
                        <td><span class="status-badge priority-{{ strtolower($priority) }}">{{ $priority }}</span></td>
                        <td>{{ $sla_hours }}</td>
                        <td>{{ $total }}</td>
                        <td>{{ $cumplidos }}</td>
                        <td>{{ $vencidos }}</td>
                        <td>{{ $percentage }}%</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if($tipo_reporte === 'general' || $tipo_reporte === 'tendencias')
    <!-- Sección de Análisis de Tendencias -->
    <div class="section page-break">
        <div class="section-title">📊 Análisis de Tendencias</div>
        
        <div class="chart-placeholder">
            <div style="text-align: center;">
                <h3>Gráfico de Tendencias de Incidentes</h3>
                <p>{{ count($trend_analysis) }} días de datos analizados</p>
                <p>Promedio diario: {{ round(collect($trend_analysis)->avg('incidents_created'), 1) }} incidentes creados</p>
                <p>Promedio resolución: {{ round(collect($trend_analysis)->avg('incidents_resolved'), 1) }} incidentes resueltos</p>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Creados</th>
                    <th>Resueltos</th>
                    <th>Escalados</th>
                    <th>Balance</th>
                </tr>
            </thead>
            <tbody>
                @foreach(array_slice($trend_analysis, -10) as $trend)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($trend['date'])->format('d/m/Y') }}</td>
                        <td>{{ $trend['incidents_created'] }}</td>
                        <td>{{ $trend['incidents_resolved'] }}</td>
                        <td>{{ $trend['incidents_escalated'] }}</td>
                        <td style="color: {{ ($trend['incidents_resolved'] - $trend['incidents_created']) >= 0 ? 'green' : 'red' }}">
                            {{ $trend['incidents_resolved'] - $trend['incidents_created'] }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Sección de Distribución por Categorías -->
    <div class="section page-break">
        <div class="section-title">🏷️ Distribución por Categorías ITIL</div>
        
        <table>
            <thead>
                <tr>
                    <th>Categoría</th>
                    <th>Cantidad de Tickets</th>
                    <th>Porcentaje</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $total_category_tickets = array_sum(array_column($category_distribution, 'count'));
                @endphp
                @foreach($category_distribution as $key => $category)
                    @php
                        $percentage = $total_category_tickets > 0 ? round(($category['count'] / $total_category_tickets) * 100, 2) : 0;
                    @endphp
                    <tr>
                        <td>{{ $category['name'] }}</td>
                        <td>{{ $category['count'] }}</td>
                        <td>{{ $percentage }}%</td>
                        <td>
                            @if($percentage > 20)
                                <span style="color: red;">Alto volumen</span>
                            @elseif($percentage > 10)
                                <span style="color: orange;">Volumen medio</span>
                            @else
                                <span style="color: green;">Volumen normal</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Sección de Análisis de Carga de Trabajo -->
    <div class="section page-break">
        <div class="section-title">👥 Análisis de Carga de Trabajo del Equipo</div>
        
        <table>
            <thead>
                <tr>
                    <th>Técnico</th>
                    <th>Tickets Abiertos</th>
                    <th>Total Asignados</th>
                    <th>Resueltos</th>
                    <th>Tasa Resolución</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($workload_analysis as $analyst)
                    <tr>
                        <td>{{ $analyst['user_name'] }}</td>
                        <td>{{ $analyst['open_tickets'] }}</td>
                        <td>{{ $analyst['total_tickets'] }}</td>
                        <td>{{ $analyst['resolved_tickets'] }}</td>
                        <td>{{ $analyst['resolution_rate'] }}%</td>
                        <td>
                            @if($analyst['resolution_rate'] >= 80)
                                <span style="color: green;">Excelente</span>
                            @elseif($analyst['resolution_rate'] >= 60)
                                <span style="color: orange;">Bueno</span>
                            @else
                                <span style="color: red;">Necesita mejora</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Sección de Resumen de Tickets -->
    @if($tickets->count() > 0)
    <div class="section page-break">
        <div class="section-title">🎫 Resumen de Tickets (Últimos {{ $tickets->count() }} registros)</div>
        
        <table style="font-size: 10px;">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Tipo</th>
                    <th>Prioridad</th>
                    <th>Estado</th>
                    <th>Asignado</th>
                    <th>Creado</th>
                    <th>SLA</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tickets->take(50) as $ticket)
                    <tr>
                        <td>{{ $ticket->id }}</td>
                        <td style="max-width: 150px; overflow: hidden; text-overflow: ellipsis;">
                            {{ substr($ticket->titulo, 0, 30) }}{{ strlen($ticket->titulo) > 30 ? '...' : '' }}
                        </td>
                        <td><span class="status-badge">{{ $ticket->tipo }}</span></td>
                        <td><span class="status-badge priority-{{ strtolower($ticket->prioridad) }}">{{ $ticket->prioridad }}</span></td>
                        <td><span class="status-badge status-{{ strtolower(str_replace(' ', '-', $ticket->estado)) }}">{{ $ticket->estado }}</span></td>
                        <td>{{ $ticket->asignadoA->name ?? 'Sin asignar' }}</td>
                        <td>{{ $ticket->created_at->format('d/m/Y') }}</td>
                        <td style="color: {{ $ticket->sla_vencido ? 'red' : 'green' }};">
                            {{ $ticket->sla_vencido ? 'Vencido' : 'OK' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="footer">
        <p>Reporte ITIL generado automáticamente - Sistema de Gestión de Incidencias</p>
        <p>Página <span class="pagenum"></span> - {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>
