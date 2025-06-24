<?php

namespace App\Filament\Resources\TicketResource\Widgets;

use Illuminate\Contracts\Support\Htmlable;
use App\Filament\CustomWidgets\MetricWidget;
use App\Models\Ticket;

class MetricWidgetSample extends MetricWidget
{
    protected string | Htmlable $label = "Tickets Overview";

    public function getValue()
    {
        return match ($this->filter) {
            'week' => Ticket::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'month' => Ticket::whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count(),
            'year' => Ticket::whereBetween('created_at', [now()->startOfYear(), now()->endOfYear()])->count(),
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
