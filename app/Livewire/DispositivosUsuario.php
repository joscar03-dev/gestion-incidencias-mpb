<?php

namespace App\Livewire;

use App\Models\Dispositivo;
use App\Models\DispositivoAsignacion;
use App\Models\SolicitudDispositivo;
use App\Models\CategoriaDispositivo;
use App\Models\Ticket;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.livewire-welcome')]
class DispositivosUsuario extends Component
{
    use WithPagination, WithFileUploads;

    public $activeTab = 'mis-dispositivos';
    public $showRequerimientoModal = false;
    public $showExitoModal = false;
    public $showReporteModal = false;
    public $ticketCreado;

    // Variables para requerimientos de dispositivos
    public $categoria_solicitada;
    public $justificacion_requerimiento;
    public $documento_requerimiento;
    public $prioridad_requerimiento = 'Media';

    // Variables para reportes de problemas
    public $dispositivo_seleccionado;
    public $tipo_problema;
    public $descripcion_problema;
    public $requiere_reemplazo = false;

    protected $listeners = ['refreshComponent' => '$refresh'];

    public function mount()
    {
        $this->activeTab = 'mis-dispositivos';
    }

    public function sincronizar()
    {
        $this->dispatch('notify', 'Vista actualizada correctamente.');
    }

    public function setActiveTab($tab)
    {
        // Si está saliendo de la pestaña de solicitar dispositivo, limpiar el formulario
        if ($this->activeTab === 'solicitar-dispositivo' && $tab !== 'solicitar-dispositivo') {
            $this->reset(['categoria_solicitada', 'justificacion_requerimiento', 'documento_requerimiento']);
            $this->prioridad_requerimiento = 'Media';
        }

        $this->activeTab = $tab;
        $this->resetPage();
    }

    // Abrir modal para requerimiento de dispositivo
    public function abrirRequerimientoModal()
    {
        $this->reset(['categoria_solicitada', 'justificacion_requerimiento', 'documento_requerimiento', 'prioridad_requerimiento']);
        $this->prioridad_requerimiento = 'Media';
        $this->showRequerimientoModal = true;
    }

    // Cerrar modal para requerimiento de dispositivo
    public function cerrarRequerimientoModal()
    {
        $this->showRequerimientoModal = false;
    }

    // Validar formulario antes de mostrar el modal de confirmación
    public function enviarRequerimiento()
    {
        $this->validate([
            'categoria_solicitada' => 'required|exists:categoria_dispositivos,id',
            'justificacion_requerimiento' => 'required|min:10|max:500',
            'documento_requerimiento' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
            'prioridad_requerimiento' => 'required|in:Alta,Media,Baja,Critica',
        ]);

        // Mostrar modal de confirmación
        $this->showRequerimientoModal = true;
    }

    // Método para confirmar y procesar el envío del requerimiento
    public function confirmarEnvioRequerimiento()
    {
        $documentoPath = null;
        if ($this->documento_requerimiento) {
            $documentoPath = $this->documento_requerimiento->store('requerimientos', 'public');
        }

        // Obtener la categoría seleccionada
        $categoria = CategoriaDispositivo::find($this->categoria_solicitada);

        // Buscar al Jefe de Administración para auto-asignar
        $jefeAdministracion = User::role('Jefe de Administración')->first();
        if (!$jefeAdministracion) {
            // Fallback: buscar administrador
            $jefeAdministracion = User::role(['Administrador', 'Admin'])->first();
        }

        // Crear la solicitud de dispositivo
        $solicitud = SolicitudDispositivo::create([
            'user_id' => Auth::id(),
            'categoria_dispositivo_id' => $this->categoria_solicitada,
            'justificacion' => $this->justificacion_requerimiento,
            'documento_requerimiento' => $documentoPath,
            'prioridad' => $this->prioridad_requerimiento,
            'estado' => 'Pendiente',
            'fecha_solicitud' => now(),
            'admin_respuesta_id' => $jefeAdministracion ? $jefeAdministracion->id : null, // Auto-asignar
        ]);

        // Crear automáticamente un ticket de tipo "Requerimiento"
        $ticket = Ticket::create([
            'area_id' => Auth::user()->area_id,
            'categoria_id' => 1, // Categoría por defecto
            'prioridad' => $this->prioridad_requerimiento,
            'tipo' => 'Requerimiento', // Tipo automático
            'estado' => 'Abierto',
            'titulo' => "Requerimiento de dispositivo: {$categoria->nombre}",
            'descripcion' => $this->justificacion_requerimiento,
            'creado_por' => Auth::id(),
            'asignado_a' => $jefeAdministracion ? $jefeAdministracion->id : null, // Auto-asignar al Jefe de Administración
            'fecha_creacion' => now(),
            'attachment' => $documentoPath, // Adjuntar documento si existe
        ]);

        // Actualizar la solicitud con el ticket_id para establecer la relación bidireccional
        $solicitud->update(['ticket_id' => $ticket->id]);

        // Guarda el ID del ticket creado para mostrarlo en el modal de éxito
        $this->ticketCreado = $ticket->id;

        // Limpia el formulario inmediatamente después de procesar
        $this->limpiarFormularioRequerimiento();

        // Muestra el modal de éxito
        $this->showRequerimientoModal = false;
        $this->showExitoModal = true;
    }

    // Método auxiliar para limpiar el formulario de requerimiento
    private function limpiarFormularioRequerimiento()
    {
        $this->reset([
            'categoria_solicitada',
            'justificacion_requerimiento',
            'documento_requerimiento'
        ]);
        $this->prioridad_requerimiento = 'Media';
    }

    // Método para cerrar el modal de éxito y limpiar el formulario
    public function cerrarExitoModal()
    {
        $this->showExitoModal = false;
        $this->activeTab = 'mis-requerimientos'; // Cambiamos a la pestaña de mis requerimientos para ver el nuevo requerimiento

        // Limpiamos el formulario usando nuestro método auxiliar
        $this->limpiarFormularioRequerimiento();

        // También limpiamos el ID del ticket creado
        $this->reset(['ticketCreado']);
    }

    // Abrir modal para reportar problema
    public function abrirReporteModal($dispositivoId)
    {
        $this->dispositivo_seleccionado = $dispositivoId;
        $this->reset(['tipo_problema', 'descripcion_problema', 'requiere_reemplazo']);
        $this->showReporteModal = true;
    }

    // Reportar problema con dispositivo
    public function reportarProblema()
    {
        $this->validate([
            'dispositivo_seleccionado' => 'required|exists:dispositivos,id',
            'tipo_problema' => 'required|in:Hardware,Software,Conectividad,Rendimiento,Otro',
            'descripcion_problema' => 'required|min:10|max:500',
            'requiere_reemplazo' => 'boolean',
        ]);

        // Asignar un técnico disponible automáticamente
        $tecnico = Ticket::asignarTecnicoAutomaticamente();

        // Crear ticket con asignación automática
        $ticket = Ticket::create([
            'dispositivo_id' => $this->dispositivo_seleccionado,
            'area_id' => Auth::user()->area_id,
            'categoria_id' => 1, // Categoría por defecto para problemas de dispositivos
            'prioridad' => $this->requiere_reemplazo ? 'Alta' : 'Media',
            'estado' => 'Abierto',
            'titulo' => "Problema con dispositivo - {$this->tipo_problema}",
            'descripcion' => $this->descripcion_problema,
            'creado_por' => Auth::id(),
            'asignado_a' => $tecnico ? $tecnico->id : null,
            'asignado_por' => null, // null indica asignación automática
            'fecha_creacion' => now(),
        ]);

        // Si requiere reemplazo, marcar dispositivo en reparación
        if ($this->requiere_reemplazo) {
            $dispositivo = Dispositivo::find($this->dispositivo_seleccionado);
            $dispositivo->update(['estado' => 'Reparación']);
        }

        $this->showReporteModal = false;
        $this->dispatch('notify', "Incidencia reportada correctamente. Ticket #{$ticket->id} creado.");
        $this->reset(['dispositivo_seleccionado', 'tipo_problema', 'descripcion_problema', 'requiere_reemplazo']);
    }

    // Cerrar modal de reporte de problema
    public function cerrarReporteModal()
    {
        $this->showReporteModal = false;
        $this->reset(['dispositivo_seleccionado', 'tipo_problema', 'descripcion_problema', 'requiere_reemplazo']);
    }
    // Enviar reporte de problema (alias de reportarProblema)
    public function enviarReporte()
    {
        return $this->reportarProblema();
    }

    // Confirmar recepción de dispositivo asignado
    public function confirmarRecepcion($asignacionId)
    {
        try {
            $asignacion = DispositivoAsignacion::findOrFail($asignacionId);

            // Verificar que la asignación pertenece al usuario actual
            if ($asignacion->user_id !== Auth::id()) {
                session()->flash('error', 'No tienes permisos para confirmar esta asignación.');
                return;
            }

            // Verificar que la asignación no esté ya confirmada
            if ($asignacion->confirmado) {
                session()->flash('info', 'Esta asignación ya ha sido confirmada.');
                return;
            }

            // Confirmar la recepción usando el método del modelo
            $asignacion->confirmarRecepcion();

            session()->flash('success', '✅ Recepción confirmada exitosamente. Gracias por confirmar que has recibido el dispositivo.');

        } catch (\Exception $e) {
            session()->flash('error', 'Error al confirmar la recepción: ' . $e->getMessage());
        }
    }

    // Crear ticket de requerimiento general
    public function crearTicketRequerimiento()
    {
        // Emitir evento para que el Dashboard cambie a la vista de crear ticket
        // con el tipo "Requerimiento" preseleccionado
        $this->dispatch('changeView', 'create');

        // También emitir evento específico para preseleccionar el tipo
        $this->dispatch('preseleccionar-tipo', 'Requerimiento');

        // Mostrar mensaje informativo
        session()->flash('info', 'Será redirigido al formulario de creación de tickets con el tipo "Requerimiento" preseleccionado.');
    }

    // Obtener dispositivos asignados al usuario
    public function getMisDispositivosProperty()
    {
        // Obtenemos los IDs de dispositivos con asignaciones activas para el usuario actual
        $asignacionesActivasIds = \App\Models\DispositivoAsignacion::where('user_id', Auth::id())
            ->whereNull('fecha_desasignacion')
            ->pluck('dispositivo_id')
            ->toArray();

        // Consultamos los dispositivos que están asignados al usuario según esas asignaciones activas
        return Dispositivo::whereIn('id', $asignacionesActivasIds)
            ->with(['categoria_dispositivo', 'area'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    // Obtener requerimientos del usuario
    public function getMisRequerimientosProperty()
    {
        return SolicitudDispositivo::where('user_id', Auth::id())
            ->with(['categoria_dispositivo'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    // Obtener tickets relacionados con dispositivos del usuario
    public function getMisTicketsDispositivosProperty()
    {
        return Ticket::where('creado_por', Auth::id())
            ->whereNotNull('dispositivo_id')
            ->with(['dispositivo'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    // Obtener historial de asignaciones
    public function getHistorialAsignacionesProperty()
    {
        return DispositivoAsignacion::where('user_id', Auth::id())
            ->with(['dispositivo.categoria_dispositivo'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    // Obtener categorías disponibles
    public function getCategoriasProperty()
    {
        return CategoriaDispositivo::pluck('nombre', 'id');
    }

    // Obtener asignaciones pendientes de confirmación
    public function getAsignacionesPendientesProperty()
    {
        return DispositivoAsignacion::where('user_id', Auth::id())
            ->pendientesConfirmacion()
            ->with(['dispositivo', 'dispositivo.categoria_dispositivo'])
            ->orderBy('fecha_asignacion', 'desc')
            ->get();
    }

    public function render()
    {
        return view('livewire.dispositivos-usuario', [
            'misDispositivos' => $this->misDispositivos,
            'misRequerimientos' => $this->misRequerimientos,
            'misTicketsDispositivos' => $this->misTicketsDispositivos,
            'historialAsignaciones' => $this->historialAsignaciones,
            'asignacionesPendientes' => $this->asignacionesPendientes,
            'categorias' => $this->categorias,
        ]);
    }
}
