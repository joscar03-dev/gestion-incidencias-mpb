<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Analytics ITIL - Indicadores de Eficiencia y Calidad</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }

        .header {
            background: linear-gradient(135deg, #1e40af, #3b82f6);
            color: white;
            padding: 20px;
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 24px;
            margin-bottom: 10px;
        }

        .header .date {
            font-size: 14px;
            opacity: 0.9;
        }

        .summary-cards {
            display: flex;
            justify-content: space-between;
            margin-bottom: 25px;
            gap: 15px;
        }

        .summary-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            flex: 1;
        }

        .summary-card .value {
            font-size: 20px;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 5px;
        }

        .summary-card .label {
            font-size: 11px;
            color: #64748b;
        }

        .section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }

        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 15px;
            padding-bottom: 5px;
            border-bottom: 2px solid #e2e8f0;
        }

        .technician-list {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-bottom: 15px;
        }

        .technician-item {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 12px;
        }

        .technician-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        .technician-name {
            font-weight: bold;
            color: #1f2937;
        }

        .efficiency-rate {
            font-weight: bold;
            font-size: 14px;
        }

        .rate-excellent { color: #059669; }
        .rate-good { color: #d97706; }
        .rate-poor { color: #dc2626; }

        .technician-stats {
            display: flex;
            justify-content: space-between;
            font-size: 10px;
            color: #6b7280;
        }

        .progress-bar {
            width: 100%;
            height: 6px;
            background: #e5e7eb;
            border-radius: 3px;
            margin-top: 8px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: #3b82f6;
            border-radius: 3px;
        }

        .category-list {
            margin-bottom: 15px;
        }

        .category-item {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 8px;
        }

        .category-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        .category-name {
            font-weight: bold;
            color: #1f2937;
        }

        .category-score {
            font-weight: bold;
            color: #3b82f6;
        }

        .category-stats {
            display: flex;
            justify-content: space-between;
            font-size: 10px;
            color: #6b7280;
        }

        .escalation-section {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
        }

        .escalation-header {
            display: flex;
            justify-content: space-around;
            text-align: center;
            margin-bottom: 15px;
        }

        .escalation-metric {
            background: white;
            border-radius: 6px;
            padding: 10px;
        }

        .escalation-value {
            font-size: 18px;
            font-weight: bold;
            color: #dc2626;
        }

        .escalation-label {
            font-size: 10px;
            color: #6b7280;
        }

        .satisfaction-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 15px;
        }

        .satisfaction-item {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 12px;
            text-align: center;
        }

        .satisfaction-category {
            font-size: 11px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 8px;
        }

        .satisfaction-score {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .satisfaction-stars {
            font-size: 10px;
            color: #6b7280;
        }

        .summary-section {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-top: 20px;
        }

        .summary-box {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
        }

        .summary-title {
            font-size: 14px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 12px;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
            font-size: 11px;
        }

        .summary-value {
            font-weight: bold;
        }

        .value-excellent { color: #059669; }
        .value-good { color: #d97706; }
        .value-poor { color: #dc2626; }
        .value-info { color: #3b82f6; }

        .page-break {
            page-break-before: always;
        }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 10px;
            color: #6b7280;
            padding: 10px 0;
            border-top: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>Reporte de Analytics ITIL</h1>
        <h2>Indicadores de Eficiencia y Calidad</h2>
        <div class="date">Generado el: {{ $last_updated }}</div>
    </div>

    <!-- Indicadores Principales -->
    <div class="summary-cards">
        <div class="summary-card">
            <div class="value">{{ $efficiency_indicators['overall_efficiency'] }}%</div>
            <div class="label">Eficiencia General</div>
        </div>
        <div class="summary-card">
            <div class="value">{{ $quality_indicators['overall_quality_score'] }}%</div>
            <div class="label">Calidad General</div>
        </div>
        <div class="summary-card">
            <div class="value">{{ $efficiency_indicators['avg_resolution_time'] }}h</div>
            <div class="label">Tiempo Promedio</div>
        </div>
        <div class="summary-card">
            <div class="value">{{ $quality_indicators['sla_compliance_rate'] }}%</div>
            <div class="label">Cumplimiento SLA</div>
        </div>
    </div>

    <!-- Productividad por Técnico -->
    <div class="section">
        <h3 class="section-title">Productividad por Técnico (Top 10)</h3>
        <div class="technician-list">
            @foreach($efficiency_indicators['technician_productivity']->take(10) as $tech)
                <div class="technician-item">
                    <div class="technician-header">
                        <div class="technician-name">{{ $tech['technician'] }}</div>
                        <div class="efficiency-rate
                            @if($tech['efficiency_rate'] >= 80) rate-excellent
                            @elseif($tech['efficiency_rate'] >= 60) rate-good
                            @else rate-poor @endif">
                            {{ $tech['efficiency_rate'] }}%
                        </div>
                    </div>
                    <div class="technician-stats">
                        <span>Asignados: {{ $tech['total_assigned'] }}</span>
                        <span>Resueltos: {{ $tech['resolved'] }}</span>
                        <span>Pendientes: {{ $tech['pending'] }}</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: {{ $tech['efficiency_rate'] }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Eficiencia por Categoría -->
    <div class="section">
        <h3 class="section-title">Eficiencia por Categoría ITIL</h3>
        <div class="category-list">
            @foreach($efficiency_indicators['category_efficiency']->take(8) as $category)
                <div class="category-item">
                    <div class="category-header">
                        <div class="category-name">{{ $category['category'] }}</div>
                        <div class="category-score">Score: {{ $category['efficiency_score'] }}</div>
                    </div>
                    <div class="category-stats">
                        <span>Total: {{ $category['total_tickets'] }}</span>
                        <span>Resueltos: {{ $category['resolved_tickets'] }}</span>
                        <span>Tiempo: {{ $category['avg_resolution_time'] }}h</span>
                        <span>{{ $category['resolution_rate'] }}% resueltos</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: {{ $category['resolution_rate'] }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="page-break"></div>

    <!-- Calidad por Técnico -->
    <div class="section">
        <h3 class="section-title">Calidad por Técnico (Top 10)</h3>
        <div class="technician-list">
            @foreach($quality_indicators['technician_quality']->take(10) as $tech)
                <div class="technician-item">
                    <div class="technician-header">
                        <div class="technician-name">{{ $tech['technician'] }}</div>
                        <div class="efficiency-rate
                            @if($tech['quality_score'] >= 80) rate-excellent
                            @elseif($tech['quality_score'] >= 60) rate-good
                            @else rate-poor @endif">
                            {{ $tech['quality_score'] }}/100
                        </div>
                    </div>
                    <div class="technician-stats">
                        <span>Satisfacción: {{ $tech['avg_satisfaction'] }}/5</span>
                        <span>Tickets: {{ $tech['resolved_tickets'] }}</span>
                        <span>Reaperturas: {{ $tech['reopen_rate'] }}%</span>
                        <span>Escalaciones: {{ $tech['escalation_rate'] }}%</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: {{ $tech['quality_score'] }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Análisis de Escalaciones -->
    <div class="section">
        <h3 class="section-title">Análisis de Escalaciones</h3>
        <div class="escalation-section">
            <div class="escalation-header">
                <div class="escalation-metric">
                    <div class="escalation-value">{{ $quality_indicators['escalation_analysis']['total_escalated'] }}</div>
                    <div class="escalation-label">Total Escalados</div>
                </div>
                <div class="escalation-metric">
                    <div class="escalation-value">{{ $quality_indicators['escalation_analysis']['escalation_rate'] }}%</div>
                    <div class="escalation-label">Tasa de Escalación</div>
                </div>
            </div>

            <h4 style="margin-bottom: 10px; font-size: 12px; font-weight: bold;">Escalaciones por Categoría:</h4>
            @foreach($quality_indicators['escalation_analysis']['escalation_by_category'] as $escalation)
                <div class="category-item">
                    <div class="category-header">
                        <div class="category-name">{{ $escalation['category'] }}</div>
                        <div class="category-score
                            @if($escalation['escalation_rate'] > 15) rate-poor
                            @elseif($escalation['escalation_rate'] > 8) rate-good
                            @else rate-excellent @endif">
                            {{ $escalation['escalation_rate'] }}%
                        </div>
                    </div>
                    <div class="category-stats">
                        <span>Escalados: {{ $escalation['escalated'] }}/{{ $escalation['total'] }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Satisfacción por Categoría -->
    <div class="section">
        <h3 class="section-title">Satisfacción del Cliente por Categoría</h3>
        <div class="satisfaction-grid">
            @foreach($quality_indicators['satisfaction_by_category']->take(8) as $satisfaction)
                <div class="satisfaction-item">
                    <div class="satisfaction-category">{{ $satisfaction['category'] }}</div>
                    <div class="satisfaction-score
                        @if($satisfaction['quality_score'] >= 80) rate-excellent
                        @elseif($satisfaction['quality_score'] >= 60) rate-good
                        @else rate-poor @endif">
                        {{ $satisfaction['quality_score'] }}%
                    </div>
                    <div class="satisfaction-stars">{{ $satisfaction['avg_satisfaction'] }}/5 ⭐</div>
                    <div style="font-size: 9px; color: #9ca3af; margin-top: 4px;">
                        {{ $satisfaction['satisfaction_responses'] }}/{{ $satisfaction['total_tickets'] }} respuestas
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Resúmenes de Rendimiento -->
    <div class="summary-section">
        <!-- Resumen de Productividad -->
        <div class="summary-box">
            <div class="summary-title">Resumen de Productividad</div>
            <div class="summary-item">
                <span>Alto Rendimiento (≥80%)</span>
                <span class="summary-value value-excellent">{{ $efficiency_indicators['productivity_summary']['high_performers'] }}</span>
            </div>
            <div class="summary-item">
                <span>Rendimiento Promedio (60-79%)</span>
                <span class="summary-value value-good">{{ $efficiency_indicators['productivity_summary']['average_performers'] }}</span>
            </div>
            <div class="summary-item">
                <span>Bajo Rendimiento (<60%)</span>
                <span class="summary-value value-poor">{{ $efficiency_indicators['productivity_summary']['low_performers'] }}</span>
            </div>
        </div>

        <!-- Resumen de Calidad -->
        <div class="summary-box">
            <div class="summary-title">Resumen de Calidad</div>
            <div class="summary-item">
                <span>Calidad Excelente (≥80)</span>
                <span class="summary-value value-excellent">{{ $quality_indicators['quality_summary']['excellent_quality'] }}</span>
            </div>
            <div class="summary-item">
                <span>Buena Calidad (60-79)</span>
                <span class="summary-value value-good">{{ $quality_indicators['quality_summary']['good_quality'] }}</span>
            </div>
            <div class="summary-item">
                <span>Necesita Mejora (<60)</span>
                <span class="summary-value value-poor">{{ $quality_indicators['quality_summary']['needs_improvement'] }}</span>
            </div>
        </div>

        <!-- KPIs Clave -->
        <div class="summary-box">
            <div class="summary-title">KPIs Clave</div>
            <div class="summary-item">
                <span>Tasa de Reapertura</span>
                <span class="summary-value value-poor">{{ $quality_indicators['reopen_rate'] }}%</span>
            </div>
            <div class="summary-item">
                <span>Primera Resolución</span>
                <span class="summary-value value-excellent">{{ $quality_indicators['first_call_resolution_rate'] }}%</span>
            </div>
            <div class="summary-item">
                <span>Tiempo Promedio Respuesta</span>
                <span class="summary-value value-info">{{ $efficiency_indicators['avg_first_response_time'] }}h</span>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>Sistema de Gestión de Incidencias ITIL - Reporte generado automáticamente el {{ $last_updated }}</p>
    </div>
</body>
</html>
