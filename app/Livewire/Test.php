<?php

namespace App\Livewire;

use Livewire\Component;

class Test extends Component
{
    public $count = 0;
    public $message = 'Component loaded';

    public function increment()
    {
        $this->count++;
        $this->message = 'Button clicked! Count: ' . $this->count;
    }

    public function mount()
    {
        $this->message = 'Component mounted successfully';
    }

    public function render()
    {
        return view('livewire.test');
    }
}
