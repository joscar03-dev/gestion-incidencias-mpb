<?php

require_once 'vendor/autoload.php';

// Configurar Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Ticket;
use App\Models\Sla;
use Carbon\Carbon;

echo "=== PRUEBA DE ESCALADO CON TICKET NUEVO ===\n";
echo "============================================\n\n";

// Crear ticket de prueba
$ticket = Ticket::create([
    'titulo' => 'Test escalado - ' . now(),
    'descripcion' => 'Ticket de prueba para verificar escalado',
    'prioridad' => 'Alta',
    'estado' => 'Abierto',
    'area_id' => 1,
    'creado_por' => 1,
    'asignado_a' => 1,
    'created_at' => now()->subMinutes(20), // Creado hace 20 minutos
]);

echo "✅ Ticket creado: #{$ticket->id}\n";
echo "   Fecha creación: {$ticket->created_at}\n";
echo "   Fecha actual: " . now() . "\n";

// Verificar tiempo transcurrido
$tiempoTranscurrido = now()->diffInMinutes($ticket->created_at);
echo "   Tiempo transcurrido: {$tiempoTranscurrido} minutos\n\n";

// Verificar SLA del área
$sla = Sla::where('area_id', 1)->first();
if ($sla) {
    echo "📋 SLA del área Tecnología:\n";
    echo "   - Escalamiento automático: " . ($sla->escalamiento_automatico ? 'SÍ' : 'NO') . "\n";
    echo "   - Tiempo escalamiento: {$sla->tiempo_escalamiento} minutos\n";
    echo "   - Override área: " . ($sla->override_area ? 'SÍ' : 'NO') . "\n\n";

    // Verificar si debe escalar
    $debeEscalar = $sla->debeEscalar($tiempoTranscurrido, 'alto');
    echo "   - Debe escalar (método SLA): " . ($debeEscalar ? 'SÍ' : 'NO') . "\n";

    // Verificar con el método del ticket
    $debeEscalarTicket = $ticket->debeEscalar();
    echo "   - Debe escalar (método Ticket): " . ($debeEscalarTicket ? 'SÍ' : 'NO') . "\n\n";

    // Verificar escalado automático
    $escalado = $ticket->verificarSlaYEscalamiento();
    echo "   - Resultado verificación: " . ($escalado ? 'ESCALADO' : 'NO ESCALADO') . "\n";

    // Verificar estado final
    $ticket->refresh();
    echo "   - Estado final: {$ticket->estado}\n";
    echo "   - Escalado: " . ($ticket->escalado ? 'SÍ' : 'NO') . "\n";

} else {
    echo "❌ No se encontró SLA para el área\n";
}

echo "\n=== PRUEBA COMPLETADA ===\n";
