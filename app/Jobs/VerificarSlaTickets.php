<?php

namespace App\Jobs;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class VerificarSlaTickets implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Iniciando verificación automática de SLA');

        // Obtener tickets activos (no cerrados ni archivados)
        $ticketsActivos = Ticket::whereNotIn('estado', [
            Ticket::ESTADOS['Cerrado'],
            Ticket::ESTADOS['Archivado']
        ])->get();

        $ticketsEscalados = 0;
        $ticketsVencidos = 0;

        foreach ($ticketsActivos as $ticket) {
            try {
                // Verificar y escalar si es necesario
                if ($ticket->verificarSlaYEscalamiento()) {
                    $ticketsEscalados++;
                    Log::info("Ticket #{$ticket->id} escalado automáticamente");
                }

                // Marcar como vencido si corresponde
                if ($ticket->estaVencido('respuesta') && !$ticket->sla_vencido) {
                    $ticket->update(['sla_vencido' => true]);
                    $ticketsVencidos++;
                    Log::warning("Ticket #{$ticket->id} marcado como SLA vencido");
                }

            } catch (\Exception $e) {
                Log::error("Error verificando SLA del ticket #{$ticket->id}: " . $e->getMessage());
            }
        }

        Log::info("Verificación de SLA completada. Escalados: {$ticketsEscalados}, Vencidos: {$ticketsVencidos}");
    }
}
