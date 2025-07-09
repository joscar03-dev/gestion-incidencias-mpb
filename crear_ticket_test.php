<?php

require_once 'vendor/autoload.php';

// Configurar Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Ticket;

// Crear ticket que debería escalarse automáticamente
$ticket = Ticket::create([
    'titulo' => 'Test escalado automático',
    'descripcion' => 'Ticket creado para probar escalado automático',
    'prioridad' => 'Alta',
    'estado' => 'Abierto',
    'area_id' => 1,
    'creado_por' => 1,
    'asignado_a' => 1,
    'created_at' => \Carbon\Carbon::now()->subMinutes(20), // 20 minutos atrás
]);

echo "✅ Ticket creado: #{$ticket->id}\n";
echo "   Tiempo: hace 15 minutos\n";
echo "   Debería escalarse con SLA de 10 minutos\n";
