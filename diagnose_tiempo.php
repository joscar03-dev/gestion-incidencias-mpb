<?php

require_once 'vendor/autoload.php';

// Configurar Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Ticket;
use Carbon\Carbon;

echo "=== DIAGNÓSTICO DE TIEMPO ===\n";
echo "==============================\n\n";

// Verificar configuración de tiempo
echo "Fecha actual (now()): " . now() . "\n";
echo "Zona horaria configurada: " . config('app.timezone') . "\n";
echo "Zona horaria PHP: " . date_default_timezone_get() . "\n\n";

// Obtener algunos tickets para verificar
$tickets = Ticket::orderBy('created_at', 'desc')->take(3)->get();

foreach ($tickets as $ticket) {
    echo "TICKET #{$ticket->id}\n";
    echo "  created_at: {$ticket->created_at}\n";
    echo "  created_at raw: {$ticket->created_at->toISOString()}\n";
    echo "  now(): " . now() . "\n";
    echo "  now() raw: " . now()->toISOString() . "\n";

    // Diferentes formas de calcular la diferencia
    $diff1 = now()->diffInMinutes($ticket->created_at);
    $diff2 = $ticket->created_at->diffInMinutes(now());
    $diff3 = abs(now()->diffInMinutes($ticket->created_at));

    echo "  Diferencia 1 (now()->diffInMinutes(created_at)): {$diff1}\n";
    echo "  Diferencia 2 (created_at->diffInMinutes(now())): {$diff2}\n";
    echo "  Diferencia 3 (abs): {$diff3}\n";

    // Verificar si created_at es mayor que now()
    if ($ticket->created_at > now()) {
        echo "  ⚠️  PROBLEMA: created_at es mayor que now()\n";
    }

    echo "\n" . str_repeat("-", 40) . "\n\n";
}

echo "=== DIAGNÓSTICO COMPLETADO ===\n";
