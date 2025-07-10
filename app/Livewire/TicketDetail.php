<?php

namespace App\Livewire;

use App\Models\Ticket;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class TicketDetail extends Component
{
    public $ticket;
    public $ticketId;

    public function mount($ticketId = null)
    {
        $this->ticketId = $ticketId;
        $this->loadTicket();
    }

    public function loadTicket()
    {
        if ($this->ticketId) {
            $this->ticket = Ticket::where('id', $this->ticketId)
                ->where('creado_por', Auth::id())
                ->with(['area'])
                ->first();
        }
    }

    public function cancelTicket()
    {
        if (!$this->ticket) {
            return;
        }

        if ($this->ticket->estado === 'Cerrado' || $this->ticket->estado === 'Cancelado') {
            session()->flash('error', 'Este ticket ya no puede ser cancelado.');
            return;
        }

        try {
            $this->ticket->update([
                'estado' => 'Cancelado',
                'fecha_resolucion' => now(),
                'comentarios_resolucion' => 'Ticket cancelado por el usuario.'
            ]);

            session()->flash('success', 'Ticket cancelado exitosamente.');
            $this->loadTicket(); // Recargar para mostrar el nuevo estado
            $this->dispatch('ticket-updated');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al cancelar el ticket. Inténtalo de nuevo.');
        }
    }

    public function render()
    {
        return view('livewire.ticket-detail');
    }
}
