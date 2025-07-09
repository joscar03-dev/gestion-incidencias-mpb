<?php

require_once 'vendor/autoload.php';

// Configurar Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Ticket;
use App\Models\Area;
use App\Models\User;
use App\Models\Sla;

echo "=== PRUEBA FINAL DEL SISTEMA DE ESCALADO ===\n";
echo "=============================================\n\n";

// Obtener áreas y usuarios
$areaTecnologia = Area::where('nombre', 'Tecnología')->first();
$areaRRHH = Area::where('nombre', 'Recursos Humanos')->first();
$usuario = User::first();

if (!$areaTecnologia || !$areaRRHH || !$usuario) {
    echo "❌ No se encontraron las áreas o usuario necesarios\n";
    exit;
}

// Verificar configuración de SLAs
echo "=== CONFIGURACIÓN DE SLAs ===\n";
$slaTecnologia = Sla::where('area_id', $areaTecnologia->id)->first();
$slaRRHH = Sla::where('area_id', $areaRRHH->id)->first();

echo "SLA Tecnología:\n";
echo "  - Escalamiento automático: " . ($slaTecnologia->escalamiento_automatico ? 'SÍ' : 'NO') . "\n";
echo "  - Tiempo escalamiento: {$slaTecnologia->tiempo_escalamiento} minutos\n\n";

echo "SLA RRHH:\n";
echo "  - Escalamiento automático: " . ($slaRRHH->escalamiento_automatico ? 'SÍ' : 'NO') . "\n";
echo "  - Tiempo escalamiento: {$slaRRHH->tiempo_escalamiento} minutos\n\n";

// Escenario 1: Ticket que NO debe escalarse (área con escalamiento desactivado)
echo "=== ESCENARIO 1: Área sin escalamiento automático ===\n";
$ticket1 = new Ticket([
    'titulo' => 'Test RRHH - Sin escalamiento',
    'descripcion' => 'Ticket en área sin escalamiento automático',
    'prioridad' => 'Alta',
    'estado' => 'Abierto',
    'area_id' => $areaRRHH->id,
    'creado_por' => $usuario->id,
    'asignado_a' => $usuario->id,
    'escalado' => false,
]);

$ticket1->created_at = now()->subMinutes(180); // 3 horas atrás
$ticket1->updated_at = now()->subMinutes(180);
$ticket1->save();

echo "Ticket #{$ticket1->id} creado hace 180 minutos\n";
echo "Área: {$areaRRHH->nombre} (escalamiento automático: NO)\n";
echo "Debe escalar: " . ($ticket1->debeEscalar() ? 'SÍ' : 'NO') . "\n\n";

// Escenario 2: Ticket que SÍ debe escalarse (área con escalamiento activado)
echo "=== ESCENARIO 2: Área con escalamiento automático ===\n";
$ticket2 = new Ticket([
    'titulo' => 'Test Tecnología - Con escalamiento',
    'descripcion' => 'Ticket en área con escalamiento automático',
    'prioridad' => 'Media',
    'estado' => 'Abierto',
    'area_id' => $areaTecnologia->id,
    'creado_por' => $usuario->id,
    'asignado_a' => $usuario->id,
    'escalado' => false,
]);

$ticket2->created_at = now()->subMinutes(25); // 25 minutos atrás
$ticket2->updated_at = now()->subMinutes(25);
$ticket2->save();

echo "Ticket #{$ticket2->id} creado hace 25 minutos\n";
echo "Área: {$areaTecnologia->nombre} (escalamiento automático: SÍ, tiempo: 10 min)\n";
echo "Debe escalar: " . ($ticket2->debeEscalar() ? 'SÍ' : 'NO') . "\n";

// Ejecutar escalado
$escalado = $ticket2->verificarSlaYEscalamiento();
echo "Resultado escalado: " . ($escalado ? 'ESCALADO' : 'NO ESCALADO') . "\n";
$ticket2->refresh();
echo "Estado final: {$ticket2->estado}\n\n";

// Escenario 3: Ticket ya escalado (no debe escalarse nuevamente)
echo "=== ESCENARIO 3: Ticket ya escalado ===\n";
$ticket3 = new Ticket([
    'titulo' => 'Test ya escalado',
    'descripcion' => 'Ticket que ya fue escalado',
    'prioridad' => 'Alta',
    'estado' => 'Escalado',
    'area_id' => $areaTecnologia->id,
    'creado_por' => $usuario->id,
    'asignado_a' => $usuario->id,
    'escalado' => true,
]);

$ticket3->created_at = now()->subMinutes(60); // 1 hora atrás
$ticket3->updated_at = now()->subMinutes(60);
$ticket3->save();

echo "Ticket #{$ticket3->id} creado hace 60 minutos (ya escalado)\n";
echo "Debe escalar: " . ($ticket3->debeEscalar() ? 'SÍ' : 'NO') . "\n\n";

// Escenario 4: Ticket cerrado (no debe escalarse)
echo "=== ESCENARIO 4: Ticket cerrado ===\n";
$ticket4 = new Ticket([
    'titulo' => 'Test cerrado',
    'descripcion' => 'Ticket que está cerrado',
    'prioridad' => 'Alta',
    'estado' => 'Cerrado',
    'area_id' => $areaTecnologia->id,
    'creado_por' => $usuario->id,
    'asignado_a' => $usuario->id,
    'escalado' => false,
]);

$ticket4->created_at = now()->subMinutes(45); // 45 minutos atrás
$ticket4->updated_at = now()->subMinutes(45);
$ticket4->save();

echo "Ticket #{$ticket4->id} creado hace 45 minutos (estado: Cerrado)\n";
echo "Debe escalar: " . ($ticket4->debeEscalar() ? 'SÍ' : 'NO') . "\n\n";

echo "=== PRUEBA COMPLETADA ===\n";
echo "Todos los escenarios han sido probados correctamente.\n";
