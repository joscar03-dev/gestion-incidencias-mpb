<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;

class PendingSurveys extends Component
{
    public $ticketsPendingSurvey = [];

    protected $listeners = [
        'survey-submitted' => 'refreshSurveys',
        'survey-closed' => 'refreshSurveys',
        'refresh-surveys' => 'refreshSurveys',
    ];

    public function mount()
    {
        $this->loadPendingSurveys();
    }

    public function loadPendingSurveys()
    {
        $this->ticketsPendingSurvey = Auth::user()->tickets()
            ->whereIn('estado', ['Cerrado', 'Cancelado'])
            ->whereDoesntHave('satisfaction', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->orderBy('fecha_cierre', 'desc')
            ->take(3)
            ->get();
    }

    public function refreshSurveys()
    {
        $this->loadPendingSurveys();
    }

    public function render()
    {
        return view('livewire.pending-surveys');
    }
}
