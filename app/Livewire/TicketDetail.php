<?php

namespace App\Livewire;

use App\Models\Ticket;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class TicketDetail extends Component
{
    public $ticket;
    public $ticketId;

    protected $listeners = ['ticket-updated' => 'loadTicket'];

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

    public function exportTicketPdf()
    {
        if (!$this->ticket) {
            session()->flash('error', 'No se puede exportar el ticket.');
            return;
        }

        // Cargar el ticket con todas sus relaciones
        $ticket = Ticket::where('id', $this->ticket->id)
            ->where('creado_por', Auth::id())
            ->with(['area', 'creadoPor', 'asignadoA'])
            ->first();

        if (!$ticket) {
            session()->flash('error', 'Ticket no encontrado.');
            return;
        }

        $pdf = Pdf::loadView('exports.ticket-detail-pdf', compact('ticket'));

        $fileName = 'ticket_' . $ticket->id . '_' . now()->format('Y-m-d_H-i-s') . '.pdf';

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $fileName);
    }

    public function render()
    {
        return view('livewire.ticket-detail');
    }
}
