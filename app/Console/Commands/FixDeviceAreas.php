<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Dispositivo;
use App\Models\User;

class FixDeviceAreas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dispositivos:fix-areas';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Asigna el área correcta a dispositivos que tienen usuario pero no área';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando corrección de áreas de dispositivos...');

        // Buscar dispositivos que tienen usuario asignado pero no área
        $dispositivos = Dispositivo::whereNotNull('usuario_id')
            ->whereNull('area_id')
            ->with('usuario')
            ->get();

        if ($dispositivos->isEmpty()) {
            $this->info('No se encontraron dispositivos sin área que necesiten corrección.');
            return;
        }

        $this->info("Se encontraron {$dispositivos->count()} dispositivos para corregir.");

        $corregidos = 0;
        $errores = 0;

        foreach ($dispositivos as $dispositivo) {
            try {
                if ($dispositivo->usuario && $dispositivo->usuario->area_id) {
                    $dispositivo->update(['area_id' => $dispositivo->usuario->area_id]);
                    $this->line("✓ Dispositivo #{$dispositivo->id} ({$dispositivo->nombre}) asignado al área: {$dispositivo->usuario->area->nombre}");
                    $corregidos++;
                } else {
                    $this->warn("⚠ Dispositivo #{$dispositivo->id} ({$dispositivo->nombre}) - Usuario sin área asignada");
                    $errores++;
                }
            } catch (\Exception $e) {
                $this->error("✗ Error al corregir dispositivo #{$dispositivo->id}: {$e->getMessage()}");
                $errores++;
            }
        }

        $this->info("\nResumen:");
        $this->info("- Dispositivos corregidos: {$corregidos}");
        if ($errores > 0) {
            $this->warn("- Errores encontrados: {$errores}");
        }
        $this->info("Corrección completada.");
    }
}
