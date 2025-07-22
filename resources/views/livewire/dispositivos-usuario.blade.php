@php
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;
@endphp

<div class="py-5">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Page Title -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                Gestión de Dispositivos
            </h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                Administra tus dispositivos, solicita nuevos equipos y reporta problemas.
            </p>
        </div>

        <!-- Mensajes de feedback -->
        @if (session()->has('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4" role="alert">
                <div class="flex">
                    <div class="py-1">
                        <svg class="fill-current h-6 w-6 text-green-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-bold">¡Éxito!</p>
                        <p class="text-sm">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4" role="alert">
                <div class="flex">
                    <div class="py-1">
                        <svg class="fill-current h-6 w-6 text-red-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-bold">Error</p>
                        <p class="text-sm">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if (session()->has('info'))
            <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-4" role="alert">
                <div class="flex">
                    <div class="py-1">
                        <svg class="fill-current h-6 w-6 text-blue-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-bold">Información</p>
                        <p class="text-sm">{{ session('info') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="space-y-6">
            <!-- Header con navegación por pestañas -->
            <div class="bg-white rounded-lg shadow">
                <div class="border-b border-gray-200">
                    <!-- Navegación desktop -->
                    <nav class="hidden md:flex px-6">
                        <button
                            wire:click="setActiveTab('mis-dispositivos')"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm mr-8 {{ $activeTab === 'mis-dispositivos' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}"
                        >
                            Mis Dispositivos ({{ $misDispositivos->count() ?? 0 }})
                        </button>
                        <button
                            wire:click="setActiveTab('solicitar-dispositivo')"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm mr-8 {{ $activeTab === 'solicitar-dispositivo' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}"
                        >
                            Solicitar Requerimiento
                        </button>
                        <button
                            wire:click="setActiveTab('mis-requerimientos')"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm mr-8 {{ $activeTab === 'mis-requerimientos' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}"
                        >
                            Mis Requerimientos
                        </button>
                        <button
                            wire:click="setActiveTab('historial')"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'historial' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}"
                        >
                            Historial
                        </button>
                    </nav>

                    <!-- Navegación móvil - Select Dropdown -->
                    <div class="md:hidden px-4 py-3">
                        <div class="relative">
                            <select
                                wire:model.live="activeTab"
                                class="w-full appearance-none bg-white border border-gray-300 rounded-lg px-4 py-3 pr-8 text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                            >
                                <option value="mis-dispositivos">📱 Mis Dispositivos ({{ $misDispositivos->count() ?? 0 }})</option>
                                <option value="solicitar-dispositivo">➕ Solicitar Requerimiento</option>
                                <option value="mis-requerimientos">📋 Mis Requerimientos</option>
                                <option value="historial">🕐 Historial</option>
                            </select>
                            <!-- Icono de flecha personalizado -->
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contenido según pestaña activa -->
            @if($activeTab === 'mis-dispositivos')
                <!-- Mis Dispositivos -->
                <div class="space-y-4">
                    @if($misDispositivos && $misDispositivos->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($misDispositivos as $dispositivo)
                                <div class="bg-white rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden">
                                    <!-- Imagen del dispositivo -->
                                    <div class="h-48 bg-gray-50 relative overflow-hidden">
                                        @if($dispositivo->imagen)
                                            <img
                                                src="{{ asset('storage/' . $dispositivo->imagen) }}"
                                                alt="{{ $dispositivo->nombre }}"
                                                class="w-full h-full object-cover"
                                            >
                                        @else
                                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100">
                                                <div class="text-center">
                                                    <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                                    </svg>
                                                    <p class="mt-2 text-sm text-gray-500">Sin imagen</p>
                                                </div>
                                            </div>
                                        @endif

                                        <!-- Badge de estado superpuesto -->
                                        <div class="absolute top-3 right-3">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium shadow-sm
                                                {{ $dispositivo->estado === 'Asignado' ? 'bg-green-100 text-green-800 border border-green-200' :
                                                   ($dispositivo->estado === 'Reparación' ? 'bg-red-100 text-red-800 border border-red-200' : 'bg-gray-100 text-gray-800 border border-gray-200') }}">
                                                {{ $dispositivo->estado }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Contenido de la tarjeta -->
                                    <div class="p-6">
                                        <div class="flex items-start justify-between mb-3">
                                            <div class="flex-1">
                                                <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ $dispositivo->nombre }}</h3>
                                                <p class="text-sm text-indigo-600 font-medium">{{ $dispositivo->categoria_dispositivo->nombre ?? 'Sin categoría' }}</p>
                                                <p class="text-xs text-gray-500 mt-1 font-mono">S/N: {{ $dispositivo->numero_serie }}</p>
                                            </div>
                                        </div>

                                        @if($dispositivo->descripcion)
                                            <p class="text-sm text-gray-600 mb-4 leading-relaxed">{{ Str::limit($dispositivo->descripcion, 100) }}</p>
                                        @endif

                                        <!-- Información adicional -->
                                        <div class="space-y-2 mb-4">
                                            <div class="flex items-center text-xs text-gray-500">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                                </svg>
                                                <span>{{ $dispositivo->area->nombre ?? 'Sin área asignada' }}</span>
                                            </div>
                                            @if($dispositivo->fecha_compra)
                                                <div class="flex items-center text-xs text-gray-500">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>
                                                    <span>Compra: {{ \Carbon\Carbon::parse($dispositivo->fecha_compra)->format('d/m/Y') }}</span>
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Acciones -->
                                        <div class="flex justify-end">
                                            @if($dispositivo->estado !== 'Reparación')
                                                <button
                                                    wire:click="abrirReporteModal({{ $dispositivo->id }})"
                                                    class="inline-flex items-center px-3 py-1.5 border border-red-300 shadow-sm text-xs font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200"
                                                    title="Reportar problema"
                                                >
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.664-.833-2.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                                    </svg>
                                                    Reportar Problema
                                                </button>
                                            @else
                                                <span class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-orange-700 bg-orange-100 rounded-md">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    </svg>
                                                    En Reparación
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="bg-white rounded-lg shadow p-8 text-center">
                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No tienes dispositivos asignados</h3>
                            <p class="text-gray-500 mb-4">Solicita un dispositivo haciendo clic en la pestaña "Solicitar Requerimiento"</p>
                            <button
                                wire:click="setActiveTab('solicitar-dispositivo')"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700"
                            >
                                Solicitar Dispositivo
                            </button>
                        </div>
                    @endif
                </div>

            @elseif($activeTab === 'solicitar-dispositivo')
                <!-- Solicitar Requerimiento -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Solicitar Dispositivo</h3>
                        <p class="text-sm text-gray-600">Complete el formulario para solicitar un dispositivo. Su requerimiento será revisado por el administrador.</p>
                    </div>

                    <form wire:submit="enviarRequerimiento" class="space-y-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                            <div>
                                <label for="categoria_solicitada" class="block text-sm font-medium text-gray-700 mb-2">
                                    Categoría de Dispositivo *
                                </label>
                                <select
                                    wire:model.live="categoria_solicitada"
                                    id="categoria_solicitada"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                >
                                    <option value="">Seleccione una categoría</option>
                                    @if(isset($categorias))
                                        @foreach($categorias as $id => $nombre)
                                            <option value="{{ $id }}">{{ $nombre }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('categoria_solicitada') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="prioridad_requerimiento" class="block text-sm font-medium text-gray-700 mb-2">
                                    Prioridad *
                                </label>
                                <select
                                    wire:model.live="prioridad_requerimiento"
                                    id="prioridad_requerimiento"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                >
                                    <option value="Baja">Baja</option>
                                    <option value="Media">Media</option>
                                    <option value="Alta">Alta</option>
                                    <option value="Critica">Crítica</option>
                                </select>
                                @error('prioridad_requerimiento') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div>
                            <label for="justificacion_requerimiento" class="block text-sm font-medium text-gray-700 mb-2">
                                Justificación del Requerimiento *
                            </label>
                            <textarea
                                wire:model.live="justificacion_requerimiento"
                                id="justificacion_requerimiento"
                                rows="4"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                placeholder="Explique por qué necesita este dispositivo y cómo lo utilizará..."
                            ></textarea>
                            @error('justificacion_requerimiento') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex flex-col sm:flex-row justify-end gap-3">
                            <button
                                type="submit"
                                class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                </svg>
                                Enviar Solicitud
                            </button>
                        </div>
                    </form>
                </div>

            @elseif($activeTab === 'mis-requerimientos')
                <!-- Mis Requerimientos -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Mis Requerimientos</h3>
                    @if(isset($misRequerimientos) && $misRequerimientos->count() > 0)
                        <div class="space-y-4">
                            @foreach($misRequerimientos as $requerimiento)
                                <div class="border rounded-lg p-4">
                                    <div class="flex justify-between items-start mb-2">
                                        <h4 class="font-medium">{{ $requerimiento->categoria_dispositivo->nombre ?? 'Categoría no disponible' }}</h4>
                                        <div class="flex space-x-2">
                                            <span class="px-2 py-1 text-xs rounded-full
                                                {{ $requerimiento->prioridad === 'Alta' ? 'bg-red-100 text-red-800' :
                                                   ($requerimiento->prioridad === 'Media' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                                {{ $requerimiento->prioridad }}
                                            </span>
                                            <span class="px-2 py-1 text-xs rounded-full
                                                {{ $requerimiento->estado === 'Aprobado' ? 'bg-green-100 text-green-800' :
                                                   ($requerimiento->estado === 'Rechazado' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                                {{ $requerimiento->estado }}
                                            </span>
                                        </div>
                                    </div>
                                    <p class="text-sm text-gray-600 mb-2">{{ $requerimiento->justificacion }}</p>
                                    <p class="text-xs text-gray-500">Solicitado el: {{ $requerimiento->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="text-gray-500">No tienes requerimientos registrados</p>
                        </div>
                    @endif
                </div>

            @else
                <!-- Historial -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Historial de Asignaciones</h3>
                    @if(isset($historialAsignaciones) && $historialAsignaciones->count() > 0)
                        <div class="space-y-4">
                            @foreach($historialAsignaciones as $asignacion)
                                <div class="border rounded-lg p-4">
                                    <div class="flex justify-between items-start mb-2">
                                        <h4 class="font-medium">{{ $asignacion->dispositivo->nombre ?? 'Dispositivo no disponible' }}</h4>
                                        <span class="text-xs text-gray-500">
                                            {{ $asignacion->fecha_asignacion ? \Carbon\Carbon::parse($asignacion->fecha_asignacion)->format('d/m/Y') : 'Fecha no disponible' }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-600">{{ $asignacion->dispositivo->categoria_dispositivo->nombre ?? 'Sin categoría' }}</p>
                                    @if($asignacion->fecha_desasignacion)
                                        <p class="text-xs text-red-600">Desasignado el: {{ \Carbon\Carbon::parse($asignacion->fecha_desasignacion)->format('d/m/Y') }}</p>
                                    @else
                                        <p class="text-xs text-green-600">Actualmente asignado</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-gray-500">No hay historial de asignaciones</p>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Modal de Confirmación del Requerimiento -->
            @if($showRequerimientoModal)
                <div class="fixed z-50 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                <div class="sm:flex sm:items-start">
                                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                                        <svg class="h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                            Confirmar envío de solicitud
                                        </h3>
                                        <div class="mt-2">
                                            <p class="text-sm text-gray-500">
                                                ¿Estás seguro de que deseas enviar esta solicitud de dispositivo?
                                            </p>
                                            @if(isset($categoria_solicitada) && isset($prioridad_requerimiento))
                                            <div class="mt-4 bg-gray-50 p-3 rounded-lg">
                                                <p class="text-sm font-medium text-gray-700">Resumen de la solicitud:</p>
                                                <ul class="mt-2 text-sm text-gray-600 space-y-1">
                                                    <li><span class="font-medium">Categoría:</span> {{ $categorias[$categoria_solicitada] ?? 'No seleccionada' }}</li>
                                                    <li><span class="font-medium">Prioridad:</span> {{ $prioridad_requerimiento }}</li>
                                                    <li><span class="font-medium">Justificación:</span> {{ Str::limit($justificacion_requerimiento, 100) }}</li>
                                                </ul>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                <button
                                    wire:click="confirmarEnvioRequerimiento"
                                    type="button"
                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm"
                                >
                                    Enviar Solicitud
                                </button>
                                <button
                                    wire:click="cerrarRequerimientoModal"
                                    type="button"
                                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                                >
                                    Cancelar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Modal de Reporte -->
            @if(isset($showReporteModal) && $showReporteModal)
                <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
                    <div class="relative top-4 sm:top-20 mx-auto p-4 sm:p-5 border w-11/12 sm:w-10/12 md:w-1/2 shadow-lg rounded-md bg-white max-w-md sm:max-w-lg">
                        <div class="mt-3">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Reportar Problema</h3>
                            <form wire:submit="enviarReporte" class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Tipo de Problema
                                    </label>
                                    <select wire:model="tipo_problema" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                        <option value="">Seleccione el tipo de problema</option>
                                        <option value="Hardware">Hardware</option>
                                        <option value="Software">Software</option>
                                        <option value="Conectividad">Conectividad</option>
                                        <option value="Rendimiento">Rendimiento</option>
                                        <option value="Otro">Otro</option>
                                    </select>
                                    @error('tipo_problema') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Descripción del Problema
                                    </label>
                                    <textarea
                                        wire:model="descripcion_problema"
                                        rows="4"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                        placeholder="Describe detalladamente el problema..."></textarea>
                                    @error('descripcion_problema') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4">
                                    <button type="button" wire:click="cerrarReporteModal"
                                            class="w-full sm:w-auto px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 justify-center inline-flex items-center">
                                        Cancelar
                                    </button>
                                    <button type="submit"
                                            class="w-full sm:w-auto px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 justify-center inline-flex items-center">
                                        Reportar Problema
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Modal de Éxito -->
            @if($showExitoModal)
                <div class="fixed z-50 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                <div class="sm:flex sm:items-start">
                                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                                        <svg class="h-6 w-6 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                            ¡Requerimiento Enviado Exitosamente!
                                        </h3>
                                        <div class="mt-2">
                                            <p class="text-sm text-gray-500">
                                                Tu solicitud ha sido registrada correctamente.
                                                @if($ticketCreado)
                                                    Se ha generado el ticket #{{ $ticketCreado }} que será revisado por el administrador.
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                <button
                                    wire:click="cerrarExitoModal"
                                    type="button"
                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm"
                                >
                                    OK
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>
