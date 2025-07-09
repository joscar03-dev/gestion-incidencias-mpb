<?php

namespace App\Console\Commands;

use App\Models\Ticket;
use Illuminate\Console\Command;

class VerificarEscaladoSla extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sla:verificar-escalado {--force : Forzar escalado de tickets específicos}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica y escala automáticamente tickets según SLA';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Verificando escalado de tickets según SLA...');
        $this->newLine();

        // Obtener tickets activos que no están escalados
        $tickets = Ticket::whereNotIn('estado', ['Cerrado', 'Archivado'])
            ->where('escalado', false)
            ->with(['area', 'area.slas'])
            ->get();

        $this->info("📋 Tickets activos encontrados: {$tickets->count()}");
        $this->newLine();

        $ticketsEscalados = 0;
        $ticketsVencidos = 0;

        foreach ($tickets as $ticket) {
            $this->line("Verificando Ticket #{$ticket->id}: {$ticket->titulo}");

            // Calcular tiempo transcurrido
            $tiempoTranscurrido = abs(now()->diffInMinutes($ticket->created_at));
            $this->line("  ⏱️  Tiempo transcurrido: {$tiempoTranscurrido} minutos");

            // Verificar SLA del área
            if ($ticket->area && $ticket->area->slas->isNotEmpty()) {
                $sla = $ticket->area->slas->first();
                $this->line("  📊 SLA área: {$ticket->area->nombre}");
                $this->line("     - Escalamiento automático: " . ($sla->escalamiento_automatico ? 'SÍ' : 'NO'));
                $this->line("     - Tiempo escalamiento: {$sla->tiempo_escalamiento} min");

                // Verificar si debe escalar
                if ($ticket->debeEscalar()) {
                    $this->warn("  🚨 ESCALANDO ticket #{$ticket->id}");

                    try {
                        $ticket->escalar('Escalado automático por SLA');
                        $ticketsEscalados++;
                        $this->info("  ✅ Ticket escalado exitosamente");
                    } catch (\Exception $e) {
                        $this->error("  ❌ Error escalando ticket: " . $e->getMessage());
                    }
                } else {
                    $this->line("  ✅ Ticket dentro del SLA");
                }

                // Verificar si está vencido
                if ($ticket->estaVencido('respuesta') && !$ticket->sla_vencido) {
                    $ticket->update(['sla_vencido' => true]);
                    $ticketsVencidos++;
                    $this->warn("  ⚠️  SLA vencido - marcado como tal");
                }

            } else {
                $this->line("  ⚠️  Sin SLA configurado para área");
            }

            $this->newLine();
        }

        // Resumen
        $this->info('📊 RESUMEN:');
        $this->info("  - Tickets verificados: {$tickets->count()}");
        $this->info("  - Tickets escalados: {$ticketsEscalados}");
        $this->info("  - Tickets marcados como vencidos: {$ticketsVencidos}");

        if ($ticketsEscalados > 0) {
            $this->warn("🚨 Se escalaron {$ticketsEscalados} tickets");
        } else {
            $this->info("✅ No fue necesario escalar ningún ticket");
        }

        return Command::SUCCESS;
    }
}
