<?php

namespace App\Filament\Widgets;

use App\Models\Categoria;
use App\Models\Rol;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    // protected static ?int $sort = 1;
    protected function getStats(): array
    {
        return [

            Stat::make('Total de tickets', Ticket::count()),
            Stat::make('Total de categorias', Categoria::where('is_active', true)->count()),
            Stat::make('Total de moderadores', User::role('Moderador')->count()),
        ];
    }
}
