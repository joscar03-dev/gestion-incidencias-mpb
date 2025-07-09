<?php

require_once 'vendor/autoload.php';

// Configurar Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Sla;
use App\Models\Area;

echo "=== PRUEBA DE OVERRIDE SLA ===\n\n";

// Obtener un SLA de prueba (o crear uno)
$sla = Sla::first();

if (!$sla) {
    echo "❌ No se encontró ningún SLA en la base de datos.\n";
    echo "Crea un SLA desde el panel de administración primero.\n";
    exit;
}

echo "📋 SLA de prueba:\n";
echo "   Área: " . $sla->area->nombre . "\n";
echo "   Tiempo respuesta base: {$sla->tiempo_respuesta} minutos\n";
echo "   Tiempo resolución base: {$sla->tiempo_resolucion} minutos\n";
echo "   Override activado: " . ($sla->override_area ? 'SÍ' : 'NO') . "\n\n";

// Probar diferentes prioridades
$prioridades = ['critico', 'alto', 'medio', 'bajo'];

foreach ($prioridades as $prioridad) {
    echo "🔍 Probando prioridad: " . strtoupper($prioridad) . "\n";

    $resultado = $sla->calcularSlaEfectivo($prioridad);

    echo "   Tiempo respuesta: {$resultado['tiempo_respuesta']} minutos\n";
    echo "   Tiempo resolución: {$resultado['tiempo_resolucion']} minutos\n";
    echo "   Override aplicado: " . ($resultado['override_aplicado'] ? 'SÍ' : 'NO') . "\n";

    if (isset($resultado['factor_aplicado'])) {
        echo "   Factor aplicado: {$resultado['factor_aplicado']} (" . ($resultado['factor_aplicado'] * 100) . "%)\n";
    }

    echo "\n";
}

echo "✅ Prueba completada.\n";
echo "\nSi el override está DESACTIVADO, todos los resultados deberían ser iguales a los tiempos base.\n";
echo "Si el override está ACTIVADO, los tiempos deberían variar según la prioridad.\n";
