<?php

namespace App\Filament\Widgets;

use App\Models\Ticket;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

use Filament\Widgets\ChartWidget;

class TicketsOverviewChart extends ChartWidget
{
    protected static ?string $heading = 'Tickets Overview';

    public ?string $filter  = 'week';
    protected function getFilters(): ?array
    {
        return [
            'week' => 'Hace 7 días', //last week
            'month' => 'Hace 1 mes', //last month
            'year' => 'Este año',   //this year
        ];
    }

    protected function getData(): array
    {
        $start = null;
        $end = null;
        $perData = null;

        switch ($this->filter) {
            case 'week':
                $start = now()->startOfWeek();
                $end = now()->endOfWeek();
                $perData ='perDay';
                break;
            case 'month':
                $start = now()->subDays(30);
                $end = now();
                $perData = 'perDay';
                break;
            case 'year':
                $start = now()->startOfYear();
                $end = now()->endOfYear();
                $perData ='perMonth';
                break;
            default:
                $start = now()->startOfWeek();
                $end = now()->endOfWeek();
                $perData ='perDay';
                break;
        }
        $data = Trend::model(Ticket::class)
            ->between(
                start: $start,
                end: $end,
            )
            ->$perData()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Tickets',
                    'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn(TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
