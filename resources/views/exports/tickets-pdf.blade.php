<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Tickets</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
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
        .header p {
            color: #666;
            margin: 5px 0;
        }
        .filters {
            background-color: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 30px;
        }
        .filters h3 {
            margin-top: 0;
            color: #4CAF50;
        }
        .filters p {
            margin: 5px 0;
        }
        .summary {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }
        .summary-item {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
            min-width: 150px;
            margin-bottom: 10px;
        }
        .summary-item h4 {
            margin: 0;
            color: #4CAF50;
            font-size: 24px;
        }
        .summary-item p {
            margin: 5px 0 0 0;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #4CAF50;
            color: white;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .status {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-abierto { background-color: #2196F3; color: white; }
        .status-en-progreso { background-color: #FF9800; color: white; }
        .status-cerrado { background-color: #4CAF50; color: white; }
        .status-cancelado { background-color: #F44336; color: white; }
        .priority {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
        }
        .priority-baja { background-color: #9E9E9E; color: white; }
        .priority-media { background-color: #2196F3; color: white; }
        .priority-alta { background-color: #FF9800; color: white; }
        .priority-critica { background-color: #F44336; color: white; }
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
        <h1>Reporte de Tickets de Soporte</h1>
        <p>Generado el {{ now()->format('d/m/Y H:i') }}</p>
        <p>Usuario: {{ auth()->user()->name }}</p>
    </div>

    @if($filters)
    <div class="filters">
        <h3>Filtros Aplicados</h3>
        @if($filters['search'])
            <p><strong>Búsqueda:</strong> {{ $filters['search'] }}</p>
        @endif
        @if($filters['status'])
            <p><strong>Estado:</strong> {{ $filters['status'] }}</p>
        @endif
        @if($filters['priority'])
            <p><strong>Prioridad:</strong> {{ $filters['priority'] }}</p>
        @endif
        @if(!$filters['search'] && !$filters['status'] && !$filters['priority'])
            <p>Sin filtros aplicados - Mostrando todos los tickets</p>
        @endif
    </div>
    @endif

    <div class="summary">
        <div class="summary-item">
            <h4>{{ $tickets->count() }}</h4>
            <p>Total de Tickets</p>
        </div>
        <div class="summary-item">
            <h4>{{ $tickets->where('estado', 'Abierto')->count() }}</h4>
            <p>Abiertos</p>
        </div>
        <div class="summary-item">
            <h4>{{ $tickets->where('estado', 'En Progreso')->count() }}</h4>
            <p>En Progreso</p>
        </div>
        <div class="summary-item">
            <h4>{{ $tickets->where('estado', 'Cerrado')->count() }}</h4>
            <p>Cerrados</p>
        </div>
        <div class="summary-item">
            <h4>{{ $tickets->where('estado', 'Cancelado')->count() }}</h4>
            <p>Cancelados</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Título</th>
                <th>Estado</th>
                <th>Prioridad</th>
                <th>Área</th>
                <th>Fecha</th>
                <th>Asignado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tickets as $ticket)
            <tr>
                <td>{{ $ticket->id }}</td>
                <td>{{ Str::limit($ticket->titulo, 40) }}</td>
                <td>
                    <span class="status status-{{ strtolower(str_replace(' ', '-', $ticket->estado)) }}">
                        {{ $ticket->estado }}
                    </span>
                </td>
                <td>
                    <span class="priority priority-{{ strtolower($ticket->prioridad) }}">
                        {{ $ticket->prioridad }}
                    </span>
                </td>
                <td>{{ $ticket->area->nombre ?? 'Sin área' }}</td>
                <td>{{ $ticket->created_at->format('d/m/Y') }}</td>
                <td>{{ $ticket->asignadoA->name ?? 'Sin asignar' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Sistema de Gestión de Incidencias - {{ config('app.name') }}</p>
        <p>Reporte generado automáticamente</p>
    </div>
</body>
</html>
