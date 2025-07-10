<?php

namespace App\Livewire;

use App\Models\Area;
use App\Models\Ticket;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class TicketCreate extends Component
{
    public $titulo = '';
    public $descripcion = '';
    public $prioridad = 'Media';
    public $area_id = '';
    public $showModal = false;

    protected $rules = [
        'titulo' => 'required|string|max:255',
        'descripcion' => 'required|string|max:1000',
        'prioridad' => 'required|in:Baja,Media,Alta,Crítica',
        'area_id' => 'required|exists:areas,id',
    ];

    protected $messages = [
        'titulo.required' => 'El título es obligatorio.',
        'titulo.max' => 'El título no puede tener más de 255 caracteres.',
        'descripcion.required' => 'La descripción es obligatoria.',
        'descripcion.max' => 'La descripción no puede tener más de 1000 caracteres.',
        'prioridad.required' => 'La prioridad es obligatoria.',
        'prioridad.in' => 'La prioridad debe ser: Baja, Media, Alta o Crítica.',
        'area_id.required' => 'El área es obligatoria.',
        'area_id.exists' => 'El área seleccionada no existe.',
    ];

    public function openModal()
    {
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->titulo = '';
        $this->descripcion = '';
        $this->prioridad = 'Media';
        $this->area_id = '';
        $this->resetValidation();
    }

    public function save()
    {
        $this->validate();

        if (!Auth::check()) {
            session()->flash('error', 'Debes iniciar sesión para crear un ticket.');
            return;
        }

        try {
            Ticket::create([
                'titulo' => $this->titulo,
                'descripcion' => $this->descripcion,
                'prioridad' => $this->prioridad,
                'area_id' => $this->area_id,
                'creado_por' => Auth::id(),
                'estado' => 'Abierto',
            ]);

            session()->flash('success', 'Ticket creado exitosamente.');
            $this->closeModal();
            $this->dispatch('ticket-created');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al crear el ticket. Inténtalo de nuevo.');
        }
    }

    public function render()
    {
        return view('livewire.ticket-create', [
            'areas' => Area::all()
        ]);
    }
}
