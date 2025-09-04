<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Categoria;
use App\Models\Ticket;

class ItilIntegrationSummary extends Command
{
    protected $signature = 'itil:summary';
    protected $description = 'Muestra un resumen completo de la integración ITIL';

    public function handle()
    {
        $this->info('🎯 RESUMEN DE INTEGRACIÓN ITIL v4');
        $this->info('================================');

        // Estadísticas de categorías
        $this->line('');
        $this->info('📊 CATEGORÍAS ITIL:');
        $this->line('──────────────────');

        $totalCategorias = Categoria::where('itil_category', true)->count();
        $categoriasActivas = Categoria::where('itil_category', true)->where('is_active', true)->count();

        $this->line("• Total categorías ITIL: {$totalCategorias}");
        $this->line("• Categorías activas: {$categoriasActivas}");

        $tipos = ['incidente', 'solicitud_servicio', 'cambio', 'problema'];
        foreach ($tipos as $tipo) {
            $count = Categoria::where('tipo_categoria', $tipo)->where('itil_category', true)->count();
            $emoji = match ($tipo) {
                'incidente' => '🔴',
                'solicitud_servicio' => '🔵',
                'cambio' => '🟡',
                'problema' => '🟢',
                default => '⚪'
            };
            $this->line("• {$emoji} " . ucwords(str_replace('_', ' ', $tipo)) . ": {$count}");
        }

        // Estadísticas de tickets
        $this->line('');
        $this->info('🎫 TICKETS CON CATEGORÍAS ITIL:');
        $this->line('────────────────────────────────');

        $ticketsConCategorias = Ticket::whereHas('categorias', function ($query) {
            $query->where('itil_category', true);
        })->count();

        $totalTickets = Ticket::count();
        $porcentaje = $totalTickets > 0 ? round(($ticketsConCategorias / $totalTickets) * 100, 1) : 0;

        $this->line("• Tickets categorizados: {$ticketsConCategorias} de {$totalTickets} ({$porcentaje}%)");

        foreach ($tipos as $tipo) {
            $count = Ticket::whereHas('categorias', function ($query) use ($tipo) {
                $query->where('tipo_categoria', $tipo);
            })->count();

            $emoji = match ($tipo) {
                'incidente' => '🔴',
                'solicitud_servicio' => '🔵',
                'cambio' => '🟡',
                'problema' => '🟢',
                default => '⚪'
            };

            $this->line("• {$emoji} " . ucwords(str_replace('_', ' ', $tipo)) . ": {$count} tickets");
        }

        // SLA y prioridades
        $this->line('');
        $this->info('⏱️ PRIORIDADES:');
        $this->line('───────────────');

        $prioridades = ['critica', 'alta', 'media', 'baja'];
        foreach ($prioridades as $prioridad) {
            $count = Categoria::where('itil_category', true)->where('prioridad_default', $prioridad)->count();
            $emoji = match ($prioridad) {
                'critica' => '🔴',
                'alta' => '🟠',
                'media' => '🟡',
                'baja' => '🟢',
                default => '⚪'
            };
            $this->line("• {$emoji} " . ucfirst($prioridad) . ": {$count} categorías");
        }

        // Funcionalidades disponibles
        $this->line('');
        $this->info('🔧 FUNCIONALIDADES INTEGRADAS:');
        $this->line('──────────────────────────────');
        $this->line('✅ Dashboard ITIL completo con métricas');
        $this->line('✅ Categorización automática de tickets');
        $this->line('✅ Prioridades basadas en categorías ITIL');
        $this->line('✅ Widgets y gráficos especializados');
        $this->line('✅ Exportación Excel/PDF con datos ITIL');
        $this->line('✅ Filtros avanzados por categorías');
        $this->line('✅ Sincronización automática de prioridades');
        $this->line('✅ SLA automático basado en categorías');
        $this->line('✅ Recursos Filament optimizados');

        $this->line('');
        $this->info('🎉 ¡Integración ITIL v4 completada exitosamente!');

        return Command::SUCCESS;
    }
}
