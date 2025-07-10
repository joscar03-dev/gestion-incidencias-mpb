<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket #{{ $ticket->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
            line-height: 1.6;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #4CAF50;
        }
        .header h1 {
            color: #4CAF50;
            margin: 0;
        }
        .ticket-info {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 30px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            border-bottom: 1px solid #eee;
            padding-bottom: 8px;
        }
        .info-label {
            font-weight: bold;
            color: #4CAF50;
            width: 30%;
        }
        .info-value {
            width: 65%;
        }
        .status, .priority {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-abierto { background-color: #2196F3; color: white; }
        .status-en-progreso { background-color: #FF9800; color: white; }
        .status-cerrado { background-color: #4CAF50; color: white; }
        .status-cancelado { background-color: #F44336; color: white; }
        .priority-baja { background-color: #9E9E9E; color: white; }
        .priority-media { background-color: #2196F3; color: white; }
        .priority-alta { background-color: #FF9800; color: white; }
        .priority-critica { background-color: #F44336; color: white; }
        .description {
            background-color: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .description h3 {
            margin-top: 0;
            color: #4CAF50;
        }
        .comments {
            background-color: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .comments h3 {
            margin-top: 0;
            color: #4CAF50;
        }
        .timeline {
            margin-top: 20px;
        }
        .timeline h3 {
            color: #4CAF50;
            border-bottom: 2px solid #4CAF50;
            padding-bottom: 10px;
        }
        .timeline-item {
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .timeline-date {
            font-weight: bold;
            color: #666;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #666;
            font-size: 12px;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Ticket #{{ $ticket->id }}</h1>
        <p>{{ $ticket->titulo }}</p>
        <p>Generado el {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <div class="ticket-info">
        <div class="info-row">
            <div class="info-label">ID:</div>
            <div class="info-value">#{{ $ticket->id }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Título:</div>
            <div class="info-value">{{ $ticket->titulo }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Estado:</div>
            <div class="info-value">
                <span class="status status-{{ strtolower(str_replace(' ', '-', $ticket->estado)) }}">
                    {{ $ticket->estado }}
                </span>
            </div>
        </div>
        <div class="info-row">
            <div class="info-label">Prioridad:</div>
            <div class="info-value">
                <span class="priority priority-{{ strtolower($ticket->prioridad) }}">
                    {{ $ticket->prioridad }}
                </span>
            </div>
        </div>
        <div class="info-row">
            <div class="info-label">Área:</div>
            <div class="info-value">{{ $ticket->area->nombre ?? 'Sin área asignada' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Creado por:</div>
            <div class="info-value">{{ $ticket->creadoPor->name ?? 'Usuario desconocido' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Asignado a:</div>
            <div class="info-value">{{ $ticket->asignadoA->name ?? 'Sin asignar' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Fecha de creación:</div>
            <div class="info-value">{{ $ticket->created_at->format('d/m/Y H:i') }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Última actualización:</div>
            <div class="info-value">{{ $ticket->updated_at->format('d/m/Y H:i') }}</div>
        </div>
        @if($ticket->escalado)
        <div class="info-row">
            <div class="info-label">Escalado:</div>
            <div class="info-value">Sí - {{ $ticket->fecha_escalamiento ? $ticket->fecha_escalamiento->format('d/m/Y H:i') : 'Fecha no disponible' }}</div>
        </div>
        @endif
        @if($ticket->sla_vencido)
        <div class="info-row">
            <div class="info-label">SLA:</div>
            <div class="info-value">Vencido</div>
        </div>
        @endif
    </div>

    <div class="description">
        <h3>Descripción del Problema</h3>
        <p>{{ $ticket->descripcion }}</p>
    </div>

    @if($ticket->comentario)
    <div class="comments">
        <h3>{{ $ticket->estado === 'Cerrado' ? 'Solución' : 'Comentarios' }}</h3>
        <p>{{ $ticket->comentario }}</p>
    </div>
    @endif

    @if($ticket->comentarios_resolucion)
    <div class="comments">
        <h3>Comentarios de Resolución</h3>
        <p>{{ $ticket->comentarios_resolucion }}</p>
    </div>
    @endif

    <div class="timeline">
        <h3>Historial del Ticket</h3>
        <div class="timeline-item">
            <div class="timeline-date">{{ $ticket->created_at->format('d/m/Y H:i') }}</div>
            <div>Ticket creado por {{ $ticket->creadoPor->name ?? 'Usuario desconocido' }}</div>
        </div>
        @if($ticket->asignado_a)
        <div class="timeline-item">
            <div class="timeline-date">{{ $ticket->created_at->format('d/m/Y H:i') }}</div>
            <div>Ticket asignado a {{ $ticket->asignadoA->name }}</div>
        </div>
        @endif
        @if($ticket->escalado && $ticket->fecha_escalamiento)
        <div class="timeline-item">
            <div class="timeline-date">{{ $ticket->fecha_escalamiento->format('d/m/Y H:i') }}</div>
            <div>Ticket escalado</div>
        </div>
        @endif
        @if($ticket->fecha_resolucion)
        <div class="timeline-item">
            <div class="timeline-date">{{ $ticket->fecha_resolucion->format('d/m/Y H:i') }}</div>
            <div>Ticket {{ $ticket->estado === 'Cerrado' ? 'cerrado' : 'resuelto' }}</div>
        </div>
        @endif
        <div class="timeline-item">
            <div class="timeline-date">{{ $ticket->updated_at->format('d/m/Y H:i') }}</div>
            <div>Última actualización</div>
        </div>
    </div>

    <div class="footer">
        <p>Sistema de Gestión de Incidencias - {{ config('app.name') }}</p>
        <p>Reporte generado automáticamente</p>
    </div>
</body>
</html>
