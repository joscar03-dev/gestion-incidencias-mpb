<?php

namespace App\Livewire;

use App\Models\Ticket;
use App\Exports\TicketsExport;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class TicketList extends Component
{
    use WithPagination;

    public $search = '';
    public $status = '';
    public $priority = '';
    public $showModal = false;
    public $selectedTicketId = null;

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
        $this->selectedTicketId = $ticketId;
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedTicketId = null;
    }

    public function refreshList()
    {
        $this->resetPage();
    }

    public function exportExcel()
    {
        $tickets = $this->getFilteredTickets();

        $fileName = 'tickets_' . now()->format('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(new TicketsExport($tickets), $fileName);
    }

    public function exportPdf()
    {
        $tickets = $this->getFilteredTickets();

        $filters = [
            'search' => $this->search,
            'status' => $this->status,
            'priority' => $this->priority,
        ];

        $pdf = Pdf::loadView('exports.tickets-pdf', compact('tickets', 'filters'));

        $fileName = 'tickets_' . now()->format('Y-m-d_H-i-s') . '.pdf';

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $fileName);
    }

    private function getFilteredTickets()
    {
        $user = Auth::user();

        return Ticket::where('creado_por', $user->id)
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
            ->with(['area', 'creadoPor', 'asignadoA'])
            ->orderBy('created_at', 'desc')
            ->get();
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
            ->paginate(15);

        return view('livewire.ticket-list', [
            'tickets' => $tickets
        ]);
    }
}
