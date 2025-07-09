<?php

require_once 'vendor/autoload.php';

// Configurar Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Sla;
use App\Models\Area;

echo "=== PRUEBA DE OVERRIDE EN FORMULARIO ===\n\n";

// Obtener todos los SLAs
$slas = Sla::with('area')->get();

foreach ($slas as $sla) {
    echo "📋 SLA del área: {$sla->area->nombre}\n";
    echo "   Tiempo base respuesta: {$sla->tiempo_respuesta} min\n";
    echo "   Tiempo base resolución: {$sla->tiempo_resolucion} min\n";
    echo "   Override activado: " . ($sla->override_area ? 'SÍ' : 'NO') . "\n";

    // Probar todas las prioridades
    $prioridades = ['critico', 'alto', 'medio', 'bajo'];

    foreach ($prioridades as $prioridad) {
        $slaCalculado = $sla->calcularSlaEfectivo($prioridad);

        echo "   🔸 Prioridad {$prioridad}: ";
        echo "Respuesta {$slaCalculado['tiempo_respuesta']}m, ";
        echo "Resolución {$slaCalculado['tiempo_resolucion']}m ";
        echo "(" . ($slaCalculado['override_aplicado'] ? 'Override aplicado' : 'Tiempo fijo') . ")\n";
    }

    echo "\n";
}

echo "✅ Prueba completada.\n";
echo "Si el override está DESACTIVADO, todos los tiempos deberían ser iguales.\n";
echo "Si el override está ACTIVADO, los tiempos deberían variar según la prioridad.\n";
