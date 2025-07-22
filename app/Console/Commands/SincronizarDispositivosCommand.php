<?php

namespace App\Console\Commands;

use App\Models\Dispositivo;
use App\Models\DispositivoAsignacion;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SincronizarDispositivosCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dispositivos:sincronizar {--fix : Corregir las inconsistencias encontradas}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincroniza el estado de los dispositivos con sus asignaciones';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando sincronización de dispositivos...');

        // 1. Buscar dispositivos con usuario_id pero sin asignaciones activas
        $dispositivosConUsuarioSinAsignacion = Dispositivo::whereNotNull('usuario_id')
            ->whereDoesntHave('asignaciones', function($query) {
                $query->whereNull('fecha_desasignacion');
            })
            ->get();

        $countInconsistentes = $dispositivosConUsuarioSinAsignacion->count();

        if ($countInconsistentes > 0) {
            $this->warn("Se encontraron {$countInconsistentes} dispositivos inconsistentes (con usuario_id pero sin asignación activa)");
            
            if ($this->option('fix')) {
                $this->info("Corrigiendo dispositivos inconsistentes...");
                
                foreach ($dispositivosConUsuarioSinAsignacion as $dispositivo) {
                    $oldUserId = $dispositivo->usuario_id;
                    $dispositivo->update([
                        'usuario_id' => null,
                        'estado' => 'Disponible'
                    ]);
                    $this->line("Corregido dispositivo #{$dispositivo->id} ({$dispositivo->nombre}) - Usuario #{$oldUserId} eliminado");
                }
                
                $this->info("✅ {$countInconsistentes} dispositivos corregidos");
            } else {
                $this->info("Ejecute el comando con la opción --fix para corregir estas inconsistencias");
            }
        } else {
            $this->info("✅ No se encontraron inconsistencias entre dispositivos y asignaciones");
        }

        // 2. Verificar dispositivos con asignaciones activas pero sin usuario_id
        $dispositivosConAsignacionSinUsuario = Dispositivo::where(function($query) {
                $query->whereNull('usuario_id')->orWhere('estado', '!=', 'Asignado');
            })
            ->whereHas('asignaciones', function($query) {
                $query->whereNull('fecha_desasignacion');
            })
            ->get();

        $countInconsistentes2 = $dispositivosConAsignacionSinUsuario->count();

        if ($countInconsistentes2 > 0) {
            $this->warn("Se encontraron {$countInconsistentes2} dispositivos con asignaciones activas pero sin usuario_id o estado incorrecto");
            
            if ($this->option('fix')) {
                $this->info("Corrigiendo dispositivos con asignaciones activas...");
                
                foreach ($dispositivosConAsignacionSinUsuario as $dispositivo) {
                    $asignacionActiva = $dispositivo->asignaciones()
                        ->whereNull('fecha_desasignacion')
                        ->first();
                        
                    if ($asignacionActiva) {
                        $dispositivo->update([
                            'usuario_id' => $asignacionActiva->user_id,
                            'estado' => 'Asignado'
                        ]);
                        $this->line("Corregido dispositivo #{$dispositivo->id} ({$dispositivo->nombre}) - Asignado a usuario #{$asignacionActiva->user_id}");
                    }
                }
                
                $this->info("✅ {$countInconsistentes2} dispositivos corregidos");
            } else {
                $this->info("Ejecute el comando con la opción --fix para corregir estas inconsistencias");
            }
        } else {
            $this->info("✅ No se encontraron dispositivos con asignaciones activas pero sin usuario asignado");
        }

        return Command::SUCCESS;
    }
}
