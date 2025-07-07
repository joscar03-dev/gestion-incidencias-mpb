<?php

namespace App\Filament\User\Resources\TicketResource\Widgets;

use App\Filament\CustomWidgets\MetricsOverviewWidget;
use App\Filament\Resources\TicketResource\Widgets\MetricWidgetSample;

class MetricsOverviewSample extends MetricsOverviewWidget
{
    protected function getMetrics(): array
    {
        return [
            MetricWidgetSample::class,
        ];
    }


}
