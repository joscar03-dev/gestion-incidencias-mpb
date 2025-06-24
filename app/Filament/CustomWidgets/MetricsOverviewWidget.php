<?php

namespace App\Filament\CustomWidgets;

use Filament\Widgets\Concerns\CanPoll;
use Filament\Widgets\Widget;

class MetricsOverviewWidget extends Widget
{
    use CanPoll;

    protected static string $view = 'filament.custom-widgets.metrics-overview-widget';

    /**
     * @var array<MetricWidget> | null
     */
    protected ?array $cachedMetrics = null;

    protected int | string | array $columnSpan = 'full';

    protected ?string $heading = null;

    protected ?string $description = null;

    protected function getColumns(): int
    {
        $count = count($this->getCachedMetrics());

        if ($count < 3) {
            return 3;
        }

        if (($count % 3) !== 1) {
            return 3;
        }

        return 4;
    }

    /**
     * @return array<MetricWidget>
     */
    protected function getCachedMetrics(): array
    {
        return $this->cachedMetrics ??= $this->getMetrics();
    }

    protected function getDescription(): ?string
    {
        return $this->description;
    }

    protected function getHeading(): ?string
    {
        return $this->heading;
    }

    /**
     * @return array<MetricWidget>
     */
    protected function getMetrics(): array
    {
        return [];
    }
}
