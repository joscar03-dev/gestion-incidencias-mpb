<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Ticket;
use App\Models\Area;
use App\Models\User;

class CreateTestTicketCommand extends Command
{
    protected $signature = 'create:test-ticket';
    protected $description = 'Create a test ticket for SLA testing';

    public function handle()
    {
        $user = User::first();
        $area = Area::first();

        // Crear ticket con fecha pasada para probar SLA vencido
        $ticket = new Ticket();
        $ticket->titulo = 'Test SLA ADVERTENCIA - ' . now()->format('Y-m-d H:i:s');
        $ticket->descripcion = 'Test para verificar cálculo de SLA en advertencia';
        $ticket->prioridad = 'Alta';
        $ticket->estado = 'Abierto';
        $ticket->area_id = $area->id;
        $ticket->creado_por = $user->id;
        $ticket->asignado_a = $user->id;

        // Establecer fecha de creación hace 12 minutos (para estar en advertencia)
        $ticket->created_at = now()->subMinutes(12);
        $ticket->updated_at = now()->subMinutes(12);

        // Guardar sin disparar eventos
        $ticket->saveQuietly();

        $this->info("Ticket creado con ID: {$ticket->id}");
        $this->info("Título: {$ticket->titulo}");
        $this->info("Prioridad: {$ticket->prioridad}");
        $this->info("Área: {$ticket->area->nombre}");
        $this->info("Creado: {$ticket->created_at}");

        // Probar inmediatamente el SLA
        $this->newLine();
        $this->info("=== PRUEBA DE SLA INMEDIATA ===");

        $slaEfectivo = $ticket->getSlaEfectivo();
        if ($slaEfectivo) {
            $this->info("SLA Efectivo (con prioridad):");
            $this->info("  - Tiempo respuesta: {$slaEfectivo['tiempo_respuesta']} mins");
            $this->info("  - Tiempo resolución: {$slaEfectivo['tiempo_resolucion']} mins");
            $this->info("  - Factor aplicado: {$slaEfectivo['factor_aplicado']}");
        }

        $tiempoRestante = $ticket->getTiempoRestanteSla('respuesta');
        $this->info("Tiempo restante: " . ($tiempoRestante !== null ? $tiempoRestante . " mins" : 'N/A'));

        $estadoSla = $ticket->getEstadoSla();
        $this->info("Estado SLA: {$estadoSla}");

        $tiempoTranscurrido = $ticket->created_at->diffInMinutes(now());
        $this->info("Tiempo transcurrido: {$tiempoTranscurrido} mins");
    }
}
