<?php

require_once 'vendor/autoload.php';

// Configurar Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Ticket;
use App\Models\Area;
use App\Models\User;

echo "=== PRUEBA DE ESCALADO CON TICKET VENCIDO ===\n";
echo "==============================================\n\n";

// Crear un ticket que definitivamente debería escalarse
$areaTecnologia = Area::where('nombre', 'Tecnología')->first();
$usuario = User::first();

if (!$areaTecnologia || !$usuario) {
    echo "❌ No se encontraron área de Tecnología o usuario\n";
    exit;
}

// Crear ticket con fecha anterior para que pase el tiempo de escalamiento
$ticket = new Ticket([
    'titulo' => 'Test escalado VENCIDO - ' . now()->format('Y-m-d H:i:s'),
    'descripcion' => 'Ticket de prueba para verificar escalado automático',
    'prioridad' => 'Alta',
    'estado' => 'Abierto',
    'area_id' => $areaTecnologia->id,
    'creado_por' => $usuario->id,
    'asignado_a' => $usuario->id,
    'escalado' => false,
]);

// Establecer manualmente las fechas
$ticket->created_at = now()->subMinutes(30); // Creado hace 30 minutos
$ticket->updated_at = now()->subMinutes(30);
$ticket->save();

echo "✅ Ticket creado: #{$ticket->id}\n";
echo "   Título: {$ticket->titulo}\n";
echo "   Creado hace: 30 minutos\n";
echo "   Área: {$areaTecnologia->nombre}\n";
echo "   SLA de escalamiento: 10 minutos\n\n";

// Verificar el estado inicial
echo "=== ESTADO INICIAL ===\n";
echo "Escalado: " . ($ticket->escalado ? 'SÍ' : 'NO') . "\n";
echo "Estado: {$ticket->estado}\n";

// Verificar si debe escalarse
$tiempoTranscurrido = $ticket->created_at->diffInMinutes(now());
echo "Tiempo transcurrido: {$tiempoTranscurrido} minutos\n";

$debeEscalar = $ticket->debeEscalar();
echo "Debe escalar: " . ($debeEscalar ? 'SÍ' : 'NO') . "\n\n";

if ($debeEscalar) {
    // Ejecutar la verificación de escalado
    echo "=== EJECUTANDO VERIFICACIÓN DE ESCALADO ===\n";
    $resultado = $ticket->verificarSlaYEscalamiento();
    echo "Resultado: " . ($resultado ? 'ESCALADO' : 'NO ESCALADO') . "\n\n";

    // Verificar el estado final
    $ticket->refresh();
    echo "=== ESTADO FINAL ===\n";
    echo "Escalado: " . ($ticket->escalado ? 'SÍ' : 'NO') . "\n";
    echo "Estado: {$ticket->estado}\n";

    // Verificar comentarios del ticket
    $comentarios = $ticket->comments()->get();
    echo "Comentarios totales: " . $comentarios->count() . "\n";

    foreach ($comentarios as $comentario) {
        echo "  - {$comentario->body}\n";
    }
} else {
    echo "⚠️  El ticket no debería escalarse según la lógica actual\n";
}

echo "\n=== PRUEBA COMPLETADA ===\n";
