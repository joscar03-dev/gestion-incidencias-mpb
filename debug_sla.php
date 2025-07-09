<?php

require_once 'vendor/autoload.php';

// Simular Laravel App
$app = require_once 'bootstrap/app.php';

// Boot the application
$app->boot();

use App\Models\Ticket;
use App\Models\Area;
use App\Models\Sla;

// Obtener algunos tickets para probar
$tickets = Ticket::with(['area', 'area.sla'])->limit(5)->get();

echo "=== DIAGNÓSTICO DE CÁLCULO DE SLA ===\n";
echo "=====================================\n\n";

foreach ($tickets as $ticket) {
    echo "TICKET #" . $ticket->id . "\n";
    echo "  Título: " . $ticket->titulo . "\n";
    echo "  Prioridad: " . $ticket->prioridad . "\n";
    echo "  Estado: " . $ticket->estado . "\n";
    echo "  Área: " . ($ticket->area ? $ticket->area->nombre : 'N/A') . "\n";
    echo "  Creado: " . $ticket->created_at . "\n";

    // Verificar SLA del área
    if ($ticket->area && $ticket->area->sla) {
        $slaArea = $ticket->area->sla;
        echo "  SLA del área:\n";
        echo "    - Tiempo respuesta: " . $slaArea->tiempo_respuesta . " mins\n";
        echo "    - Tiempo resolución: " . $slaArea->tiempo_resolucion . " mins\n";
    } else {
        echo "  SLA del área: NO DISPONIBLE\n";
    }

    // Probar getSlaEfectivo
    $slaEfectivo = $ticket->getSlaEfectivo();
    if ($slaEfectivo) {
        echo "  SLA Efectivo (con prioridad):\n";
        echo "    - Tiempo respuesta: " . $slaEfectivo['tiempo_respuesta'] . " mins\n";
        echo "    - Tiempo resolución: " . $slaEfectivo['tiempo_resolucion'] . " mins\n";
        echo "    - Factor aplicado: " . $slaEfectivo['factor_aplicado'] . "\n";
    } else {
        echo "  SLA Efectivo: NO DISPONIBLE\n";
    }

    // Probar tiempo restante
    $tiempoRestante = $ticket->getTiempoRestanteSla('respuesta');
    echo "  Tiempo restante: " . ($tiempoRestante !== null ? $tiempoRestante . " mins" : 'N/A') . "\n";

    // Probar estado SLA
    $estadoSla = $ticket->getEstadoSla();
    echo "  Estado SLA: " . $estadoSla . "\n";

    // Tiempo transcurrido
    $tiempoTranscurrido = now()->diffInMinutes($ticket->created_at);
    echo "  Tiempo transcurrido: " . $tiempoTranscurrido . " mins\n";

    echo "\n" . str_repeat("-", 50) . "\n\n";
}

echo "=== VERIFICACIÓN DE ÁREAS Y SLAs ===\n";
echo "=====================================\n\n";

$areas = Area::with('sla')->get();
foreach ($areas as $area) {
    echo "ÁREA: " . $area->nombre . "\n";
    if ($area->sla) {
        echo "  SLA ID: " . $area->sla->id . "\n";
        echo "  Tiempo respuesta: " . $area->sla->tiempo_respuesta . " mins\n";
        echo "  Tiempo resolución: " . $area->sla->tiempo_resolucion . " mins\n";
        echo "  Activo: " . ($area->sla->activo ? 'SÍ' : 'NO') . "\n";
    } else {
        echo "  SLA: NO ASIGNADO\n";
    }
    echo "\n";
}
