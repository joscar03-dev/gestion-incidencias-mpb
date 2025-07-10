<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Navigation extends Component
{
    protected $listeners = ['user-authenticated' => 'refreshNavigation'];

    public function refreshNavigation()
    {
        // Simplemente refrescar el componente
        $this->render();
    }

    public function render()
    {
        return view('livewire.navigation');
    }
}
