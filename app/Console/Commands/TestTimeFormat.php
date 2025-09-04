<?php

namespace App\Console\Commands;

use App\Models\Ticket;
use Illuminate\Console\Command;

class TestTimeFormat extends Command
{
    protected $signature = 'test:time-format';
    protected $description = 'Probar el nuevo formato de tiempo en tickets';

    public function handle()
    {
        $this->info('🕐 Probando formato de tiempo mejorado...');

        // Ejemplos de diferentes tiempos en minutos
        $tiempos = [
            5,      // 5 minutos
            45,     // 45 minutos
            90,     // 1h 30m
            300,    // 5h
            1440,   // 1 día
            1500,   // 1d 1h
            2880,   // 2 días
            4320,   // 3 días
            1456,   // Tu ejemplo: 1d 0h 16m
            0,      // 0 minutos
            -10,    // Tiempo negativo (vencido)
        ];

        $this->table(
            ['Minutos', 'Formato Anterior', 'Formato Nuevo'],
            collect($tiempos)->map(function ($minutos) {
                $formatoAnterior = $this->formatoAnterior($minutos);
                $formatoNuevo = Ticket::formatTiempo($minutos);
                return [$minutos, $formatoAnterior, $formatoNuevo];
            })
        );

        // Probar con un ticket real
        $this->newLine();
        $this->info('📋 Probando con ticket real...');

        $ticket = Ticket::first();
        if ($ticket) {
            $tiempo = $ticket->getTiempoRestanteSla('respuesta');
            $this->line("Ticket #{$ticket->id}:");
            $this->line("   Tiempo restante: " . ($tiempo !== null ? Ticket::formatTiempo($tiempo) : 'N/A'));
            $this->line("   Estado SLA: {$ticket->getEstadoSla()}");
        }

        $this->newLine();
        $this->info('✅ Prueba completada. El formato ahora muestra días cuando es necesario.');
    }

    private function formatoAnterior($minutos)
    {
        if ($minutos <= 0) return 'Vencido';
        if ($minutos == 0) return '0m';

        $horas = floor($minutos / 60);
        $mins = $minutos % 60;

        if ($horas > 0) {
            return "{$horas}h {$mins}m";
        }
        return "{$mins}m";
    }
}
