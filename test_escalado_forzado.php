<?php

require_once 'vendor/autoload.php';

// Configurar Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Ticket;
use App\Models\Sla;
use Carbon\Carbon;

echo "=== PRUEBA FORZADA DE ESCALADO ===\n";
echo "===================================\n\n";

// Obtener el primer ticket que no esté escalado
$ticket = Ticket::where('escalado', false)->where('estado', '!=', 'Cerrado')->first();

if (!$ticket) {
    echo "❌ No se encontró un ticket sin escalar\n";
    exit;
}

echo "📋 Ticket seleccionado: #{$ticket->id}\n";
echo "   Título: {$ticket->titulo}\n";
echo "   Prioridad: {$ticket->prioridad}\n";
echo "   Estado: {$ticket->estado}\n";
echo "   Escalado: " . ($ticket->escalado ? 'SÍ' : 'NO') . "\n";
echo "   Fecha creación: {$ticket->created_at}\n\n";

// Obtener SLA del área
$area = $ticket->area;
if ($area && $area->slas->isNotEmpty()) {
    $sla = $area->slas->first();
    echo "📋 SLA del área {$area->nombre}:\n";
    echo "   - Escalamiento automático: " . ($sla->escalamiento_automatico ? 'SÍ' : 'NO') . "\n";
    echo "   - Tiempo escalamiento: {$sla->tiempo_escalamiento} minutos\n\n";

    // Probar con diferentes tiempos transcurridos
    $tiemposPrueba = [5, 10, 15, 30, 60, 120];

    foreach ($tiemposPrueba as $tiempo) {
        echo "🔍 Probando con {$tiempo} minutos transcurridos:\n";

        $debeEscalar = $sla->debeEscalar($tiempo, 'alto');
        echo "   - Debe escalar: " . ($debeEscalar ? 'SÍ' : 'NO') . "\n";

        if ($debeEscalar) {
            echo "   ✅ DEBE ESCALAR con {$tiempo} minutos\n";
            break;
        }
    }

    // Forzar escalado manual
    echo "\n🔧 Forzando escalado manual...\n";
    $ticket->escalar('Escalado manual de prueba');

    $ticket->refresh();
    echo "   - Estado final: {$ticket->estado}\n";
    echo "   - Escalado: " . ($ticket->escalado ? 'SÍ' : 'NO') . "\n";
    echo "   - Fecha escalamiento: {$ticket->fecha_escalamiento}\n";

} else {
    echo "❌ No se encontró SLA para el área del ticket\n";
}

echo "\n=== PRUEBA COMPLETADA ===\n";
