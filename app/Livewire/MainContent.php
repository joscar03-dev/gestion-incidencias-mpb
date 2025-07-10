<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class MainContent extends Component
{
    protected $listeners = ['user-authenticated' => 'refreshContent'];

    public function refreshContent()
    {
        // Simplemente refrescar el componente
        $this->render();
    }

    public function render()
    {
        return view('livewire.main-content');
    }
}
