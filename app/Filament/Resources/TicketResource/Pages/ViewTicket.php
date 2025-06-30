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
    
}
