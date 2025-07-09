<?php

require_once 'vendor/autoload.php';

// Configurar Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Ticket;
use App\Models\Sla;
use App\Models\Area;
use App\Models\User;

echo "=== VERIFICACIÓN DEL ESCALADO ===\n";
echo "==================================\n\n";

// Verificar si hay tickets existentes
$tickets = Ticket::with(['area', 'area.slas', 'creadoPor', 'asignadoA'])->get();

echo "📋 Tickets existentes: " . $tickets->count() . "\n\n";

if ($tickets->count() > 0) {
    foreach ($tickets as $ticket) {
        echo "TICKET #{$ticket->id}\n";
        echo "  Título: {$ticket->titulo}\n";
        echo "  Prioridad: {$ticket->prioridad}\n";
        echo "  Estado: {$ticket->estado}\n";
        echo "  Escalado: " . ($ticket->escalado ? 'SÍ' : 'NO') . "\n";
        echo "  Creado: {$ticket->created_at}\n";

        // Verificar el tiempo transcurrido
        $tiempoTranscurrido = $ticket->created_at->diffInMinutes(now());
        echo "  Tiempo transcurrido: {$tiempoTranscurrido} minutos\n";

        // Verificar el SLA del área
        if ($ticket->area && $ticket->area->slas->isNotEmpty()) {
            $sla = $ticket->area->slas->first();
            echo "  SLA del área:\n";
            echo "    - Tiempo escalamiento: {$sla->tiempo_escalamiento} minutos\n";
            echo "    - Escalamiento automático: " . ($sla->escalamiento_automatico ? 'SÍ' : 'NO') . "\n";

            // Verificar si debería escalarse
            if ($sla->escalamiento_automatico) {
                $debeEscalar = $sla->debeEscalar($tiempoTranscurrido, strtolower($ticket->prioridad));
                echo "    - Debe escalar: " . ($debeEscalar ? 'SÍ' : 'NO') . "\n";
            }
        } else {
            echo "  SLA del área: NO DISPONIBLE\n";
        }

        // Verificar método de escalado del ticket
        echo "  Verificando escalado del ticket...\n";
        try {
            $escalado = $ticket->verificarSlaYEscalamiento();
            echo "    - Resultado verificación: " . ($escalado ? 'ESCALADO' : 'NO ESCALADO') . "\n";
        } catch (\Exception $e) {
            echo "    - Error en verificación: " . $e->getMessage() . "\n";
        }

        echo "\n" . str_repeat("-", 50) . "\n\n";
    }
} else {
    echo "No hay tickets para verificar. Creando un ticket de prueba...\n";

    // Crear un ticket de prueba
    $area = Area::first();
    $user = User::first();

    if ($area && $user) {
        $ticket = Ticket::create([
            'titulo' => 'Ticket de prueba para escalado',
            'descripcion' => 'Descripción de prueba',
            'prioridad' => 'Alta',
            'estado' => 'Abierto',
            'area_id' => $area->id,
            'creado_por' => $user->id,
            'asignado_a' => $user->id,
            'created_at' => now()->subMinutes(180), // 3 horas atrás
        ]);

        echo "✅ Ticket de prueba creado: #{$ticket->id}\n";
        echo "   Creado hace: 180 minutos\n";
        echo "   Reejecuta el script para verificar el escalado.\n";
    } else {
        echo "❌ No se pudo crear ticket de prueba (falta área o usuario)\n";
    }
}

// Verificar configuración de SLAs
echo "\n=== CONFIGURACIÓN DE SLAs ===\n";
$slas = Sla::with('area')->get();

foreach ($slas as $sla) {
    echo "SLA del área {$sla->area->nombre}:\n";
    echo "  - Escalamiento automático: " . ($sla->escalamiento_automatico ? 'SÍ' : 'NO') . "\n";
    echo "  - Tiempo escalamiento: {$sla->tiempo_escalamiento} minutos\n";
    echo "  - Override área: " . ($sla->override_area ? 'SÍ' : 'NO') . "\n";
    echo "\n";
}

echo "=== VERIFICACIÓN COMPLETADA ===\n";
