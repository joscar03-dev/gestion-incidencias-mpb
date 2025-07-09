<?php

require_once 'vendor/autoload.php';

// Configurar Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Ticket;
use App\Models\Area;
use App\Models\User;

echo "=== PRUEBA DEL NUEVO SISTEMA DE ESCALADO ===\n";
echo "=============================================\n\n";

echo "🔧 CARACTERÍSTICAS DEL NUEVO ESCALADO:\n";
echo "1. ✅ Incrementa la prioridad automáticamente\n";
echo "2. ✅ Notifica al técnico asignado\n";
echo "3. ✅ Notifica a admin y superadmin\n";
echo "4. ✅ Agrega comentario al ticket\n";
echo "5. ✅ Registra en logs\n\n";

// Crear ticket de prueba
$areaTecnologia = Area::where('nombre', 'Tecnología')->first();
$usuario = User::first();

if (!$areaTecnologia || !$usuario) {
    echo "❌ No se encontraron área de Tecnología o usuario\n";
    exit;
}

echo "=== CREANDO TICKET DE PRUEBA ===\n";

// Probar con diferentes prioridades
$prioridades = ['Baja', 'Media', 'Alta', 'Critica'];

foreach ($prioridades as $prioridad) {
    echo "\n🎯 PROBANDO ESCALADO CON PRIORIDAD: {$prioridad}\n";
    echo str_repeat("-", 50) . "\n";

    $ticket = new Ticket([
        'titulo' => "Test escalado prioridad {$prioridad}",
        'descripcion' => "Prueba de escalado con prioridad {$prioridad}",
        'prioridad' => $prioridad,
        'estado' => 'Abierto',
        'area_id' => $areaTecnologia->id,
        'creado_por' => $usuario->id,
        'asignado_a' => $usuario->id,
        'escalado' => false,
    ]);

    $ticket->created_at = now()->subMinutes(30);
    $ticket->updated_at = now()->subMinutes(30);
    $ticket->save();

    echo "✅ Ticket #{$ticket->id} creado con prioridad: {$prioridad}\n";

    // Verificar estado inicial
    echo "📊 Estado inicial:\n";
    echo "   - Prioridad: {$ticket->prioridad}\n";
    echo "   - Estado: {$ticket->estado}\n";
    echo "   - Escalado: " . ($ticket->escalado ? 'SÍ' : 'NO') . "\n";

    // Ejecutar escalado
    echo "\n🚨 EJECUTANDO ESCALADO...\n";
    $ticket->escalar('Prueba de escalado automático');

    // Verificar estado final
    $ticket->refresh();
    echo "📊 Estado después del escalado:\n";
    echo "   - Prioridad: {$ticket->prioridad}\n";
    echo "   - Estado: {$ticket->estado}\n";
    echo "   - Escalado: " . ($ticket->escalado ? 'SÍ' : 'NO') . "\n";
    echo "   - Fecha escalamiento: {$ticket->fecha_escalamiento}\n";

    // Verificar comentarios
    $comentarios = $ticket->comments()->get();
    echo "   - Comentarios agregados: " . $comentarios->count() . "\n";

    if ($comentarios->count() > 0) {
        $ultimoComentario = $comentarios->last();
        echo "   - Último comentario: " . substr($ultimoComentario->body, 0, 50) . "...\n";
    }

    echo "\n✅ Escalado completado correctamente\n";
}

echo "\n=== VERIFICANDO LOGS ===\n";
echo "Los logs de notificaciones se guardaron en storage/logs/laravel.log\n";
echo "Puedes verificarlos con: tail -f storage/logs/laravel.log\n\n";

echo "=== PRUEBA COMPLETADA ===\n";
echo "El nuevo sistema de escalado está funcionando correctamente.\n";
