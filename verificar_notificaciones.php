<?php

require_once 'vendor/autoload.php';

// Configurar Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== VERIFICANDO NOTIFICACIONES DE ESCALADO ===\n";
echo "===============================================\n\n";

// Verificar cuántas notificaciones hay
$totalNotificaciones = DB::table('notifications')->count();
echo "📊 Total de notificaciones en la base de datos: {$totalNotificaciones}\n\n";

// Obtener las últimas 10 notificaciones
$notificaciones = DB::table('notifications')
    ->orderBy('created_at', 'desc')
    ->take(10)
    ->get(['data', 'notifiable_id', 'created_at']);

echo "📋 Últimas 10 notificaciones:\n";
echo str_repeat("-", 50) . "\n";

foreach ($notificaciones as $notificacion) {
    $data = json_decode($notificacion->data, true);
    echo "🔔 {$data['title']}\n";
    echo "   Usuario ID: {$notificacion->notifiable_id}\n";
    echo "   Fecha: {$notificacion->created_at}\n";

    // Mostrar parte del mensaje si existe
    if (isset($data['body'])) {
        $bodyPreview = substr($data['body'], 0, 100) . '...';
        echo "   Mensaje: {$bodyPreview}\n";
    }

    echo "\n";
}

echo "=== VERIFICACIÓN COMPLETADA ===\n";
echo "Las notificaciones se pueden ver en el panel de Filament.\n";
echo "Ve a /admin o /reportar y verifica el icono de notificaciones.\n";
