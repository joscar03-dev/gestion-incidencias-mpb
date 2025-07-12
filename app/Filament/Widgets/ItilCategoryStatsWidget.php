<?php

namespace App\Filament\Widgets;

use App\Models\Categoria;
use App\Models\Ticket;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ItilCategoryStatsWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected function getStats(): array
    {
        // Obtener estadísticas de categorías ITIL
        $categoriasItil = Categoria::where('itil_category', true)->count();
        $categoriasActivas = Categoria::where('itil_category', true)->where('is_active', true)->count();

        // Tickets con categorías ITIL
        $ticketsConCategorias = Ticket::whereHas('categorias', function($query) {
            $query->where('itil_category', true);
        })->count();

        // Distribución por tipo de categoría
        $incidentes = Ticket::whereHas('categorias', function($query) {
            $query->where('tipo_categoria', 'incidente');
        })->count();

        $solicitudes = Ticket::whereHas('categorias', function($query) {
            $query->where('tipo_categoria', 'solicitud_servicio');
        })->count();

        $cambios = Ticket::whereHas('categorias', function($query) {
            $query->where('tipo_categoria', 'cambio');
        })->count();

        $problemas = Ticket::whereHas('categorias', function($query) {
            $query->where('tipo_categoria', 'problema');
        })->count();

        return [
            Stat::make('Categorías ITIL', $categoriasItil)
                ->description("$categoriasActivas activas")
                ->descriptionIcon('heroicon-m-tag')
                ->color('info')
                ->chart([7, 12, 8, 15, 20, 18, 24]),

            Stat::make('Tickets Categorizados', $ticketsConCategorias)
                ->description('Con categorías ITIL')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success')
                ->chart([5, 8, 12, 15, 18, 22, $ticketsConCategorias]),

            Stat::make('🔴 Incidentes', $incidentes)
                ->description('Categorías de incidentes')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger')
                ->chart([3, 5, 8, 12, 15, 18, $incidentes]),

            Stat::make('🔵 Solicitudes', $solicitudes)
                ->description('Solicitudes de servicio')
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->color('info')
                ->chart([2, 4, 6, 9, 12, 15, $solicitudes]),

            Stat::make('🟡 Cambios', $cambios)
                ->description('Gestión de cambios')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color('warning')
                ->chart([1, 2, 3, 5, 8, 10, $cambios]),

            Stat::make('🟢 Problemas', $problemas)
                ->description('Gestión de problemas')
                ->descriptionIcon('heroicon-m-bug-ant')
                ->color('success')
                ->chart([0, 1, 2, 3, 5, 7, $problemas]),
        ];
    }
}
