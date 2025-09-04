<?php

namespace App\Console\Commands;

use App\Models\Ticket;
use Illuminate\Console\Command;

class FixTicketResolutionDates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tickets:fix-resolution-dates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Arregla las fechas de resolución faltantes en tickets cerrados';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Buscando tickets cerrados sin fecha_resolucion...');

        $ticketsSinFechaResolucion = Ticket::whereIn('estado', [
            Ticket::ESTADOS['Cerrado'],
            Ticket::ESTADOS['Cancelado']
        ])
            ->whereNull('fecha_resolucion')
            ->get();

        $this->info("Encontrados {$ticketsSinFechaResolucion->count()} tickets sin fecha de resolución");

        $arreglados = 0;

        foreach ($ticketsSinFechaResolucion as $ticket) {
            // Usar fecha_cierre si existe, si no usar updated_at
            $fechaResolucion = $ticket->fecha_cierre ?? $ticket->updated_at;

            $ticket->update(['fecha_resolucion' => $fechaResolucion]);

            $this->line("✅ Ticket #{$ticket->id}: fecha_resolucion = {$fechaResolucion}");
            $arreglados++;
        }

        $this->info("🎉 Proceso completado. {$arreglados} tickets arreglados.");

        // Mostrar ejemplo de SLA corregido
        if ($arreglados > 0) {
            $ticketEjemplo = $ticketsSinFechaResolucion->first();
            $ticketEjemplo->refresh();

            $this->newLine();
            $this->info("📊 Ejemplo - Ticket #{$ticketEjemplo->id}:");
            $this->line("   Estado: {$ticketEjemplo->estado}");
            $this->line("   Creado: {$ticketEjemplo->created_at}");
            $this->line("   Cerrado: {$ticketEjemplo->fecha_cierre}");
            $this->line("   Resuelto: {$ticketEjemplo->fecha_resolucion}");
            $this->line("   Estado SLA: {$ticketEjemplo->getEstadoSla()}");
        }
    }
}
