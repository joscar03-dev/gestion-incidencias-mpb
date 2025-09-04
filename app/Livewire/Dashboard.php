<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class Dashboard extends Component
{
    public $currentView = 'tickets'; // 'tickets', 'create', 'home'

    public function mount()
    {
        // Verificar si hay un parámetro view en la URL
        $view = request()->query('view');
        if ($view && in_array($view, ['tickets', 'create', 'home', 'devices'])) {
            $this->currentView = $view;
        }
    }

    protected $listeners = [
        'changeView' => 'setView',
        'ticket-created' => 'onTicketCreated',
        'create-satisfaction-survey' => 'createSatisfactionSurvey'
    ];

    public function setView($view)
    {
        $this->currentView = $view;
    }

    public function showTickets()
    {
        $this->currentView = 'tickets';
    }

    public function showCreateTicket()
    {
        $this->currentView = 'create';
    }

    public function showHome()
    {
        $this->currentView = 'home';
    }

    public function showDevices()
    {
        $this->currentView = 'devices';
    }

    public function createSatisfactionSurvey($surveyData)
    {
        try {
            // Validar que el usuario esté autenticado
            if (!Auth::check()) {
                return;
            }

            // Validar que el ticket existe y pertenece al usuario
            $ticket = \App\Models\Ticket::find($surveyData['ticket_id']);
            if (!$ticket || $ticket->creado_por !== Auth::id()) {
                return;
            }

            // Verificar que no existe ya una encuesta para este ticket y usuario
            $existingSurvey = \App\Models\TicketSatisfaction::where('ticket_id', $ticket->id)
                ->where('user_id', Auth::id())
                ->first();

            if ($existingSurvey) {
                return; // Ya existe una encuesta
            }

            // Crear la encuesta de satisfacción
            \App\Models\TicketSatisfaction::create([
                'ticket_id' => $ticket->id,
                'user_id' => Auth::id(),
                'rating' => $surveyData['rating'],
                'time_satisfaction' => $surveyData['time_satisfaction'] ?? null,
                'comments' => $surveyData['comments'] ?? null,
            ]);

            // Mostrar notificación de éxito
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => '¡Gracias por su feedback! Su encuesta ha sido registrada exitosamente.'
            ]);
        } catch (\Exception $e) {
            Log::error('Error creando encuesta de satisfacción: ' . $e->getMessage());

            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error al procesar la encuesta. Por favor, inténtelo de nuevo.'
            ]);
        }
    }

    public function onTicketCreated()
    {
        // Cambiar automáticamente a la vista de tickets cuando se crea uno
        $this->currentView = 'tickets';

        // Mostrar notificación de éxito
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => '¡Ticket creado exitosamente! Se ha cambiado automáticamente a la lista de tickets.'
        ]);
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}
