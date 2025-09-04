<?php

namespace App\Livewire;

use App\Models\Ticket;
use App\Models\TicketSatisfaction;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class TicketSatisfactionSurvey extends Component
{
    public $ticket;
    public $rating = null;
    public $timeSatisfaction = null;
    public $comments = '';
    public $showSurvey = true;
    public $submitted = false;

    public function mount(Ticket $ticket)
    {
        $this->ticket = $ticket;

        // Verificar si ya existe una encuesta para este ticket y usuario
        $existingSurvey = TicketSatisfaction::where('ticket_id', $ticket->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existingSurvey) {
            $this->submitted = true;
            $this->showSurvey = false;
        }

        // Solo mostrar para tickets cerrados y al usuario creador
        if (
            !in_array($ticket->estado, [Ticket::ESTADOS['Cerrado'], Ticket::ESTADOS['Cancelado']]) ||
            $ticket->creado_por !== Auth::id()
        ) {
            $this->showSurvey = false;
        }

        // Si viene desde una notificación, hacer auto-scroll
        if (request()->get('survey') == $ticket->id) {
            $this->dispatch('scroll-to-survey', ticketId: $ticket->id);
        }
    }

    public function submitSurvey()
    {
        $this->validate([
            'rating' => 'required|integer|min:1|max:5',
            'timeSatisfaction' => 'required|in:muy_rapido,adecuado,regular,muy_lento',
            'comments' => 'nullable|string|max:500',
        ]);

        TicketSatisfaction::create([
            'ticket_id' => $this->ticket->id,
            'user_id' => Auth::id(),
            'rating' => $this->rating,
            'time_satisfaction' => $this->timeSatisfaction,
            'comments' => $this->comments,
            'submitted_at' => now(),
        ]);

        $this->submitted = true;
        $this->showSurvey = false;

        // Emitir evento para actualizar la lista de encuestas pendientes
        $this->dispatch('survey-submitted');

        // Mostrar notificación de éxito
        session()->flash('message', '¡Gracias por su feedback! Su encuesta ha sido registrada exitosamente.');
    }

    public function closeSurvey()
    {
        $this->showSurvey = false;

        // Emitir evento para actualizar la lista de encuestas pendientes
        $this->dispatch('survey-closed');
    }

    public function render()
    {
        return view('livewire.ticket-satisfaction-survey');
    }
}
