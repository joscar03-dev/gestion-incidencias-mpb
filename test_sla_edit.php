<?php

require_once 'vendor/autoload.php';

// Configurar Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Sla;

echo "=== PRUEBA DE EDICIÓN SLA ===\n\n";

// Obtener un SLA existente
$sla = Sla::first();

if (!$sla) {
    echo "❌ No se encontró ningún SLA en la base de datos.\n";
    exit;
}

echo "📋 SLA encontrado:\n";
echo "   ID: {$sla->id}\n";
echo "   Área: {$sla->area->nombre}\n";
echo "   Descripción actual: " . ($sla->descripcion ?? 'Sin descripción') . "\n\n";

// Intentar actualizar la descripción
try {
    $sla->descripcion = 'Prueba de edición - ' . now();
    $sla->save();

    echo "✅ SLA actualizado correctamente\n";
    echo "   Nueva descripción: {$sla->descripcion}\n";

} catch (\Exception $e) {
    echo "❌ Error al actualizar el SLA: " . $e->getMessage() . "\n";
}

// Intentar cambiar el área_id (esto debería fallar)
try {
    $otraArea = \App\Models\Area::where('id', '!=', $sla->area_id)->first();

    if ($otraArea) {
        echo "\n🔄 Intentando cambiar área a: {$otraArea->nombre}\n";
        $sla->area_id = $otraArea->id;
        $sla->save();

        echo "✅ Área cambiada correctamente\n";
    } else {
        echo "\n⚠️ No se encontró otra área para probar el cambio\n";
    }

} catch (\Exception $e) {
    echo "❌ Error al cambiar área (esto es esperado): " . $e->getMessage() . "\n";
}

echo "\n=== PRUEBA COMPLETADA ===\n";
