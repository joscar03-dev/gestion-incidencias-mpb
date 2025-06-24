<?php

namespace App\Filament\Widgets;

use Illuminate\Contracts\Support\Htmlable;
use App\Filament\CustomWidgets\MetricWidget;

class MetricWidgetFromCommand extends MetricWidget
{
    protected string | Htmlable $label = "Ejemplo de Widget de Métrica";

    public function getValue()
    {
        return "123";
    }

}
