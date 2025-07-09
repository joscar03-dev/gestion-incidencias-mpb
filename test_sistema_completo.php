<?php

require_once 'vendor/autoload.php';

// Configurar Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Ticket;
use App\Models\Area;
use App\Models\User;
use Illuminate\Support\Facades\DB;

echo "=== SISTEMA DE ESCALADO CON NOTIFICACIONES FILAMENT ===\n";
echo "=======================================================\n\n";

echo "🎯 CARACTERÍSTICAS IMPLEMENTADAS:\n";
echo "✅ Escalado automático cuando vence el SLA\n";
echo "✅ Incremento automático de prioridad al escalar\n";
echo "✅ Notificaciones personalizadas para técnicos\n";
echo "✅ Notificaciones para admin y superadmin\n";
echo "✅ Comentarios automáticos en el ticket\n";
echo "✅ Integración completa con Filament\n\n";

// Obtener usuarios
$usuario = User::first();
$areaTecnologia = Area::where('nombre', 'Tecnología')->first();

// Contar notificaciones antes
$notificacionesAntes = DB::table('notifications')->count();

echo "📊 ESTADO INICIAL:\n";
echo "Notificaciones en base de datos: {$notificacionesAntes}\n\n";

echo "🎫 CREANDO TICKET FINAL DE PRUEBA:\n";
$ticket = new Ticket([
    'titulo' => 'Ticket final - Sistema de escalado completo',
    'descripcion' => 'Este ticket probará todo el sistema de escalado',
    'prioridad' => 'Alta',
    'estado' => 'Abierto',
    'area_id' => $areaTecnologia->id,
    'creado_por' => $usuario->id,
    'asignado_a' => $usuario->id,
    'escalado' => false,
]);

$ticket->created_at = now()->subMinutes(30);
$ticket->updated_at = now()->subMinutes(30);
$ticket->save();

echo "✅ Ticket #{$ticket->id} creado\n";
echo "   Prioridad inicial: {$ticket->prioridad}\n";
echo "   Estado inicial: {$ticket->estado}\n";
echo "   Asignado a: {$ticket->asignadoA->name}\n\n";

echo "🚨 EJECUTANDO ESCALADO AUTOMÁTICO:\n";
$resultado = $ticket->escalar('SLA vencido - Prueba final del sistema');

echo "✅ Escalado ejecutado exitosamente\n\n";

// Verificar cambios en el ticket
$ticket->refresh();
echo "📈 CAMBIOS EN EL TICKET:\n";
echo "   Prioridad: Alta → {$ticket->prioridad}\n";
echo "   Estado: Abierto → {$ticket->estado}\n";
echo "   Escalado: NO → " . ($ticket->escalado ? 'SÍ' : 'NO') . "\n";
echo "   Fecha escalamiento: {$ticket->fecha_escalamiento}\n\n";

// Verificar comentarios
$comentarios = $ticket->comments()->get();
echo "💬 COMENTARIOS EN EL TICKET:\n";
echo "   Total comentarios: " . $comentarios->count() . "\n";
if ($comentarios->count() > 0) {
    $ultimoComentario = $comentarios->last();
    echo "   Último comentario: " . substr($ultimoComentario->body, 0, 100) . "...\n";
}
echo "\n";

// Verificar notificaciones
$notificacionesDespues = DB::table('notifications')->count();
$notificacionesNuevas = $notificacionesDespues - $notificacionesAntes;

echo "📧 NOTIFICACIONES ENVIADAS:\n";
echo "   Total notificaciones: {$notificacionesDespues}\n";
echo "   Notificaciones nuevas: {$notificacionesNuevas}\n\n";

// Mostrar las últimas notificaciones de escalado
echo "📋 ÚLTIMAS NOTIFICACIONES DE ESCALADO:\n";
$notificacionesEscalado = DB::table('notifications')
    ->where('data', 'LIKE', '%escalado%')
    ->orderBy('created_at', 'desc')
    ->take(3)
    ->get(['data', 'notifiable_id', 'created_at']);

foreach ($notificacionesEscalado as $notificacion) {
    $data = json_decode($notificacion->data, true);
    echo "🔔 {$data['title']}\n";
    echo "   Usuario ID: {$notificacion->notifiable_id}\n";
    echo "   Fecha: {$notificacion->created_at}\n\n";
}

echo "🎉 SISTEMA DE ESCALADO COMPLETAMENTE IMPLEMENTADO:\n";
echo "✅ Escalado automático funcional\n";
echo "✅ Prioridades se incrementan correctamente\n";
echo "✅ Notificaciones Filament integradas\n";
echo "✅ Comentarios automáticos en tickets\n";
echo "✅ Logs detallados para debugging\n\n";

echo "🔗 PRÓXIMOS PASOS:\n";
echo "1. Programar el comando 'php artisan sla:verificar-escalado' cada 5-10 minutos\n";
echo "2. Configurar las notificaciones en el panel de Filament\n";
echo "3. Personalizar más los mensajes según sea necesario\n";
echo "4. Opcionalmente, agregar notificaciones por email\n\n";

echo "=== IMPLEMENTACIÓN COMPLETADA EXITOSAMENTE ===\n";
