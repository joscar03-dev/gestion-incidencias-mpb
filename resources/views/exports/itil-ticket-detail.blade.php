<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Detalle de Ticket ITIL - {{ $ticket->id }}</title>
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
        .info-section {
            margin-bottom: 25px;
            background: #f9f9f9;
            padding: 15px;
            border-left: 4px solid #0066cc;
        }
        .info-title {
            font-weight: bold;
            color: #0066cc;
            margin-bottom: 10px;
            font-size: 16px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 10px;
            margin-bottom: 10px;
        }
        .info-label {
            font-weight: bold;
            color: #333;
        }
        .info-value {
            color: #666;
        }
        .status-badge {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 11px;
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
        .description-box {
            background: white;
            border: 1px solid #ddd;
            padding: 15px;
            margin-top: 10px;
            border-radius: 5px;
        }
        .timeline {
            border-left: 3px solid #0066cc;
            padding-left: 20px;
            margin: 20px 0;
        }
        .timeline-item {
            margin-bottom: 15px;
            position: relative;
        }
        .timeline-item::before {
            content: '';
            position: absolute;
            left: -25px;
            top: 5px;
            width: 8px;
            height: 8px;
            background: #0066cc;
            border-radius: 50%;
        }
        .timeline-date {
            font-size: 10px;
            color: #666;
            margin-bottom: 5px;
        }
        .metrics-section {
            background: #f0f8ff;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }
        .sla-indicator {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 11px;
            font-weight: bold;
        }
        .sla-ok { background: #d4edda; color: #155724; }
        .sla-warning { background: #fff3cd; color: #856404; }
        .sla-danger { background: #f8d7da; color: #721c24; }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            padding: 10px;
            border-top: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🎫 Detalle de Ticket ITIL</h1>
        <p><strong>ID:</strong> {{ $ticket->id }} | <strong>Generado:</strong> {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <!-- Información Principal -->
    <div class="info-section">
        <div class="info-title">📋 Información Principal</div>
        <div class="info-grid">
            <div class="info-label">Título:</div>
            <div class="info-value">{{ $ticket->titulo }}</div>
            
            <div class="info-label">Tipo ITIL:</div>
            <div class="info-value">
                <span class="status-badge">{{ $ticket->tipo }}</span>
            </div>
            
            <div class="info-label">Prioridad:</div>
            <div class="info-value">
                <span class="status-badge priority-{{ strtolower($ticket->prioridad) }}">{{ $ticket->prioridad }}</span>
            </div>
            
            <div class="info-label">Estado:</div>
            <div class="info-value">
                <span class="status-badge status-{{ strtolower(str_replace(' ', '-', $ticket->estado)) }}">{{ $ticket->estado }}</span>
            </div>
            
            <div class="info-label">Creado por:</div>
            <div class="info-value">{{ $ticket->creadoPor->name ?? 'Sistema' }}</div>
            
            <div class="info-label">Asignado a:</div>
            <div class="info-value">{{ $ticket->asignadoA->name ?? 'Sin asignar' }}</div>
            
            <div class="info-label">Área:</div>
            <div class="info-value">{{ $ticket->area->nombre ?? 'Sin área' }}</div>
            
            <div class="info-label">Categorías:</div>
            <div class="info-value">
                @if($ticket->categorias->count() > 0)
                    {{ $ticket->categorias->pluck('nombre')->implode(', ') }}
                @else
                    Sin categorías asignadas
                @endif
            </div>
        </div>
        
        <div class="description-box">
            <strong>Descripción:</strong><br>
            {!! nl2br(e($ticket->descripcion)) !!}
        </div>
        
        @if($ticket->comentario)
        <div class="description-box">
            <strong>Comentarios:</strong><br>
            {!! nl2br(e($ticket->comentario)) !!}
        </div>
        @endif
    </div>

    <!-- Información de Fechas y Tiempos -->
    <div class="info-section">
        <div class="info-title">⏰ Cronología y SLA</div>
        <div class="info-grid">
            <div class="info-label">Fecha de Creación:</div>
            <div class="info-value">{{ $ticket->created_at->format('d/m/Y H:i:s') }}</div>
            
            @if($ticket->fecha_resolucion)
            <div class="info-label">Fecha de Resolución:</div>
            <div class="info-value">{{ $ticket->fecha_resolucion->format('d/m/Y H:i:s') }}</div>
            @endif
            
            @if($ticket->fecha_escalamiento)
            <div class="info-label">Fecha de Escalamiento:</div>
            <div class="info-value">{{ $ticket->fecha_escalamiento->format('d/m/Y H:i:s') }}</div>
            @endif
            
            <div class="info-label">Tiempo Transcurrido:</div>
            <div class="info-value">
                @if($ticket->fecha_resolucion)
                    {{ $ticket->created_at->diffInHours($ticket->fecha_resolucion) }} horas
                @else
                    {{ $ticket->created_at->diffInHours(now()) }} horas (en curso)
                @endif
            </div>
            
            <div class="info-label">Estado SLA:</div>
            <div class="info-value">
                @php
                    $slaHours = match($ticket->prioridad) {
                        'Critica' => 2,
                        'Alta' => 4,
                        'Media' => 24,
                        'Baja' => 72,
                        default => 24
                    };
                    
                    $currentHours = $ticket->fecha_resolucion 
                        ? $ticket->created_at->diffInHours($ticket->fecha_resolucion)
                        : $ticket->created_at->diffInHours(now());
                        
                    if ($ticket->sla_vencido) {
                        $slaClass = 'sla-danger';
                        $slaText = 'SLA Vencido';
                    } elseif ($currentHours > ($slaHours * 0.8)) {
                        $slaClass = 'sla-warning';
                        $slaText = 'En Riesgo';
                    } else {
                        $slaClass = 'sla-ok';
                        $slaText = 'Dentro del SLA';
                    }
                @endphp
                <span class="sla-indicator {{ $slaClass }}">{{ $slaText }}</span>
                <br><small>Objetivo SLA: {{ $slaHours }} horas</small>
            </div>
        </div>
    </div>

    <!-- Cronología del Ticket -->
    <div class="info-section">
        <div class="info-title">📈 Cronología del Ticket</div>
        <div class="timeline">
            <div class="timeline-item">
                <div class="timeline-date">{{ $ticket->created_at->format('d/m/Y H:i:s') }}</div>
                <div><strong>Ticket Creado</strong> por {{ $ticket->creadoPor->name ?? 'Sistema' }}</div>
            </div>
            
            @if($ticket->asignado_a)
            <div class="timeline-item">
                <div class="timeline-date">{{ $ticket->updated_at->format('d/m/Y H:i:s') }}</div>
                <div><strong>Asignado</strong> a {{ $ticket->asignadoA->name }}</div>
            </div>
            @endif
            
            @if($ticket->fecha_escalamiento)
            <div class="timeline-item">
                <div class="timeline-date">{{ $ticket->fecha_escalamiento->format('d/m/Y H:i:s') }}</div>
                <div><strong>Ticket Escalado</strong> - Requiere atención especial</div>
            </div>
            @endif
            
            @if($ticket->fecha_resolucion)
            <div class="timeline-item">
                <div class="timeline-date">{{ $ticket->fecha_resolucion->format('d/m/Y H:i:s') }}</div>
                <div><strong>Ticket Resuelto</strong></div>
                @if($ticket->comentarios_resolucion)
                    <div style="margin-top: 5px; padding: 5px; background: #f0f8ff; border-radius: 3px;">
                        <em>Comentarios de resolución:</em><br>
                        {{ $ticket->comentarios_resolucion }}
                    </div>
                @endif
            </div>
            @endif
        </div>
    </div>

    <!-- Métricas del Ticket -->
    <div class="metrics-section">
        <div class="info-title">📊 Métricas del Ticket</div>
        <div class="info-grid">
            <div class="info-label">Clasificación ITIL:</div>
            <div class="info-value">
                @switch($ticket->tipo)
                    @case('Incidente')
                        Incidente - Interrupción no planificada del servicio
                        @break
                    @case('Requerimiento')
                        Solicitud de Servicio - Petición de usuario estándar
                        @break
                    @case('Cambio')
                        Gestión de Cambios - Modificación controlada
                        @break
                    @case('General')
                        Consulta General - Información o soporte
                        @break
                    @default
                        Sin clasificación específica
                @endswitch
            </div>
            
            <div class="info-label">Impacto Estimado:</div>
            <div class="info-value">
                @switch($ticket->prioridad)
                    @case('Critica')
                        Alto - Afecta múltiples usuarios críticos
                        @break
                    @case('Alta')
                        Medio-Alto - Afecta usuarios importantes
                        @break
                    @case('Media')
                        Medio - Afecta usuarios específicos
                        @break
                    @case('Baja')
                        Bajo - Impacto limitado
                        @break
                @endswitch
            </div>
            
            <div class="info-label">Cumplimiento SLA:</div>
            <div class="info-value">
                @if($ticket->sla_vencido)
                    ❌ No cumple - Tiempo excedido
                @elseif($ticket->fecha_resolucion)
                    ✅ Cumplido - Resuelto a tiempo
                @else
                    ⏳ En progreso - Dentro del tiempo permitido
                @endif
            </div>
            
            <div class="info-label">Eficiencia:</div>
            <div class="info-value">
                @if($ticket->fecha_resolucion)
                    @php
                        $efficiency = ($slaHours - $currentHours) / $slaHours * 100;
                        $efficiency = max(0, min(100, $efficiency));
                    @endphp
                    {{ round($efficiency, 1) }}% - 
                    @if($efficiency >= 70)
                        Excelente
                    @elseif($efficiency >= 40)
                        Buena
                    @else
                        Mejorable
                    @endif
                @else
                    En evaluación
                @endif
            </div>
        </div>
    </div>

    <!-- Información del Dispositivo (si aplica) -->
    @if($ticket->dispositivo)
    <div class="info-section">
        <div class="info-title">💻 Información del Dispositivo</div>
        <div class="info-grid">
            <div class="info-label">Dispositivo:</div>
            <div class="info-value">{{ $ticket->dispositivo->nombre ?? 'Sin nombre' }}</div>
            
            <div class="info-label">Tipo:</div>
            <div class="info-value">{{ $ticket->dispositivo->tipo ?? 'No especificado' }}</div>
            
            <div class="info-label">Estado:</div>
            <div class="info-value">{{ $ticket->dispositivo->estado ?? 'No especificado' }}</div>
        </div>
    </div>
    @endif

    <div class="footer">
        <p>Detalle de Ticket ITIL - Sistema de Gestión de Incidencias</p>
        <p>Generado automáticamente el {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>
