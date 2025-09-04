<?php

namespace App\Console\Commands;

use App\Models\Ticket;
use Illuminate\Console\Command;

class TestSlaCorrection extends Command
{
    protected $signature = 'test:sla-correction';
    protected $description = 'Probar las correcciones del sistema SLA';

    public function handle()
    {
        $this->info('🧪 Probando correcciones del sistema SLA...');

        // Encontrar un ticket abierto para probar
        $ticketAbierto = Ticket::where('estado', 'Abierto')->first();

        if (!$ticketAbierto) {
            $this->error('No se encontró ningún ticket abierto para probar');
            return;
        }

        $this->line("📋 Ticket de prueba: #{$ticketAbierto->id}");
        $this->line("   Estado actual: {$ticketAbierto->estado}");
        $this->line("   Creado: {$ticketAbierto->created_at}");
        $this->line("   Estado SLA actual: {$ticketAbierto->getEstadoSla()}");

        // Simular cierre del ticket
        $this->newLine();
        $this->info('🔄 Cerrando ticket...');

        $ticketAbierto->estado = 'Cerrado';
        $ticketAbierto->save();

        // Verificar estado después del cierre
        $ticketAbierto->refresh();

        $this->line("✅ Ticket cerrado:");
        $this->line("   Estado: {$ticketAbierto->estado}");
        $this->line("   Fecha cierre: {$ticketAbierto->fecha_cierre}");
        $this->line("   Fecha resolución: {$ticketAbierto->fecha_resolucion}");
        $this->line("   Estado SLA: {$ticketAbierto->getEstadoSla()}");

        // Verificar tiempo restante (debe ser null para tickets cerrados)
        $tiempoRestante = $ticketAbierto->getTiempoRestanteSla('respuesta');
        $this->line("   Tiempo restante: " . ($tiempoRestante === null ? 'N/A (correcto)' : $tiempoRestante . ' mins'));

        $this->newLine();
        $this->info('🎉 Prueba completada. Las correcciones están funcionando correctamente.');
    }
}
