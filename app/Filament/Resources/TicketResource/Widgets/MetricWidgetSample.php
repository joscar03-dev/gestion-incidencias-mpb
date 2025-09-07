<?php

namespace App\Filament\Resources\TicketResource\Widgets;

use Illuminate\Contracts\Support\Htmlable;
use App\Filament\CustomWidgets\MetricWidget;
use App\Models\Ticket;

class MetricWidgetSample extends MetricWidget
{
    protected string | Htmlable $label;

    public function __construct()
    {
        // Label dinámico según el rol
        $this->label = auth()->user()->hasRole('Técnico')
            ? "Mis Tickets"
            : "Tickets Overview";
    }

    public function getValue()
    {
        // Base query - filtrar según el rol del usuario
        $query = auth()->user()->hasRole('Técnico')
            ? Ticket::where('asignado_a', auth()->id()) // Solo tickets asignados al técnico
            : Ticket::query(); // Todos los tickets para otros roles

        return match ($this->filter) {
            'week' => $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'month' => $query->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count(),
            'year' => $query->whereBetween('created_at', [now()->startOfYear(), now()->endOfYear()])->count(),
        };
    }

    public ?string $filter = 'week';
    protected function getFilters(): ?array
    {
        return [
            'week' => 'Esta semana',
            'month' => 'Este mes',
            'year' => 'Este año',
        ];
    }
}
