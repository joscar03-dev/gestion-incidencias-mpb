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
    public $newComment = '';

    protected $listeners = ['ticket-updated' => 'loadTicket', 'comment-added' => 'loadTicket'];

    protected $rules = [
        'newComment' => 'required|min:3|max:1000',
    ];

    protected $messages = [
        'newComment.required' => 'El comentario es obligatorio.',
        'newComment.min' => 'El comentario debe tener al menos 3 caracteres.',
        'newComment.max' => 'El comentario no puede tener más de 1000 caracteres.',
    ];

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
                ->with(['area', 'comments.author'])
                ->first();
        }
    }

    public function refreshComments()
    {
        // Método específico para actualizar comentarios sin cambiar el scroll
        $this->loadTicket();
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
            ->with(['area', 'creadoPor', 'asignadoA', 'comments.author'])
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

    public function addComment()
    {
        $this->validate();

        if (!$this->ticket) {
            session()->flash('error', 'No se puede comentar en este ticket.');
            return;
        }

        try {
            // Crear el comentario usando el paquete Kirschbaum
            $this->ticket->comments()->create([
                'author_type' => 'App\\Models\\User',
                'author_id' => Auth::id(),
                'body' => $this->newComment,
            ]);

            // Limpiar el campo de comentario
            $this->newComment = '';

            // Recargar el ticket para mostrar el nuevo comentario
            $this->loadTicket();

            // Emitir evento para notificar que se agregó un comentario
            $this->dispatch('comment-added');

            session()->flash('success', 'Comentario agregado exitosamente.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al agregar el comentario. Inténtalo de nuevo.');
        }
    }

    public function render()
    {
        return view('livewire.ticket-detail');
    }
}
