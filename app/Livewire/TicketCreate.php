<?php

namespace App\Livewire;

use App\Models\Area;
use App\Models\Ticket;
use App\Models\User;
use App\Models\CategoriaDispositivo;
use App\Models\SolicitudDispositivo;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TicketCreate extends Component
{
    use WithFileUploads;

    public $titulo = '';
    public $descripcion = '';
    public $prioridad = 'Media';
    public $tipo = ''; // Agregar campo tipo
    public $categoria_dispositivo_id = ''; // Para requerimientos
    public $area_id = '';
    public $showModal = false;
    public $archivos = [];
    public $isDashboard = false; // Para saber si está en el dashboard o en modal

    protected $listeners = ['preseleccionar-tipo' => 'preseleccionarTipo'];

    protected $rules = [
        'titulo' => 'required|string|max:255',
        'descripcion' => 'required|string|max:1000',
        'prioridad' => 'required|in:Baja,Media,Alta,Critica',
        'tipo' => 'required|in:Incidencia,Problema,Requerimiento,Cambio',
        'categoria_dispositivo_id' => 'nullable|required_if:tipo,Requerimiento|exists:categoria_dispositivos,id',
        'area_id' => 'required|exists:areas,id',
        'archivos.*' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,gif,pdf,doc,docx,txt,zip,rar,xls,xlsx,ppt,pptx',
    ];

    protected $messages = [
        'titulo.required' => 'El título es obligatorio.',
        'titulo.max' => 'El título no puede tener más de 255 caracteres.',
        'descripcion.required' => 'La descripción es obligatoria.',
        'descripcion.max' => 'La descripción no puede tener más de 1000 caracteres.',
        'prioridad.required' => 'La prioridad es obligatoria.',
        'prioridad.in' => 'La prioridad debe ser: Baja, Media, Alta o Critica.',
        'tipo.required' => 'El tipo de ticket es obligatorio.',
        'tipo.in' => 'El tipo debe ser: Incidencia, Problema, Requerimiento o Cambio.',
        'categoria_dispositivo_id.required_if' => 'La categoría de dispositivo es obligatoria para requerimientos.',
        'categoria_dispositivo_id.exists' => 'La categoría de dispositivo seleccionada no existe.',
        'area_id.required' => 'El área es obligatoria.',
        'area_id.exists' => 'El área seleccionada no existe.',
        'archivos.*.max' => 'Cada archivo no puede ser mayor a 10MB.',
        'archivos.*.mimes' => 'Solo se permiten archivos: jpg, jpeg, png, gif, pdf, doc, docx, txt, zip, rar, xls, xlsx, ppt, pptx.',
    ];

    public function mount($isDashboard = false)
    {
        $this->isDashboard = $isDashboard;

        // Si es el dashboard, pre-asignar el área del usuario
        if ($this->isDashboard && Auth::check() && Auth::user()->area_id) {
            $this->area_id = Auth::user()->area_id;
        }
    }

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
        $this->tipo = '';
        $this->categoria_dispositivo_id = '';
        $this->archivos = [];

        // Mantener el área pre-asignada si es dashboard
        if ($this->isDashboard && Auth::check() && Auth::user()->area_id) {
            $this->area_id = Auth::user()->area_id;
        } else {
            $this->area_id = '';
        }

        $this->resetValidation();
    }

    public function removeFile($index)
    {
        unset($this->archivos[$index]);
        $this->archivos = array_values($this->archivos);
    }

    private function asignarTecnicoDisponible()
    {
        // Estados que consideramos como "no disponibles" (usando las constantes del modelo)
        $estadosNoDisponibles = [
            Ticket::ESTADOS['Cerrado'],
            Ticket::ESTADOS['Cancelado'],
            Ticket::ESTADOS['Archivado']
        ];

        // Buscar usuarios con rol 'Tecnico' (debe coincidir con el rol exacto)
        $tecnicos = User::role(['Tecnico'])->get();

        if ($tecnicos->isEmpty()) {
            return null;
        }

        // Filtrar técnicos disponibles (<5 tickets activos)
        $disponibles = $tecnicos->filter(function ($u) use ($estadosNoDisponibles) {
            return $u->ticketsAsignados()
                ->whereNotIn('estado', $estadosNoDisponibles)
                ->count() < 5;
        });

        if ($disponibles->isEmpty()) {
            // Si no hay técnicos disponibles, seleccionar el que tenga menos tickets activos
            return $tecnicos->sortBy(function ($u) use ($estadosNoDisponibles) {
                return $u->ticketsAsignados()
                    ->whereNotIn('estado', $estadosNoDisponibles)
                    ->count();
            })->first();
        } else {
            // Seleccionar el técnico con menor número de tickets activos
            return $disponibles->sortBy(function ($u) use ($estadosNoDisponibles) {
                return $u->ticketsAsignados()
                    ->whereNotIn('estado', $estadosNoDisponibles)
                    ->count();
            })->first();
        }
    }
    public function save()
    {
        $this->validate();

        if (!Auth::check()) {
            session()->flash('error', 'Debes iniciar sesión para crear un ticket.');
            return;
        }

        try {
            // Determinar técnico asignado basado en el tipo de ticket
            $tecnicoAsignado = null;

            if ($this->tipo === 'Requerimiento') {
                // Para requerimientos, asignar automáticamente al Jefe de Administración
                $tecnicoAsignado = User::role('Jefe de Administración')->first();
                if (!$tecnicoAsignado) {
                    // Fallback: buscar cualquier usuario con rol de administración
                    $tecnicoAsignado = User::role(['Administrador', 'Admin'])->first();
                }
            } else {
                // Para otros tipos, asignar técnico disponible como antes
                $tecnicoAsignado = $this->asignarTecnicoDisponible();
            }

            // Preparar datos para crear el ticket
            $ticketData = [
                'titulo' => $this->titulo,
                'descripcion' => $this->descripcion,
                'prioridad' => $this->prioridad,
                'tipo' => $this->tipo,
                'area_id' => $this->area_id,
                'creado_por' => Auth::id(),
                'estado' => 'Abierto',
                'asignado_a' => $tecnicoAsignado ? $tecnicoAsignado->id : null,
                'asignado_por' => null, // null indica asignación automática
            ];

            // Guardar archivos si los hay
            $archivosGuardados = [];
            if (!empty($this->archivos)) {
                foreach ($this->archivos as $archivo) {
                    $nombreArchivo = time() . '_' . $archivo->getClientOriginalName();
                    $rutaArchivo = $archivo->storeAs('tickets', $nombreArchivo, 'public');
                    $archivosGuardados[] = [
                        'nombre' => $archivo->getClientOriginalName(),
                        'ruta' => $rutaArchivo,
                        'tamaño' => $archivo->getSize(),
                        'tipo' => $archivo->getMimeType(),
                    ];
                }
            }

            // Agregar archivos al ticket si existen
            if (!empty($archivosGuardados)) {
                $ticketData['attachment'] = json_encode($archivosGuardados);
            }

            // Crear el ticket
            $ticket = Ticket::create($ticketData);

            // Si es un requerimiento, crear también una solicitud de dispositivo
            if ($this->tipo === 'Requerimiento' && $this->categoria_dispositivo_id) {
                $documentoPath = null;
                if (!empty($archivosGuardados)) {
                    // Usar el primer archivo como documento de respaldo
                    $documentoPath = $archivosGuardados[0]['ruta'];
                }

                $solicitud = SolicitudDispositivo::create([
                    'user_id' => Auth::id(),
                    'categoria_dispositivo_id' => $this->categoria_dispositivo_id,
                    'justificacion' => $this->descripcion,
                    'documento_requerimiento' => $documentoPath,
                    'prioridad' => $this->prioridad,
                    'estado' => 'Pendiente',
                    'fecha_solicitud' => now(),
                    'ticket_id' => $ticket->id, // Vincular con el ticket creado
                    'admin_respuesta_id' => $tecnicoAsignado ? $tecnicoAsignado->id : null, // Auto-asignar al Jefe de Administración
                ]);
            }

            $mensaje = 'Ticket #' . $ticket->id . ' creado exitosamente.';
            if ($this->tipo === 'Requerimiento' && $this->categoria_dispositivo_id) {
                $mensaje .= ' Se ha creado también una solicitud de dispositivo vinculada.';
            }
            if ($tecnicoAsignado) {
                $mensaje .= ' Asignado a: ' . $tecnicoAsignado->name;
            }
            if (!empty($archivosGuardados)) {
                $mensaje .= ' (' . count($archivosGuardados) . ' archivo(s) adjunto(s))';
            }

            session()->flash('success', $mensaje);

            if (!$this->isDashboard) {
                $this->closeModal();
            } else {
                $this->resetForm();
            }

            $this->dispatch('ticket-created');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al crear el ticket: ' . $e->getMessage());
        }
    }

    public function preseleccionarTipo($tipo)
    {
        $this->tipo = $tipo;
    }

    public function render()
    {
        return view('livewire.ticket-create', [
            'areas' => Area::all(),
            'categorias' => CategoriaDispositivo::pluck('nombre', 'id')
        ]);
    }
}
