<?php

namespace App\Filament\Resources\TicketResource\Pages;

use App\Filament\Resources\TicketResource;
use App\Filament\Resources\TicketResource\Widgets\MetricsOverviewSample;
use App\Filament\Resources\TicketResource\Widgets\TicketsTiempoResolucionChart;
use App\Exports\TicketsExport;
use App\Models\Ticket;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Database\Eloquent\Builder;

class ListTickets extends ListRecords
{
    protected static string $resource = TicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),

            // Acción para exportar con filtros avanzados
            Actions\Action::make('export_all')
                ->label('Exportar Todo a Excel')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->form([
                    // Filtros de fecha
                    DatePicker::make('created_from')
                        ->label('Creado desde'),
                    DatePicker::make('created_until')
                        ->label('Creado hasta'),
                    DatePicker::make('updated_from')
                        ->label('Actualizado desde'),
                    DatePicker::make('updated_until')
                        ->label('Actualizado hasta'),
                    DatePicker::make('closed_from')
                        ->label('Cerrado desde'),
                    DatePicker::make('closed_until')
                        ->label('Cerrado hasta'),

                    // Filtros de usuarios
                    Select::make('creado_por')
                        ->label('Creado por')
                        ->multiple()
                        ->searchable()
                        ->options(function () {
                            return \App\Models\User::pluck('name', 'id');
                        }),

                    Select::make('asignado_a')
                        ->label('Asignado a')
                        ->multiple()
                        ->searchable()
                        ->options(function () {
                            return \App\Models\User::pluck('name', 'id');
                        }),

                    // Filtros de categorización
                    Select::make('estado')
                        ->label('Estado')
                        ->multiple()
                        ->options(Ticket::ESTADOS),

                    Select::make('prioridad')
                        ->label('Prioridad')
                        ->multiple()
                        ->options(Ticket::PRIORIDAD),

                    Select::make('tipo')
                        ->label('Tipo de Ticket')
                        ->multiple()
                        ->options(Ticket::TIPOS),

                    Select::make('area_id')
                        ->label('Área')
                        ->multiple()
                        ->searchable()
                        ->options(function () {
                            return \App\Models\Area::pluck('nombre', 'id');
                        }),

                    Select::make('categorias')
                        ->label('Categorías ITIL')
                        ->multiple()
                        ->searchable()
                        ->options(function () {
                            return \App\Models\Categoria::where('itil_category', true)
                                ->where('is_active', true)
                                ->pluck('nombre', 'id');
                        }),

                    Select::make('dispositivo_id')
                        ->label('Dispositivo')
                        ->multiple()
                        ->searchable()
                        ->options(function () {
                            return \App\Models\Dispositivo::pluck('nombre', 'id');
                        }),

                    Select::make('resolucion')
                        ->label('Tipo de Resolución')
                        ->multiple()
                        ->options([
                            'Resuelto' => 'Resuelto',
                            'Escalado' => 'Escalado',
                            'Reasignado' => 'Reasignado',
                            'Cerrado sin resolver' => 'Cerrado sin resolver',
                            'Duplicado' => 'Duplicado',
                            'No procede' => 'No procede',
                        ]),

                    // Filtros especiales
                    Toggle::make('escalado')
                        ->label('Solo Escalados'),

                    Toggle::make('sla_vencido')
                        ->label('Solo SLA Vencidos'),

                    Toggle::make('has_files')
                        ->label('Solo con Archivos Adjuntos'),
                ])
                ->action(function (array $data) {
                    // Construir query con filtros
                    $query = Ticket::query();

                    // Aplicar filtros de fecha
                    if ($data['created_from']) {
                        $query->whereDate('created_at', '>=', $data['created_from']);
                    }
                    if ($data['created_until']) {
                        $query->whereDate('created_at', '<=', $data['created_until']);
                    }
                    if ($data['updated_from']) {
                        $query->whereDate('updated_at', '>=', $data['updated_from']);
                    }
                    if ($data['updated_until']) {
                        $query->whereDate('updated_at', '<=', $data['updated_until']);
                    }
                    if ($data['closed_from']) {
                        $query->whereDate('fecha_cierre', '>=', $data['closed_from']);
                    }
                    if ($data['closed_until']) {
                        $query->whereDate('fecha_cierre', '<=', $data['closed_until']);
                    }

                    // Aplicar filtros de usuarios
                    if ($data['creado_por']) {
                        $query->whereIn('creado_por', $data['creado_por']);
                    }
                    if ($data['asignado_a']) {
                        $query->whereIn('asignado_a', $data['asignado_a']);
                    }

                    // Aplicar filtros de categorización
                    if ($data['estado']) {
                        $query->whereIn('estado', $data['estado']);
                    }
                    if ($data['prioridad']) {
                        $query->whereIn('prioridad', $data['prioridad']);
                    }
                    if ($data['tipo']) {
                        $query->whereIn('tipo', $data['tipo']);
                    }
                    if ($data['area_id']) {
                        $query->whereIn('area_id', $data['area_id']);
                    }
                    if ($data['dispositivo_id']) {
                        $query->whereIn('dispositivo_id', $data['dispositivo_id']);
                    }
                    if ($data['resolucion']) {
                        $query->whereIn('resolucion', $data['resolucion']);
                    }

                    // Aplicar filtros de categorías ITIL
                    if ($data['categorias']) {
                        $query->whereHas('categorias', function ($q) use ($data) {
                            $q->whereIn('categoria_id', $data['categorias']);
                        });
                    }

                    // Aplicar filtros especiales
                    if ($data['escalado']) {
                        $query->where('escalado', true);
                    }
                    if ($data['sla_vencido']) {
                        $query->where('sla_vencido', true);
                    }
                    if ($data['has_files']) {
                        $query->whereHas('archivos');
                    }

                    // Obtener tickets con relaciones
                    $tickets = $query->with(['area', 'creadoPor', 'asignadoA', 'categorias', 'dispositivo'])->get();

                    return Excel::download(
                        new TicketsExport($tickets),
                        'tickets_filtrados_' . now()->format('Y-m-d_H-i-s') . '.xlsx'
                    );
                }),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // StatsOverview::class, //agregar widgets de encabezado
            MetricsOverviewSample::class,
            // TicketsTiempoResolucionChart::class,
        ];
    }
}
