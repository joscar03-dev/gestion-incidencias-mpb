<?php

namespace App\Livewire;

use App\Models\Ticket;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class TicketList extends Component
{
    use WithPagination;

    public $search = '';
    public $status = '';
    public $priority = '';
    public $showModal = false;
    public $selectedTicket = null;

    protected $listeners = ['ticket-created' => 'refreshList', 'ticket-updated' => 'refreshList'];

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
        'priority' => ['except' => '']
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function updatingPriority()
    {
        $this->resetPage();
    }

    public function viewTicket($ticketId)
    {
        $this->selectedTicket = Ticket::find($ticketId);
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedTicket = null;
    }

    public function refreshList()
    {
        $this->resetPage();
    }

    public function render()
    {
        $user = Auth::user();
        if (!$user) {
            return view('livewire.ticket-list', ['tickets' => collect()]);
        }

        $tickets = Ticket::where('creado_por', $user->id)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('titulo', 'like', '%' . $this->search . '%')
                      ->orWhere('descripcion', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->status, function ($query) {
                $query->where('estado', $this->status);
            })
            ->when($this->priority, function ($query) {
                $query->where('prioridad', $this->priority);
            })
            ->with(['area'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.ticket-list', [
            'tickets' => $tickets
        ]);
    }
}
