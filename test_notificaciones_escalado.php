<?php

require_once 'vendor/autoload.php';

// Configurar Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Ticket;
use App\Models\Area;
use App\Models\User;
use Illuminate\Support\Facades\DB;

echo "=== PRUEBA DE NOTIFICACIONES DE ESCALADO ===\n";
echo "=============================================\n\n";

// Obtener usuarios
$usuario = User::first();
$areaTecnologia = Area::where('nombre', 'Tecnología')->first();

// Contar notificaciones antes
$notificacionesAntes = DB::table('notifications')->count();
echo "📊 Notificaciones antes: {$notificacionesAntes}\n\n";

// Crear un ticket simple para escalado
echo "🎯 Creando ticket de prueba...\n";
$ticket = new Ticket([
    'titulo' => 'Test notificaciones escalado',
    'descripcion' => 'Prueba de notificaciones con Filament',
    'prioridad' => 'Media',
    'estado' => 'Abierto',
    'area_id' => $areaTecnologia->id,
    'creado_por' => $usuario->id,
    'asignado_a' => $usuario->id,
    'escalado' => false,
]);

$ticket->created_at = now()->subMinutes(30);
$ticket->updated_at = now()->subMinutes(30);
$ticket->save();

echo "✅ Ticket #{$ticket->id} creado\n\n";

echo "🚨 Ejecutando escalado...\n";
try {
    $resultado = $ticket->escalar('Prueba de notificaciones Filament');
    echo "✅ Escalado ejecutado: " . ($resultado ? 'SÍ' : 'NO') . "\n\n";
} catch (\Exception $e) {
    echo "❌ Error en escalado: " . $e->getMessage() . "\n\n";
}

// Contar notificaciones después
$notificacionesDespues = DB::table('notifications')->count();
echo "📊 Notificaciones después: {$notificacionesDespues}\n";
echo "📈 Notificaciones nuevas: " . ($notificacionesDespues - $notificacionesAntes) . "\n\n";

// Mostrar las últimas notificaciones
echo "📋 Últimas 3 notificaciones creadas:\n";
$ultimasNotificaciones = DB::table('notifications')
    ->orderBy('created_at', 'desc')
    ->take(3)
    ->get(['data', 'notifiable_id', 'created_at']);

foreach ($ultimasNotificaciones as $notificacion) {
    $data = json_decode($notificacion->data, true);
    echo "🔔 {$data['title']}\n";
    echo "   Usuario ID: {$notificacion->notifiable_id}\n";
    echo "   Fecha: {$notificacion->created_at}\n\n";
}

echo "=== PRUEBA COMPLETADA ===\n";
