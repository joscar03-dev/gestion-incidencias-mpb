<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Ticket;
use App\Models\Area;
use App\Models\User;

class ActualizarTiemposTicketsConSla extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tickets:actualizar-sla {--force : Sobrescribir tiempos existentes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualiza los tiempos de tickets basados en SLA del área al momento de creación';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando actualización de tiempos basados en SLA...');

        $tickets = Ticket::with(['area.slas', 'creadoPor'])->get();
        $this->output->progressStart($tickets->count());

        $updated = 0;

        foreach ($tickets as $ticket) {
            $changes = [];

            // Actualizar tiempo de respuesta si no existe o si se fuerza
            if ((!$ticket->tiempo_respuesta || $this->option('force'))) {
                $slaEfectivo = Ticket::calcularSlaEfectivoParaTicket($ticket);
                if ($slaEfectivo) {
                    $tiempoRespuestaMinutos = $slaEfectivo['tiempo_respuesta'];
                    $horas = floor($tiempoRespuestaMinutos / 60);
                    $minutos = $tiempoRespuestaMinutos % 60;
                    $changes['tiempo_respuesta'] = sprintf('%02d:%02d:00', min($horas, 838), $minutos);
                }
            }

            // Calcular tiempo de solución para tickets cerrados
            if ((!$ticket->tiempo_solucion || $this->option('force')) &&
                in_array($ticket->estado, ['Cerrado', 'Cancelado']) &&
                $ticket->fecha_resolucion) {
                $minutosSolucion = (int) $ticket->created_at->diffInMinutes($ticket->fecha_resolucion);
                $horas = floor($minutosSolucion / 60);
                $minutos = $minutosSolucion % 60;
                $changes['tiempo_solucion'] = sprintf('%02d:%02d:00', min($horas, 838), $minutos);
            }

            if (!empty($changes)) {
                try {
                    $ticket->update($changes);
                    $updated++;
                } catch (\Exception $e) {
                    $this->error("Error al actualizar ticket {$ticket->id}: " . $e->getMessage());
                }
            }

            $this->output->progressAdvance();
        }

        $this->output->progressFinish();

        $this->info("✅ Proceso completado. {$updated} tickets actualizados de {$tickets->count()} procesados.");

        return 0;
    }
}
