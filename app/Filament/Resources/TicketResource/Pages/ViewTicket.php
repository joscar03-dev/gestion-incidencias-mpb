<?php

namespace App\Filament\Resources\TicketResource\Pages;

use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Infolist;
use App\Filament\Resources\TicketResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Database\Eloquent\Model;
use Kirschbaum\Commentions\Comment;
use Kirschbaum\Commentions\Filament\Actions\CommentsAction;
use Kirschbaum\Commentions\Filament\Infolists\Components\CommentsEntry;

class ViewTicket extends ViewRecord
{
    protected static string $resource = TicketResource::class;
    protected static ?string $title = 'Detalles del Ticket';

    protected function getHeaderActions(): array
    {
        return [
            CommentsAction::make()
                ->label('Comentarios')
                ->icon('heroicon-o-chat-bubble-left-right')
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('📋 Información del Ticket')
                    ->schema([
                        Split::make([
                            Grid::make([
                                'sm' => 1,
                                'md' => 2,
                                'lg' => 3,
                            ])
                                ->schema([
                                    TextEntry::make('titulo')
                                        ->label('Título')
                                        ->icon('heroicon-o-document-text')
                                        ->columnSpan([
                                            'sm' => 1,
                                            'md' => 2,
                                            'lg' => 3,
                                        ]),
                                    TextEntry::make('descripcion')
                                        ->label('Descripción')
                                        ->icon('heroicon-o-chat-bubble-left-right')
                                        ->columnSpan([
                                            'sm' => 1,
                                            'md' => 2,
                                            'lg' => 3,
                                        ]),
                                    TextEntry::make('estado')
                                        ->label('Estado')
                                        ->badge()
                                        ->icon('heroicon-o-signal')
                                        ->color(fn (string $state): string => match ($state) {
                                            'Abierto' => 'danger',
                                            'En Progreso' => 'warning',
                                            'Escalado' => 'danger',
                                            'Cerrado' => 'success',
                                            'Archivado' => 'secondary',
                                            default => 'secondary',
                                        }),
                                    TextEntry::make('prioridad')
                                        ->label('Prioridad')
                                        ->badge()
                                        ->icon('heroicon-o-flag')
                                        ->color(fn (string $state): string => match ($state) {
                                            'Critica' => 'danger',
                                            'Alta' => 'warning',
                                            'Media' => 'success',
                                            'Baja' => 'secondary',
                                            default => 'secondary',
                                        }),
                                    TextEntry::make('tipo')
                                        ->label('Tipo')
                                        ->badge()
                                        ->icon('heroicon-o-tag'),
                                    TextEntry::make('categorias.nombre')
                                        ->label('Categorías ITIL')
                                        ->badge()
                                        ->separator(', ')
                                        ->icon('heroicon-o-squares-2x2'),
                                    TextEntry::make('creadoPor.name')
                                        ->label('Creado por')
                                        ->icon('heroicon-o-user'),
                                    TextEntry::make('asignadoA.name')
                                        ->label('Asignado a')
                                        ->icon('heroicon-o-user-circle'),
                                    TextEntry::make('created_at')
                                        ->label('Fecha de creación')
                                        ->dateTime('d/m/Y H:i')
                                        ->icon('heroicon-o-calendar'),
                                    TextEntry::make('area.nombre')
                                        ->label('Área')
                                        ->icon('heroicon-o-building-office'),
                                ])
                        ])
                    ])
                    ->collapsible(),

                Section::make('💻 Información del Dispositivo')
                    ->schema([
                        Grid::make([
                            'sm' => 1,
                            'md' => 2,
                            'lg' => 3,
                        ])
                            ->schema([
                                TextEntry::make('dispositivo.nombre')
                                    ->label('Dispositivo')
                                    ->icon('heroicon-o-computer-desktop'),
                                TextEntry::make('dispositivo.categoria_dispositivo.nombre')
                                    ->label('Categoría')
                                    ->badge()
                                    ->color('info')
                                    ->icon('heroicon-o-folder'),
                                TextEntry::make('dispositivo.numero_serie')
                                    ->label('N° de Serie')
                                    ->copyable()
                                    ->copyMessage('Número de serie copiado')
                                    ->icon('heroicon-o-identification'),
                                TextEntry::make('dispositivo.estado')
                                    ->label('Estado')
                                    ->badge()
                                    ->icon('heroicon-o-signal')
                                    ->color(fn (string $state): string => match ($state) {
                                        'Disponible' => 'success',
                                        'Asignado' => 'info',
                                        'Reparación' => 'warning',
                                        'Fuera de Servicio' => 'danger',
                                        'Dañado' => 'danger',
                                        'Retirado' => 'secondary',
                                        default => 'secondary',
                                    }),
                                TextEntry::make('dispositivo.marca')
                                    ->label('Marca')
                                    ->icon('heroicon-o-tag'),
                                TextEntry::make('dispositivo.modelo')
                                    ->label('Modelo')
                                    ->icon('heroicon-o-cube'),
                                TextEntry::make('dispositivo.area.nombre')
                                    ->label('Ubicación')
                                    ->icon('heroicon-o-map-pin'),
                                TextEntry::make('dispositivo.observaciones')
                                    ->label('Observaciones')
                                    ->columnSpan([
                                        'sm' => 1,
                                        'md' => 2,
                                        'lg' => 3,
                                    ])
                                    ->icon('heroicon-o-document-text'),
                            ])
                    ])
                    ->visible(fn ($record) => $record->dispositivo_id !== null)
                    ->collapsible(),

                Section::make('💬 Comentarios')
                    ->schema([
                        CommentsEntry::make('commentions')
                            ->label('')
                    ])
                    ->collapsible(),
            ]);
    }
    
}
