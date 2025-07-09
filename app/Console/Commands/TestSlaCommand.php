<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Ticket;
use App\Models\Area;

class TestSlaCommand extends Command
{
    protected $signature = 'test:sla';
    protected $description = 'Test SLA calculation methods';

    public function handle()
    {
        $this->info('=== DIAGNÓSTICO DE CÁLCULO DE SLA ===');
        $this->info('=====================================');
        $this->newLine();

        // Obtener algunos tickets para probar
        $tickets = Ticket::with(['area', 'area.sla'])->limit(5)->get();

        if ($tickets->isEmpty()) {
            $this->warn('No hay tickets para probar');
            return;
        }

        foreach ($tickets as $ticket) {
            $this->info("TICKET #{$ticket->id}");
            $this->line("  Título: {$ticket->titulo}");
            $this->line("  Prioridad: {$ticket->prioridad}");
            $this->line("  Estado: {$ticket->estado}");
            $this->line("  Área: " . ($ticket->area ? $ticket->area->nombre : 'N/A'));
            $this->line("  Creado: {$ticket->created_at}");

            // Verificar SLA del área
            if ($ticket->area && $ticket->area->sla) {
                $slaArea = $ticket->area->sla;
                $this->line("  SLA del área:");
                $this->line("    - Tiempo respuesta: {$slaArea->tiempo_respuesta} mins");
                $this->line("    - Tiempo resolución: {$slaArea->tiempo_resolucion} mins");
            } else {
                $this->warn("  SLA del área: NO DISPONIBLE");
            }

            // Probar getSlaEfectivo
            $slaEfectivo = $ticket->getSlaEfectivo();
            if ($slaEfectivo) {
                $this->line("  SLA Efectivo (con prioridad):");
                $this->line("    - Tiempo respuesta: {$slaEfectivo['tiempo_respuesta']} mins");
                $this->line("    - Tiempo resolución: {$slaEfectivo['tiempo_resolucion']} mins");
                $this->line("    - Factor aplicado: {$slaEfectivo['factor_aplicado']}");
            } else {
                $this->error("  SLA Efectivo: NO DISPONIBLE");
            }

            // Probar tiempo restante
            $tiempoRestante = $ticket->getTiempoRestanteSla('respuesta');
            $this->line("  Tiempo restante: " . ($tiempoRestante !== null ? $tiempoRestante . " mins" : 'N/A'));

            // Probar estado SLA
            $estadoSla = $ticket->getEstadoSla();
            $this->line("  Estado SLA: {$estadoSla}");

            // Tiempo transcurrido
            $tiempoTranscurrido = $ticket->created_at->diffInMinutes(now());
            $this->line("  Tiempo transcurrido: {$tiempoTranscurrido} mins");

            $this->newLine();
            $this->line(str_repeat('-', 50));
            $this->newLine();
        }

        $this->info('=== VERIFICACIÓN DE ÁREAS Y SLAs ===');
        $this->info('=====================================');
        $this->newLine();

        $areas = Area::with('sla')->get();
        foreach ($areas as $area) {
            $this->info("ÁREA: {$area->nombre}");
            if ($area->sla) {
                $this->line("  SLA ID: {$area->sla->id}");
                $this->line("  Tiempo respuesta: {$area->sla->tiempo_respuesta} mins");
                $this->line("  Tiempo resolución: {$area->sla->tiempo_resolucion} mins");
                $this->line("  Activo: " . ($area->sla->activo ? 'SÍ' : 'NO'));
            } else {
                $this->warn("  SLA: NO ASIGNADO");
            }
            $this->newLine();
        }
    }
}
