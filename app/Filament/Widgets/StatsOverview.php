<?php

namespace App\Filament\Widgets;

use App\Models\Categoria;
use App\Models\Dispositivo;
use App\Models\Rol;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    
    protected function getStats(): array
    {
        return [

            Stat::make('Total de tickets', Ticket::count()),
            Stat::make('Tickets Resueltos', Ticket::where('estado', Ticket::ESTADOS['Cerrado'])->count()),
                
            Stat::make('Total de dispositivos', Dispositivo::count())
                ->color('success')
                ->icon('heroicon-o-device-phone-mobile'),
        ];
    }
}
