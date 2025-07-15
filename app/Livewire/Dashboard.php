<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

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
        'ticket-created' => 'onTicketCreated'
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
