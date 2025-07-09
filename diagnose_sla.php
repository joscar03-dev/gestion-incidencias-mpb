<?php

require_once 'vendor/autoload.php';

// Configurar Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Sla;
use App\Models\Area;

echo "=== DIAGNÓSTICO DE SLAS ===\n\n";

// Mostrar todos los SLAs
$slas = Sla::with('area')->get();

echo "📋 SLAs en la base de datos:\n";
foreach ($slas as $sla) {
    echo "   ID: {$sla->id} - Área: {$sla->area->nombre} (ID: {$sla->area_id})\n";
}

echo "\n📊 Estadísticas:\n";
echo "   Total de SLAs: " . $slas->count() . "\n";
echo "   Total de áreas: " . Area::count() . "\n";

// Verificar duplicados
$duplicados = Sla::select('area_id')
    ->groupBy('area_id')
    ->havingRaw('COUNT(*) > 1')
    ->get();

if ($duplicados->count() > 0) {
    echo "\n❌ ÁREAS CON MÚLTIPLES SLAs:\n";
    foreach ($duplicados as $dup) {
        $area = Area::find($dup->area_id);
        $slasArea = Sla::where('area_id', $dup->area_id)->get();
        echo "   Área: {$area->nombre} - SLAs: " . $slasArea->pluck('id')->implode(', ') . "\n";
    }
} else {
    echo "\n✅ NO HAY DUPLICADOS - Cada área tiene un solo SLA\n";
}

// Verificar índices
echo "\n🔍 Verificando índices de la tabla slas:\n";
$indices = \Illuminate\Support\Facades\DB::select("SHOW INDEX FROM slas WHERE Key_name = 'slas_area_id_unique'");
if (count($indices) > 0) {
    echo "   ✅ Índice único en area_id existe\n";
} else {
    echo "   ❌ Índice único en area_id NO existe\n";
}

echo "\n=== DIAGNÓSTICO COMPLETADO ===\n";
